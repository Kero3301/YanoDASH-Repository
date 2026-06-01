<?php
// doc_query.php - Shannon: This file provides TOOLS only.
require dirname(__DIR__). '/database/mongodb.php';
require dirname(__DIR__). '/iam/IAMContextValidator.php';
require dirname(__DIR__). '/iam/Authorizer.php';

final class DocQuery {
    public static function buildQuery($user, $orgOnly = false, $pageType = 'dms'): ?array {
        # Ensure user's IAM context is valid
        if (!IAMContextValidator::validate($user)) return null;

        # Query initialization
        $query = null;

        # Valid document statuses per page type
        $dmsValidStatus = ['EDITING', 'PENDING ARCHIVAL'];
        $privArchValidStatus = ['ARCHIVED'];
        $pubArchValidStatus = ['PUBLICIZED'];

        # President override (cross-department access)
        if (Authorizer::isPresident($user)) switch ($pageType) {
            case 'dms': return $query = ($orgOnly === true)
                ? [ 'org_of_origin' => oid($user['IDENTITY']['org_id']),
                    'doc_status' => ['$in' => $dmsValidStatus] ]
                : [ 'doc_status' => ['$in' => $dmsValidStatus] ];
            case 'private': return $query = ($orgOnly === true)
                ? [ 'org_of_origin' => oid($user['IDENTITY']['org_id']),
                    'doc_status' => ['$in' => $privArchValidStatus] ]
                : [ 'doc_status' => ['$in' => $privArchValidStatus] ];
            case 'public': return $query = ($orgOnly === true)
                ? [ 'org_of_origin' => oid($user['IDENTITY']['org_id']),
                    'doc_status' => ['$in' => $pubArchValidStatus] ]
                : [ 'doc_status' => ['$in' => $pubArchValidStatus] ];
            default: return $query = ($orgOnly === true)
                ? [ 'org_of_origin' => oid($user['IDENTITY']['org_id']) ]
                : [ ]; } 
        # Non-president instances (departmental-only access)
        else switch ($pageType) {
            case 'dms':
            case 'private':
            case 'public':
            default:
        }

        $userOrg = valid_oid(oid($user['IDENTITY']['org_id']))
            ? oid($user['IDENTITY']['org_id'])
            : null;
        $scopes = $user['PERMISSIONS']['access_scope'];


        if ($orgOnly === true && isset($userOrg)) {
            
        } else {

        }
        
    }
}

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

require __DIR__ . '/../../vendor/autoload.php';

use MongoDB\Client;
use MongoDB\BSON\ObjectId;

if (file_exists(__DIR__ . '/../.env')) {
    $dotenv = Dotenv\Dotenv::createImmutable(__DIR__ . '/../');
    $dotenv->load();
}

$mongoUri = getenv('YANODASH_V_DBU_URI');

if (!$mongoUri) {
    die("Missing environment variable: YANODASH_V_DBU_URI");
}

try {
    $client = new Client($mongoUri);
    $db = $client->yano_dash;
} catch (Exception $e) {
    die("Database connection failed: " . $e->getMessage());
}

function buildQuery($user, $access, $pageType = 'dms') {
    $scopes = isset($access['scope_of_access']) ? (array)$access['scope_of_access'] : [];
    
    $userOrg = $user['organization'] ?? null;
    $orgString = ($userOrg instanceof ObjectId) ? (string)$userOrg : $userOrg;

    if (!in_array('read_docs', $scopes)) {
        return ['_id' => 'DENIED']; 
    }

    switch ($pageType) {
        case 'private':
            return [
                'is_publicized' => false,
                'doc_status' => ['$regex' => '^archived$', '$options' => 'i'],
                'area_of_origin' => $orgString
            ];

        case 'public':
            return ['is_publicized' => true];

        case 'dms':
        default:
            return ['area_of_origin' => $orgString];
    }
}