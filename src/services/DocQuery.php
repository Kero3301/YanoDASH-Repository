<?php
// doc_query.php - Shannon: This file provides TOOLS only.
require_once dirname(__DIR__). '/database/mongodb.php';
require_once dirname(__DIR__). '/presentation/Mapper.php';
require_once dirname(__DIR__). '/iam/IAMContextValidator.php';
require_once dirname(__DIR__). '/iam/Authorizer.php';
require_once dirname(__DIR__). '/models/DocEd.php';

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
                area_of_origin_identifier: $r['area_of_origin'],
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