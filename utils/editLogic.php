<?php
session_start();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    $tracking_code = $_POST['tracking_code'];
    $new_title = $_POST['doc_title'];
    $current_version = (int)$_POST['current_version'];

    if (isset($_FILES['new_file']) && $_FILES['new_files']['error'] === UPLOAD_ERR_OK){

        $new_version = $current_version + 1;
        $extension = pathinfo($_FILES['new_file']['name'], PATHINFO_EXTENSION);

        $new_filename = $tracking_code . "_v" . $new_version . "." . $extension;
        $upload_path = '../uploads/' . $new_filename;

        if (move_upload_file($_FILES['new_file']['tmp_name'], $upload_path)) {
            
            header("Location: manage.php?success=new_version");
        } 

    } else {
        header("Location: manage.php?success=metadata_updated");
    }

}

?>
