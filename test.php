<?php
    ini_set('display_errors', 'Off');

    session_start();
    require_once 'vendor/autoload.php';
    require_once 'utils/loader.php';

    load_components(
        'document_list'
    );
    load_utils(
        'document_factory'
    );
    
    $client = new MongoDB\Client(getenv('YANODASH_V_DBU_URI'));

    $collection_documents = $client->yano_dash->documents_schema;
    $results = $collection_documents->find(
        [
            'is_publicized' => true
        ]
    );
    $documents = get_all($results);
?>

<!DOCTYPE html>
<html>
    <head>
        <?php initialize_page("Document Loading Test")?>
    </head>
    <body>
        <?php list_all_documents($documents);?>
    </body>
</html>