<?php
require_once '../../../bootstrap/app.php';
load('mongodb', 'authorizer', 'doc_info', 'date_utils');

$statusType = 200;  # Status type 200: OK/success (baseline)

# Check for document validity by checking the id parameter
$id = $_GET['id'] ?? null;
$oidValid = valid_oid(oid($id));
if ($oidValid === false) $statusType = 400; # Status type 400: Bad request      
# POSTCONDITIONS: id is a valid ObjectId format

# Attempt to find the document in the database if ObjectId is valid
if ($statusType === 200) {
    $result = QueryRunner::tryWithCollections([
        ($docs='documents') => fn ($docs)=> $docs->findOne(['_id' => oid($id)])->execute()
    ])->getResults($docs);
    if (empty($result)) $statusType = 404;  # Status type 404: Not found

    # Continue processing the document if found
    if ($statusType === 200) {
        # Get the mode from query string, falling back to 'view' if not found
        $mode = $_GET['mode'] ?? 'view';
        if (!is_string($mode)) $mode = 'view';

        # Normalize the mode
        $mode = strtolower(trim($mode));
        # POSTCONDITIONS: Mode is a lowercase string and contains no leading or trailing whitespaces

        # Coerce invalid modes into 'view' by default
        if (!in_array($mode, ['view', 'edit'], true)) $mode = 'view';
        # POSTCONDITIONS: Mode is either 'view' or 'edit', not anything else

        # Identify and normalize the document's current information
        ##  1. Document Title (Standard Textual)
        $docTitle = $result['doc_title'] ?? 'Untitled';
        if (!is_string($docTitle)) $docTitle = 'Untitled';
        if (trim($docTitle) === '') $docTitle = 'Untitled';
        # POSTCONDITIONS: docTitle is a non-empty string

        ##  2. Document Category (Categorical)
        $docCategory = $result['doc_category'] ?? 'UNKNOWN';
        $docCategory = is_string($docCategory)
            ? strtoupper(trim($docCategory))
            : 'UNKNOWN';
        if (!isset(DocInfo::VALID_CATEGORIES[$docCategory])) $docCategory = 'UNKNOWN';
        # POSTCONDITIONS: docCategory is a non-empty string

        ##  3. Document Status (Categorical)
        $docStatus = $result['doc_status'] ?? 'UNKNOWN';       # Get the doc status (string), or 'UNKNOWN' if not set/not found
        $docStatus = is_string($docStatus)
            ? strtoupper(trim($docStatus))
            : 'UNKNOWN';
        if (!isset(DocInfo::VALID_STATUSES[$docStatus])) $docStatus = 'UNKNOWN';
        # POSTCONDITIONS: docStatus is a valid and normalized string

        ##  4. Document Author (ObjectId or null)
        $docAuthor = $result['author'] ?? null;                 # Get the doc author ID (stringified ObjectId), or null if not set/not found
        $docAuthor = is_string($docAuthor)
            ? oid(trim($docAuthor))
            : null;
        # POSTCONDITIONS: docAuthor is either a valid ObjectId or null

        ##  5. Document Area of Origin (Standard Textual or null)
        $docAreaOfOrigin = $result['area_of_origin'] ?? null;   # Get the doc area of origin (string) or null if not set/not found
        $docAreaOfOrigin = is_string($docAreaOfOrigin)
            ? strtolower(trim($docAreaOfOrigin))
            : null;
        # POSTCONDITIONS: docAreaOfOrigin is either a valid and normalized string, or null

        ##  6. Document Tracking Code (Standard Textual or null)
        $docTrackingCode = $result['tracking_code'] ?? null;    # Get the doc tracking code (string) or null if not set/found
        $docTrackingCode = is_string($docTrackingCode)
            ? trim($docTrackingCode)
            : null;
        # POSTCONDITIONS: docTrackingCode is either a string with no leading/trailing whitespaces, or null

        ##  7. Document Description (Standard Textual)
        $docDescription = $result['doc_description'] ?? '(no description)'; # Get the doc description (string) or '(no description)' if not set/found
        if (!is_string($docDescription)) $docDescription = '(no description)';
        if (trim($docDescription) === '') $docDescription = '(no description)';
        # POSTCONDITIONS: docDescription is a non-empty string

        ##  8. Document Current Version (Integer or null)
        $docCurrentVersion = $result['current_version'] ?? null;
        if (!is_int($docCurrentVersion)) $docCurrentVersion = -1;
        if ($docCurrentVersion < 1) $docCurrentVersion = null;
        # POSTCONDITIONS: docCurrentVersion is either an int >= 1 or null

        ##  9. Document dates (Associative Array)
        $docDates = $result['dates'] ?? [
            'date_added' => null,
            'date_edited' => null,
            'date_archived' => null,
            'date_publicized' => null
        ];
        if (!is_array($docDates)) $docDates = [
            'date_added' => null,
            'date_edited' => null,
            'date_archived' => null,
            'date_publicized' => null
        ];
        $dateAdded = 
            (isset($docDates['date_added']) &&
            DateUtils::validateISODateFormat($docDates['date_added']) === true)
                ? $docDates['date_added']
                : null;
        $dateEdited = 
            (isset($docDates['date_edited']) &&
            DateUtils::validateISODateFormat($docDates['date_edited']) === true)
                ? $docDates['date_edited']
                : null;
        $dateArchived = 
            (isset($docDates['date_archived']) &&
            DateUtils::validateISODateFormat($docDates['date_archived']) === true)
                ? $docDates['date_archived']
                : null;
        $datePublicized =
            (isset($docDates['date_publicized']) &&
            DateUtils::validateISODateFormat($docDates['date_publicized']) === true)
                ? $docDates['date_publicized']
                : null;
        $docDates = [
            'date_added' => $dateAdded,
            'date_edited' => $dateEdited,
            'date_archived' => $dateArchived,
            'date_publicized' => $datePublicized
        ];
        # POSTCONDITIONS: docDates is an associative array of either ISO 8601 timestamp format-compatible strings or null

        ##  10. Document Password Protection (Boolean or null)
        $docPasswordProtected = isset($result['view_password_hash']);       # Determine if the view_password_hash field exists at all
        if (
            $docPasswordProtected === true &&                               # If field exists...
            !is_string($result['view_password_hash'])                       # ...but is not a string...
        ) $docPasswordProtected = null;                                     # Null for representing invalid/corrupt states
        # POSTCONDITIONS: Document view password field either exists and is a string, or does not exist
        
        # Only evaluate this if the field exists, granted docPasswordProtected is non-null from previous step which suggest it is a string
        if ($docPasswordProtected === true) {
            $pwInfo = password_get_info($result['view_password_hash']);
            $algoName = $pwInfo['algoName'];
            if ($algoName !== 'argon2id') $docPasswordProtected = null;     # If algorithm is strictly not argon2id (e.g. invalid format), declare invalid/corrupt
        }
        # POSTCONDITIONS: The view password's algorithm is strictly and correctly argon2id

        # Check and validate the above fetched data one by one
        if (!isset(DocInfo::VALID_STATUSES[$docStatus])) $statusType = 500;
        if (is_null($docAuthor)) $statusType = 500;
        if (is_null($docAreaOfOrigin)) $statusType = 500;
        if (is_null($docTrackingCode)) $statusType = 500;
        if (is_null($docPasswordProtected)) $statusType = 500;
        if (is_null($docCurrentVersion)) $statusType = 500;
        if (is_null($docPasswordProtected)) $statusType = 500;
        # POSTCONDITIONS: The above data are all valid

        # Continue deciding if status type is still OK
        if ($statusType === 200) switch ($docStatus) {
            default: $statusType = match ($mode) {
                default => 400,
                'view' => 
                    (Authorizer::isOSCPresident($_CURRENTUSER) || 
                    Authorizer::can($_CURRENTUSER, [
                        'scopes' => ['view_docs'],
                        'domains' => [$docAreaOfOrigin]
                    ]))
                        ? 200
                        : 403,
                'edit' => 
                    ((Authorizer::isOSCPresident($_CURRENTUSER) && $docAreaOfOrigin === 'osc_president_office') ||
                    (!Authorizer::isOSCPresident($_CURRENTUSER) && Authorizer::can($_CURRENTUSER, [
                        'scopes' => ['edit_docs'],
                        'domains' => [$docAreaOfOrigin]
                    ])))
                        ? 200
                        : 403
            }; break;
            case 'ARCHIVED': $statusType = match ($mode) {
                default => 400,
                'view' => (Authorizer::isAdmin($_CURRENTUSER) || Authorizer::isEditor($_CURRENTUSER)) 
                    ? 200 
                    : 403,
                'edit' => 403
            }; break;
            case 'PUBLICIZED': $statusType = match ($mode) {
                default => 400,
                'view' => 200,
                'edit' => 403
            }; break;
        }
    }
    # ...
}
# POSTCONDITIONS: Every checks passed and the document is cleared for requested interaction
?>

<!-- BEGIN PAGE CONTENT -->
<!DOCTYPE html>
<html>
    <head>
        <?php 
            $title = match ($statusType) {
                200 => $docTitle,
                400 => 'Invalid Request',
                401 => 'Unauthorized',
                403 => 'Forbidden Access',
                404 => 'Not Found',
                500 => 'Error'
            };
            initialize_page("$title | YanoDASH");
        ?>
    </head>
</html>
<!-- END PAGE CONTENT -->