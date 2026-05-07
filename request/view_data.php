<?php
require_once '../vendor/autoload.php';

try {
    $client = new MongoDB\Client(getenv('YANODASH_V_DBU_URI'));
    $collection = $client->yano_dash->sample_mongodb_data;
    
    // Count total documents
    $totalDocs = $collection->countDocuments();
    echo "<h2>📊 Total Documents in Database: $totalDocs</h2>";
    
    // Get all documents
    $documents = $collection->find([], ['sort' => ['created_at' => -1]]);
    
    echo "<table border='1' cellpadding='10' style='border-collapse: collapse; width: 100%;'>";
    echo "<tr style='background: #2e7d32; color: white;'>";
    echo "<th>#</th><th>Document Type</th><th>Purpose</th><th>File</th><th>Date Created</th><th>ID</th>";
    echo "</tr>";
    
    $counter = 1;
    foreach ($documents as $doc) {
        $date = $doc['created_at']->toDateTime()->format('Y-m-d H:i:s');
        echo "<tr>";
        echo "<td>" . $counter++ . "</td>";
        echo "<td>" . htmlspecialchars($doc['docType'] ?? 'N/A') . "</td>";
        echo "<td>" . htmlspecialchars($doc['purpose'] ?? 'N/A') . "</td>";
        echo "<td>" . ($doc['file'] ? '<a href="../uploads/'.htmlspecialchars($doc['file']).'" target="_blank">'.htmlspecialchars($doc['file']).'</a>' : 'No file') . "</td>";
        echo "<td>" . $date . "</td>";
        echo "<td><small>" . substr((string)$doc['_id'], -8) . "</small></td>";
        echo "</tr>";
    }
    echo "</table>";
    
} catch (Exception $e) {
    echo "Error: " . $e->getMessage();
}
?>