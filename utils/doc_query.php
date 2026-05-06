<?php
// doc_query.php - Shannon: This file provides TOOLS only.
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

require __DIR__ . '/../vendor/autoload.php';

use MongoDB\Client;
use MongoDB\BSON\ObjectId;

$mongoUri = "mongodb+srv://yanoDash_RW:yanodash35278@cluster0.psn5zxh.mongodb.net/?appName=Cluster0";

try {
    $client = new Client($mongoUri);
    $db = $client->yano_dash;
} catch (Exception $e) {

    $db_connection_error = $e->getMessage();
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