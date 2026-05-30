<?php
    require __DIR__. '/../vendor/autoload.php';
    require dirname(__DIR__). '/database/mongodb.php';

    $client = new MongoDB\Client(getenv('YANODASH_V_DBU_URI'));
    $collection_accounts = coll('accounts', $client);
    $collection_documents = coll('documents', $client);

    function fetch_public_archive_documents(): array {
        $documentList = [];

        global $collection_documents;
        $results = $collection_documents->find([
            'doc_status' => 'PUBLICIZED'
        ]);

        foreach ($results as $result) {
            $isValid = baseline_schema_validate($result, 'DOCUMENT'); # TODO: Implement document baseline schema validation
            if (!$isValid) continue;

            $documentObject = new Document($result); # TODO: Include document DTO class for object construction

            array_push($documentList, $documentObject);
        }

        return $documentList;
    }

    function fetch_dms_documents(): array {
        $email = $_SESSION['email'];
        if (!$email) return [];

        $role = $_SESSION['role'];
        if (!in_array($role, ['admin', 'editor'], true)) return [];

        global $collection_accounts;
        global $collection_documents;
    
        $result = $collection_accounts->findOne([
            'email_address' => $email
        ]);

        if ($result === null) return [];

        
        
    }
?>