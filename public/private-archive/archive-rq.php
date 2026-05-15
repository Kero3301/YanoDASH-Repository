<!-- Pending Archive Requests -->
<!-- Assigned Member: Shannon -->

<?php
    session_start();

    require_once '../../src/loader.php';
    load (
        'authentication',
        'authorization',
        'mongodb_client',
        'mongodb_collections',
        'navbar',
        'footer'
    );

    if (!is_logged_in()) {
        header('location: '. $app_url. '/auth/login.php');
        exit;
    }

    if (!can_access_admin_pages($permissions)) {
        die("You do not have permission to access this resource.");
    }

    $client = mongodb_client();
    $collection_archive = coll('archive_requests', $client);
    $collection_documents = coll('documents', $client);

//     $pipeline = [
//     [
//         '$lookup' => [
//             'from' => 'documents',
//             'let' => [
//                 'request_note' => '$notes'
//             ],
//             'pipeline' => [
//                 [
//                     '$match' => [
//                         '$expr' => [
//                             '$and' => [
//                                 [
//                                     '$eq' => [
//                                         '$tracking_code',
//                                         '$$request_note'
//                                     ]
//                                 ],
//                                 [
//                                     '$eq' => [
//                                         '$doc_status',
//                                         'PENDING'
//                                     ]
//                                 ]
//                             ]
//                         ]
//                     ]
//                 ]
//             ],
//             'as' => 'doc_details'
//         ]
//     ],
//     [
//         '$unwind' => '$doc_details'
//     ],
//     [
//         '$sort' => [
//             'doc_details.dates.date_added' => -1
//         ]
//     ]
// ];

// $pending_requests = $collection_archive
//     ->aggregate($pipeline)
//     ->toArray();

    $pipeline = [
        [
            '$lookup' => [
                'from' => 'documents',
                'localField' => 'document_tc',
                'foreignField' => 'tracking_code',
                'as' => 'doc_details'
            ]
        ],
        [
            '$unwind' => '$doc_details'
        ],
        [
            '$match' => [
                'doc_details.doc_status' => 'PENDING'
            ]
        ],
        [
            '$sort' => ['doc_details.dates.date_added' => -1]
        ]
    ];

    $pending_requests = $collection_archive->aggregate($pipeline)->toArray();
?>

<!DOCTYPE html>
<html>
<head>
    <?php initialize_page('Archive Requests | YanoDASH')?>
    <link rel="stylesheet" type="text/css" href="../css/pages/rqstyle.css">
</head>

<body class="archive-page">
    <?php echo navbar(0) ?>

    <div class="page-header">
        <header class="title">
            <h1> Pending Archive Requests </h1>
        </header>

        <div class="top-actions">
            <a href="key-docs.php" class="important-btn">
                View Important Documents
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
                        <th> Purpose </th>
                        <th> Date Requested </th>
                        <th> Actions </th>
                    </tr>
                </thead>

                <tbody>
                    <?php if (count($pending_requests) > 0): ?>
                        <?php foreach ($pending_requests as $request): ?>
                            <?php $doc = $request['doc_details']; ?>
                            <tr>
                                <td>
                                    <?php echo (string)$doc['tracking_code']; ?>
                                </td>

                                <td>
                                    <?php echo htmlspecialchars($doc['doc_title'] ?? 'Untitled'); ?>
                                </td>

                                <td>
                                    <?php echo htmlspecialchars($request['purpose']); ?>
                                </td>

                                <td>
                                    <?php
                                        if (isset($doc['dates']['date_added'])) {
                                            echo $doc['dates']['date_added']
                                                ->toDateTime()
                                                ->format('Y-m-d');
                                        } else {
                                            echo 'N/A';
                                        }
                                    ?>
                                </td>

                                <td>
                                    <form action="action.php" method="POST">
                                        <input type="hidden" name="redirect_to" value="archive-rq.php">
                                        <input type="hidden" name="archive_id" value="<?php echo (string)$request['_id']; ?>">
                                        <input type="hidden" name="doc_id" value="<?php echo (string)$doc['_id']; ?>">
                                        
                                        <input type="hidden" name="tracking_code" value="<?php echo $request['document_tc']; ?>">

                                        <button type="submit" name="action" value="approve" class="approve"> Approve </button>
                                        <button type="submit" name="action" value="reject" class="reject" onclick="..."> Reject </button>
                                    </form>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <tr>
                            <td colspan="5" style="text-align: center;">No pending requests found.</td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </main>

</body>
</html>