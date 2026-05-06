<?php

require __DIR__ . '/../vendor/autoload.php';

use MongoDB\Client;
use MongoDB\BSON\ObjectId;

$mongoUri = "mongodb+srv://yanoDash_RW:yanodash35278@cluster0.psn5zxh.mongodb.net/?appName=Cluster0";

try {
    $client = new Client($mongoUri);
    $db = $client->yano_dash;
} catch (Exception $e) {
    die("Connection failed.");
}

$userId = $_SESSION['user_id'] ?? null;

if (!$userId) {
    die("Unauthorized: Please log in.");
}

/**
 * Updated to match your actual DB structure:
 * - Checks 'scope_of_access' array for permissions
 * - Uses 'access_domains' for private document filtering
 */
function buildQuery($user, $access, $pageType = 'dms') {
    $scopes = isset($access['scope_of_access']) ? iterator_to_array($access['scope_of_access']) : [];
    
    // Normalize Organization (it was 'organization' in your raw data)
    $userOrg = $user['organization'] ?? null;
    $orgString = ($userOrg instanceof ObjectId) ? (string)$userOrg : $userOrg;

    // Basic Permission Check
    if (!in_array('read_docs', $scopes)) {
        return ['_id' => 'DENIED']; 
    }

    switch ($pageType) {
        case 'dms':
            // DMS PAGE: Only docs from the user's specific organization
            return [
                'area_of_origin' => $orgString
            ];

        case 'private':
            // PRIVATE ARCHIVE: Not publicized + Status is Archived
            // Note: We still filter by Org so they only see their own office's archive
            return [
                'is_publicized' => false,
                'doc_status' => ['$regex' => '^archived$', '$options' => 'i'],
                'area_of_origin' => $orgString
            ];

        case 'public':
            // PUBLIC ARCHIVE: Only publicized documents
            return [
                'is_publicized' => true
            ];

        default:
            return ['_id' => 'INVALID_PAGE'];
    }
}

try {
    $userId = $_SESSION['user_id'] ?? null; 
    if (!$userId) die("Please log in.");

    $user = $db->account_schema->findOne(['_id' => new ObjectId($userId)]);
    $access = $db->access_levels_schema->findOne(['_id' => $user['access_level_id']]);

    // 🔹 CHANGE THIS PER PAGE: 'dms', 'private', or 'public'
    $currentPage = 'dms'; 
    
    $query = buildQuery($user, $access, $currentPage);

    // Handle Search
    if (!empty($_GET['search'])) {
        $searchTerm = preg_quote($_GET['search']);
        $searchQuery = ['doc_title' => ['$regex' => $searchTerm, '$options' => 'i']];
        
        // Combine page filters with search
        $query = ['$and' => [$query, $searchQuery]];
    }

    $documents = $db->documents_schema->find($query);

    echo "<h2>" . ucfirst($currentPage) . " Archive</h2>";
    foreach ($documents as $doc) {
        echo "<h3>" . htmlspecialchars($doc['doc_title']) . "</h3>";
        echo "<p>Origin: " . htmlspecialchars((string)($doc['area_of_origin'] ?? 'N/A')) . "</p>";
        echo "<p>Status: " . htmlspecialchars($doc['doc_status'] ?? 'N/A') . "</p><hr>";
    }

} catch (Exception $e) {
    echo "Error: " . $e->getMessage();
}
?>