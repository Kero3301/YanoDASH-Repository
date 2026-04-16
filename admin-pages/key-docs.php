<!-- Pending Archive Requests -->
<!-- Assigned Member: Shannon -->

<?php
    require_once '../components/head.php';
    require_once '../components/navbar.php';
?>

<!DOCTYPE html>
<html>
<head>
	<meta charset="utf-8">
	<meta name="viewport" content="width=device-width, initial-scale=1">
	<title> Important Documents </title>
	<link rel="stylesheet" type="text/css" href="../css/rqstyle.css">
</head>
<body class="important-page">
	<?php echo navbar(0) ?>

    <header class="title">
        <h1> Important Documents </h1>
    </header>

    <main>

        <!-- Optional: Back button -->
        <div class="top-actions">
            <a href="archive-rq.php" class="important-btn">
                Back to Requests
            </a>
        </div>

        <div class="table-container">
            <br>
            <table>
                <thead>
                    <tr>
                        <th> ID </th>
                        <th> Document Title </th>
                        <th> Requested By </th>
                        <th> Date Submitted </th>
                        <th> Status </th>
                        <th> Actions </th>
                    </tr>
                </thead>

                <tbody>
                    <tr>
                        <td> 1 </td>
                        <td> Budget Report 2026 </td>
                        <td> John Doe </td>
                        <td> 2026-04-06 </td>
                        <td><span class="pending">Pending</span></td>
                        <td>
                            <button class="approve">Approve</button>
                            <button class="reject">Reject</button>
                        </td>
                    </tr>

                    <tr>
                        <td> 2 </td>
                        <td> Annual Financial Plan </td>
                        <td> Jane Smith </td>
                        <td> 2026-04-05 </td>
                        <td><span class="approved">Approved</span></td>
                        <td>
                            <button class="approve">Approve</button>
                            <button class="reject">Reject</button>
                        </td>
                    </tr>

                    <tr>
                        <td> 3 </td>
                        <td> Policy Guidelines </td>
                        <td> Alex Cruz </td>
                        <td> 2026-04-04 </td>
                        <td><span class="rejected">Rejected</span></td>
                        <td>
                            <button class="approve">Approve</button>
                            <button class="reject">Reject</button>
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>

    </main>
</body>
</html>