<?php
// doc_query.php - Shannon: This file provides TOOLS only.
require_once dirname(__DIR__). '/database/mongodb.php';
require_once dirname(__DIR__). '/presentation/Mapper.php';
require_once dirname(__DIR__). '/iam/IAMContextValidator.php';
require_once dirname(__DIR__). '/iam/Authorizer.php';
require_once dirname(__DIR__). '/models/DocEd.php';

final class DocQuery {
    private const DMS_VALID_STATUS = ['EDITING', 'PENDING_ARCHIVAL', 'PENDING_AUDIT', 'PENDING_APPROVAL'];
    private const PRIVATE_ARCHIVE_VALID_STATUS = ['ARCHIVED'];
    private const PUBLIC_ARCHIVE_VALID_STATUS = ['PUBLICIZED'];
    private const PAGE_TYPES = ['dms', 'private', 'public'];

    public static function buildQuery($user, $orgOnly = false, $pageType = 'public'): ?array {
        # For Guests or invalid users (ignore all other parameters)
        if (!Authorizer::validateIAM($user)) return ['doc_status' => ['$in' => self::PUBLIC_ARCHIVE_VALID_STATUS]];
        # POSTCONDITIONS: User is non-guest with valid IDENTITY

        # Normalize page type
        $pageType = strtolower(trim($pageType));
        # POSTCONDITIONS: pageType is normalized and ready to use

        # Return null if page type does not correspond to any of the 3 recognized page types
        if (!in_array($pageType, self::PAGE_TYPES, true)) return null;
        # POSTCONDITIONS: pageType is any of these three: 'dms', 'private', 'public'

        # Determine if fit for org-only query
        $showOrgOnly = 
            $orgOnly === true &&
            valid_oid(oid($user['IDENTITY']['org_id']));
        
        # Decide based on whether the user is a viewer
        $isViewer = Authorizer::isViewer($user);
        # Viewer case
        if ($isViewer === true) return $showOrgOnly === true 
            ? ['doc_status' => ['$in' => self::PUBLIC_ARCHIVE_VALID_STATUS], 'org_of_origin' => oid($user['IDENTITY']['org_id'])]
            : ['doc_status' => ['$in' => self::PUBLIC_ARCHIVE_VALID_STATUS]];
        # Non-Viewer case
        else {
            # Determine whether user is an admin or editor
            $isAdmin = Authorizer::isAdmin($user);
            $isEditor = Authorizer::isEditor($user);
            
            # For admin
            if ($isAdmin === true) return 
                # For president
                Authorizer::isOSCPresident($user)       
                    ? match ($showOrgOnly) {            # President
                        true => match ($pageType) {     # Org-only
                            'dms' => [      
                                'doc_status' => ['$in' => self::DMS_VALID_STATUS],
                                'org_of_origin' => oid($user['IDENTITY']['org_id'])
                            ],
                            'private' => [ 
                                'doc_status' => ['$in' => self::PRIVATE_ARCHIVE_VALID_STATUS],
                                'org_of_origin' => oid($user['IDENTITY']['org_id'])
                            ],
                            'public' => [   
                                'doc_status' => ['$in' => self::PUBLIC_ARCHIVE_VALID_STATUS],
                                'org_of_origin' => oid($user['IDENTITY']['org_id'])
                            ]
                        },  
                        false => match ($pageType) {    # Not org-only
                            'dms' => ['doc_status' => ['$in' => self::DMS_VALID_STATUS]],
                            'private' => ['doc_status' => ['$in' => self::PRIVATE_ARCHIVE_VALID_STATUS]],
                            'public' => ['doc_status' => ['$in' => self::PUBLIC_ARCHIVE_VALID_STATUS]]
                        }
                    } 
                    : match ($showOrgOnly) {            # Non-president (departmental only access for DMS case)
                        true => match ($pageType) {     # Org-only
                            'dms' => [
                                'doc_status' => ['$in' => self::DMS_VALID_STATUS],
                                'org_of_origin' => oid($user['IDENTITY']['org_id']),
                                'area_of_origin' => $user['IDENTITY']['department']
                            ],
                            'private' => [
                                'doc_status' => ['$in' => self::PRIVATE_ARCHIVE_VALID_STATUS],
                                'org_of_origin' => oid($user['IDENTITY']['org_id'])
                            ],
                            'public' => [
                                'doc_status' => ['$in' => self::PUBLIC_ARCHIVE_VALID_STATUS],
                                'org_of_origin' => oid($user['IDENTITY']['org_id'])
                            ]
                        },
                        false => match ($pageType) {    # Not org-only
                            'dms' => [
                                'doc_status' => ['$in' => self::DMS_VALID_STATUS],
                                'area_of_origin' => $user['IDENTITY']['department']
                            ],
                            'private' => ['doc_status' => ['$in' => self::PRIVATE_ARCHIVE_VALID_STATUS]],
                            'public' => ['doc_status' => ['$in' => self::PUBLIC_ARCHIVE_VALID_STATUS]]
                        }
                    };

            # For editor (also department-only access for DMS)
            if ($isEditor === true) return 
                match ($showOrgOnly) {
                    true => match ($pageType) {
                        'dms' => [
                            'doc_status' => ['$in' => self::DMS_VALID_STATUS],
                            'org_of_origin' => oid($user['IDENTITY']['org_id']),
                            'area_of_origin' => $user['IDENTITY']['department']
                        ],
                        'private' => [
                            'doc_status' => ['$in' => self::PRIVATE_ARCHIVE_VALID_STATUS],
                            'org_of_origin' => oid($user['IDENTITY']['org_id'])
                        ],
                        'public' => [
                            'doc_status' => ['$in' => self::PUBLIC_ARCHIVE_VALID_STATUS],
                            'org_of_origin' => oid($user['IDENTITY']['org_id'])
                        ]
                    },
                    false => match ($pageType) {
                        'dms' => [
                            'doc_status' => ['$in' => self::DMS_VALID_STATUS],
                            'area_of_origin' => $user['IDENTITY']['department']
                        ],
                        'private' => ['doc_status' => ['$in' => self::PRIVATE_ARCHIVE_VALID_STATUS]],
                        'public' => ['doc_status' => ['$in' => self::PUBLIC_ARCHIVE_VALID_STATUS]]
                    }
                };

            # Default fallback
            return null;
        }        
    }

    public static function get($queryStatement): array {
        if (!is_callable($queryStatement)) return [];

        $results = QueryRunner::tryWithCollections([($docs='documents') => $queryStatement])->getResults($docs);
        if (!is_array($results)) return [];
        if (empty($results)) return [];

        $uniqueAuthorIDs = $authorMappings = [];
        foreach ($results as $r) {
            $id = (string) $r['author'];
            if (!in_array($id, $uniqueAuthorIDs)) array_push($uniqueAuthorIDs, oid($id));
        }
     
        $authors = QueryRunner::tryWithCollections([($acc='accounts')
            => fn ($acc)=> $acc->find(['_id' => ['$in' => $uniqueAuthorIDs]])->execute()
        ])->getResults($acc);
        if (!empty($authors)) foreach ($authors as $a) {
            $authorID = $a['_id'];
            $firstName = $a['name']['first_name'];
            $lastName = $a['name']['last_name'];
            $authorName = empty($firstName) && empty($lastName)
                ? "(unknown)" 
                : "$firstName $lastName";
            $authorMappings[$authorID] = $authorName;
        }

        $finalDocs = [];
        foreach ($results as $r) array_push(
            $finalDocs, new Document(
                _id: $r['_id'],
                doc_title: $r['doc_title'],
                doc_description: $r['doc_description'] ?? "(no description)",
                doc_category: $r['doc_category'],
                doc_tags: $r['doc_tags'],
                author: $authorMappings[$r['author']] ?? "(unknown)",
                author_identifier: $r['author'],
                area_of_origin: Mapper::find($r['area_of_origin']),
                area_of_origin_identifier: $r['area_of_origin'] ?? '',
                doc_status: $r['doc_status'],
                tracking_code: $r['tracking_code'],
                dates: $r['dates'],
                current_version: $r['current_version'] ?? 1,
                password_protected: (isset($r['view_password_hash']) && is_string($r['view_password_hash'])) ? true : false,
                category_data: $r['category_data'] ?? []
            )
        );
        
        return $finalDocs;
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