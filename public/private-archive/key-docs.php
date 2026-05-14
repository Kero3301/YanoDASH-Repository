<!-- Important Documents -->
<!-- Assigned Member: Shannon -->

<?php
    session_start();

    require_once '../../src/loader.php';
    load (
        'authentication',
        'authorization',
        'navbar',
        'footer'
    );

    if (!is_logged_in()) {
        header('location: '. $app_url. '/auth/login.php');
        exit;
    }

    if (!is_president($identity, $permissions)) {
        die("You do not have permission to access this resource.");
    }

    $client = mongodb_client();

    $collection = $client->yano_dash->documents;

    $pipeline = [
        [
            '$match' => [
                '$and' => [
                    ['doc_status' => 'PENDING'],
                    ['doc_tags' => new MongoDB\BSON\Regex('^important$', 'i')]
                ]
            ]
        ],
        [
            '$lookup' => [
                'from' => 'accounts',
                'localField' => 'author',
                'foreignField' => '_id',
                'as' => 'author_details'
            ]
        ],
        [
            '$unwind' => [
                'path' => '$author_details',
                'preserveNullAndEmptyArrays' => true
            ]
        ],
        [
            '$sort' => ['dates.date_added' => -1]
        ]
    ];

    $important_docs = $collection->aggregate($pipeline)->toArray();
?>

<!DOCTYPE html>
<html>
<head>
    <?php initialize_page('Important Documents | YanoDASH')?>	
	<link rel="stylesheet" type="text/css" href="../css/pages/rqstyle.css">
</head>
<body class="important-page">
	<?php echo navbar(0) ?>

    <div class="page-header">
        <header class="title">
            <h1> Important Documents </h1>
        </header>

        <div class="top-actions">
            <a href="archive-rq.php" class="important-btn">
                Back to Pending Requests
            </a>
        </div>
    </div>

    <main>
        <div class="table-container">
            <br>
            <table>
                <thead>
                    <tr>
                        <th> Tracking Code </th>
                        <th> Document Title </th>
                        <th> Requested By </th>
                        <th> Date Submitted </th>
                        <th> Status </th>
                        <th> Actions </th>
                    </tr>
                </thead>

                <tbody>
                    <?php if (empty($important_docs)): ?>
                        <tr>
                            <td colspan="6" style="text-align: center;">No documents tagged as "Important" found.</td>
                        </tr>
                    <?php else: ?>
                        <?php foreach ($important_docs as $doc): ?>
                            <tr>
                                <td> <?php echo htmlspecialchars($doc['tracking_code']); ?> </td>
                                <td> <?php echo htmlspecialchars($doc['doc_title']); ?> </td>
                                <td> 
                                    <?php 
                                        $author = $doc['author_details'] ?? null;
                                        
                                        if ($author && isset($author['name'])) {
                                            $firstName = $author['name']['first_name'] ?? '';
                                            $lastName  = $author['name']['last_name'] ?? '';
                                            
                                            $fullName = trim($firstName . ' ' . $lastName);
                                            
                                            echo htmlspecialchars($fullName ?: 'Unknown User');
                                        } else {
                                            echo 'Unknown User';
                                        }
                                    ?>
                                </td>
                                <td> 
                                    <?php 
                                        if(isset($doc['dates']['date_added'])) {
                                            echo $doc['dates']['date_added']->toDateTime()->format('Y-m-d');
                                        } else {
                                            echo 'N/A';
                                        }
                                    ?> 
                                </td>
                                <td>
                                    <span class="status-label <?php echo strtolower($doc['doc_status'] ?? 'pending'); ?>">
                                        <?php echo ucfirst($doc['doc_status'] ?? 'pending'); ?>
                                    </span>
                                </td>
                                <td>
                                    <?php if (($doc['doc_status'] ?? 'pending') === 'PENDING'): ?>
                                        <form action="action.php" method="POST">
                                            <input type="hidden" name="redirect_to" value="key-docs.php">
                                            <input type="hidden" name="doc_id" value="<?php echo (string)$doc['_id']; ?>">
                                            
                                            <input type="hidden" name="tracking_code" value="<?php echo $doc['tracking_code']; ?>">

                                            <button type="submit" name="action" value="approve" class="approve">Approve</button>
                                            <button type="submit" name="action" value="reject" class="reject" onclick="..."> Reject </button>
                                        </form>
                                    <?php else: ?>
                                        <span class="action-done">No actions needed</span>
                                    <?php endif; ?>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </main>
</body>
</html>