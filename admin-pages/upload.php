<!-- Create / Upload Document -->
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
	<title> Create / Upload Documents </title>
	<link rel="stylesheet" type="text/css" href="../css/uploadstyle.css">
</head>
<body>
	<?php echo navbar(0) ?>
	<header class="title"> <!-- start of header -->
		<h1> Create / Upload Document </h1>
	</header> <!-- end of header -->

	<main class="form-container"> <!-- start of main -->
		<div class="create-container"> <!-- start of create-container -->
			<form id="createForm" action="" method="post" enctype="multipart/form-data">
				<label for="docTitle"> Document Title: </label>
				<input type="text" id="docTitle" name="docTitle" placeholder="Enter document title" required>

				<div class="form-group"> 
				    <label for="docCategory"> Category: </label>
				    <select id="docCategory" name="docCategory" required>
				    	<option value="" disabled selected hidden> --Select Category-- </option>
				        <option value="Departmental"> Departmental </option>
				        <option value="Council"> Council </option>
				        <option value="General"> General </option>
				    </select>
				</div>

				<div class="form-group">
				    <label for="docAuthor"> Author / Creator: </label>
				    <input type="text" id="docAuthor" name="docAuthor" placeholder="Enter author/creator name" required>
				</div>

                <label for="docDesc"> Description: </label>
                <textarea id="docDesc" name="docDesc" rows="2" placeholder="Add a brief description (optional)"></textarea>

                <button type="submit" class="approve"> Create Document </button>
			</form>
		</div> <!-- end of create-container -->

	</main> <!-- end of main -->

</body>
</html>