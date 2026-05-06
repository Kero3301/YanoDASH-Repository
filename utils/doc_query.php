<?php
// doc_query.php - Shannon: This file provides TOOLS only.
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

require __DIR__ . '/../vendor/autoload.php';

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