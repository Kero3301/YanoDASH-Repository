<!-- Pending Archive Requests -->
<!-- Assigned Member: Shannon -->

<?php
    session_start();

    require_once '../../bootstrap/app.php';
    load (
        'authentication',
        'authorization',
        'mongodb',
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

    $collection_requests = coll('archive_requests');
    $collection_documents = coll('documents');
    $collection_accounts = coll('accounts');

    $requests = $collection_requests
        ->find(['status' => 'pending'])
        ->execute();
        
    $pending_requests = [];
    if (!empty($requests)) {
        foreach($requests as $req) {
            $reqTC = $req['tracking_code'];
            $requesterID = $req['requested_by'];
            $requestID = $req['_id'];
            $requester = $collection_accounts
                ->findOne(['_id' => new MongoDB\BSON\ObjectId($requesterID)])
                ->execute();
            $requesterName = $requester['name']['first_name'] ?? ''. $requester['name']['last_name'] ?? '';
            $reqDate = (new DateTime($req['created_at']))->setTimezone(new DateTimeZone('Asia/Manila'))->format('M d Y, g:i A');
            $purpose = $req['purpose'];
            $docTC = $req['notes'];
            $doc = $collection_documents
                ->findOne(['tracking_code' => $docTC])
                ->execute();
            $docTitle = $doc['doc_title'] ?? '(unknown)';

            array_push($pending_requests, [$docTC, $docTitle, $purpose, $requesterName, $reqDate, $requestID]);
        }
    }
?>

<!DOCTYPE html>
<html>
<head>
    <?php initialize_page('Archive Requests | YanoDASH')?>
    <link rel="stylesheet" type="text/css" href="../css/pages/rqstyle.css">
</head>

<body class="archive-page">
    <?php echo navbar(0) ?>
    <div class="page-contents no-padding">
    <div class="pch">
        <h1>Pending Archive Requests</h1>
    </div>

    <div class="top-actions">
        <a href="key-docs.php" class="important-btn">
            View Important Documents
        </a>
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
                        <th> Requester </th>
                        <th> Date Requested </th>
                        <th> Actions </th>
                    </tr>
                </thead>

                <tbody>
                    <?php if (count($pending_requests) > 0): ?>
                        <?php foreach ($pending_requests as $request): ?>
                            <tr>
                                <td>
                                    <?php echo htmlspecialchars($request[0]); ?>
                                </td>

                                <td>
                                    <?php echo htmlspecialchars($request[1]); ?>
                                </td>

                                <td>
                                    <?php echo htmlspecialchars($request[2]); ?>
                                </td>

                                <td>
                                    <?php echo htmlspecialchars($request[3]); ?>
                                </td>

                                <td>
                                    <?php echo htmlspecialchars($request[4]); ?>
                                </td>

                                <td>
                                    <form action="action.php" method="POST">
                                        <input type="hidden" name="redirect_to" value="archive-rq.php">
                                        <input type="hidden" name="archive_id" value="<?php echo (string)$request[5]; ?>">

                                        
                                        <input type="hidden" name="tracking_code" value="<?php echo $request[0]; ?>">

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
    </div>
</body>
</html>