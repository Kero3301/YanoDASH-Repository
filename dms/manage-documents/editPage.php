<?php
    session_start();

    // require_once '../utils/routing.php';
    require_once '../../utils/loader.php';
    load_components(
        'navbar',
        'sidebar'
    );

    require_once '../../vendor/autoload.php';

    use MongoDB\Client;

    $client = new MongoDB\Client(getenv('YANODASH_V_DBU_URI'));

    $db = $client->yano_dash;
    $collection = $db->documents_schema;


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
        <h1 class="title">Edit Document</h1>
        <form id="editDocumentForm" method="POST" action="../../utils/editLogic.php">
            
        <div class="tca">
            <?php $document = $collection->findOne(['_id' => new MongoDB\BSON\ObjectId($_GET['_id'])]);?>
            <input type="hidden" name="tracking_code" value="<?php echo $document->tracking_code; ?>">
            <input type="text" name="doc_title" value="<?php echo $document->doc_title; ?>">
        </div>

            
        <div class="tca">
            <label for="category">Category:</label>
            <select class="ay" name="category" required>
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