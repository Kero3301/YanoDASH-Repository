<!-- Pending Archive Requests -->
<!-- Assigned Member: Shannon -->

<?php
    session_start();

    require_once '../utils/routing.php';
    require_once dirname(__DIR__). '/utils/loader.php';
    load_components(
        'navbar',
        'footer'
    );
    load_utils(
        'authentication',
        'authorization'
    );

    if (!is_logged_in()) {
        header('location: '. $app_url. '/auth/login.php');
        exit;
    }

    if (!can_access_admin_pages()) {
        die("You do not have permission to access this resource.");
    }
?>

<!DOCTYPE html>
<html>
<head>
    <?php initialize_page('Archive Requests | YanoDASH')?>
    <link rel="stylesheet" type="text/css" href="../css/pages/rqstyle.css">
</head>

<body>
    <?php echo navbar(0) ?>

    <header class="title"> <!-- start of header -->
        <h1> Pending Archive Requests </h1>
    </header> <!-- end of header -->

    <div class="top-actions">
        <a href="key-docs.php" class="important-btn">
            View Important Documents
        </a>
    </div>

    <main> <!-- start of main -->
        <div class="table-container">
            <br>
            <table>
                <thead>
                    <tr>
                        <th> ID </th>
                        <th> Document Title </th>
                        <th> Requested By </th>
                        <th> Date Requested </th>
                        <th> Actions </th>
                    </tr>
                </thead>

                <tbody>
                    <tr>
                        <td> 1 </td>
                        <td> Budget Report 2026 </td>
                        <td> John Doe </td>
                        <td> 2026-04-06 </td>
                        <td>
                            <button class="approve">Approve</button>
                            <button class="reject">Reject</button>
                        </td>
                    </tr>

                    <tr>
                        <td> 2 </td>
                        <td> Event Proposal </td>
                        <td> Jane Smith </td>
                        <td> 2026-04-05 </td>
                        <td>
                            <button class="approve">Approve</button>
                            <button class="reject">Reject</button>
                        </td>
                    </tr>

                    <tr>
                        <td> 3 </td>
                        <td> Meeting Minutes </td>
                        <td> Alex Cruz </td>
                        <td> 2026-04-04 </td>
                        <td>
                            <button class="approve">Approve</button>
                            <button class="reject">Reject</button>
                        </td>
                    </tr>

                    <tr>
                        <td> 4 </td>
                        <td> Project Documentation </td>
                        <td> Maria Lopez </td>
                        <td> 2026-04-03 </td>
                        <td>
                            <button class="approve">Approve</button>
                            <button class="reject">Reject</button>
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>
    </main> <!-- end of main -->

</body>
</html>