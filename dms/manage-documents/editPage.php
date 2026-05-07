<?php
    session_start();

    // require_once '../utils/routing.php';
    require_once '../../utils/loader.php';
    load_components(
        'navbar',
        'sidebar'
    );


?>

<!DOCTYPE html>
<html>
<head>
    <?php initialize_page('Edit Document | YanoDASH')?>
    <link rel="stylesheet" type="text/css" href="../../css/pages/editstyle.css">
</head>
<body>
    <?php echo navbar(0) ?>
    <!-- Edit Document Content -->
    <div> 
        <h1>Edit Document</h1>
        <form id="editDocumentForm" method="POST" action="../utils/editLogic.php">
            
        <div class="tca">
            <input type="hidden" name="doc_id" value="<?php echo $_GET['doc_id']; ?>">
            <label  class="doc_title" for="doc_title">Document Title:</label>
            <input class="box" type="text" name="doc_title" required> 
        </div>

            
        <div class="tca">
            <label for="category">Category:</label>
            <select name="category" required>
                <option value="">Select Category</option>
                <option value="Activity Designs">Activity Designs</option>
                <option value="Memorandum">Memorandum</option>
                <option value="Financial Statements">Financial Statements</option>
                <option value="Minutes of Meetings">Minutes of Meetings</option>
                <option value="Accomplishment Report">Accomplishment Report</option>
                <option value="Project Proposal">Project Proposal</option>
            </select>
        </div>

        <div class="tca">
            <label for="area">Area:</label>
            <input class="box" type="text" name="area" required>
        </div>

            <button type="submit" class="btn">Save Changes</button>
        </form>
    </div>

</body>
</html>