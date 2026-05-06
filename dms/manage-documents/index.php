<!-- Manage Documents -->
<!-- Assigned Member: Shannon -->

<?php
    session_start();

    require_once '../../utils/loader.php';
   
    load_components('navbar');
    load_utils(
        'authentication',
        'authorization'
    );

    if (!is_logged_in()) {
        header('location: '. $app_url. '/auth/login.php');
        exit;
    }

    if (!can_use_dms())
        die("You do not have permission to access this resource.");
?>

<!DOCTYPE html>
<html>
<head>
    <?php initialize_page('Manage Documents | YanoDASH')?>
	<link rel="stylesheet" type="text/css" href="../../css/pages/managestyle.css">
</head>
<body>
	<?php echo navbar(0) ?>
	<header class="title"> <!-- start of header -->
		<h1> Manage Documents </h1>
	</header> <!-- end of header -->

    <div class="controls"> <!-- start of controls -->
        <input type="text" placeholder="Search documents...">
        
        <select> 
            <option> All Categories </option>
            <option> Reports </option>
            <option> Proposals </option>
            <option> Minutes </option>
        </select>
    </div> <!-- end of controls -->

    <div class="table-container"> <!-- start of table -->
        <br>
        <table>
            <thead>
                <tr>
                    <th> ID </th>
                    <th> Document Title </th>
                    <th> Category </th>
                    <th> Date Uploaded </th>
                    <th> Status </th>
                    <th> Actions </th>
                </tr>
            </thead>

            <tbody>
                <tr>
                    <td> 1 </td>
                    <td> Budget Report 2026 </td>
                    <td> Reports </td>
                    <td> 2026-04-01 </td>
                    <td> Active </td>
                    <td>
                        <a href="editPage.php?doc_id=1"><button class="edit">Edit</button></a>
						<button class="delete">Delete</button>
                    </td>
                </tr>

                <tr>
                    <td> 2 </td>
                    <td> Event Proposal </td>
                    <td> Proposals </td>
                    <td> 2026-03-28 </td>
                    <td> Active </td>
                    <td>
                        <button class="edit">Edit</button>
						<button class="delete">Delete</button>
                    </td>
                </tr>

                <tr>
                    <td> 3 </td>
                    <td> Meeting Minutes </td>
                    <td> Minutes </td>
                    <td> 2026-03-25 </td>
                    <td> Archived </td>
                    <td>
                        <button class="edit">Edit</button>
						<button class="delete">Delete</button>
                    </td>
                </tr>

                <tr>
                    <td> 4 </td>
                    <td> Project Documentation </td>
                    <td> Reports </td>
                    <td> 2026-03-20 </td>
                    <td> Active </td>
                    <td>
                        <button class="edit">Edit</button>
						<button class="delete">Delete</button>
                    </td>
                </tr>
            </tbody>
        </table>
    </div> <!-- end of table -->

</body>
</html>