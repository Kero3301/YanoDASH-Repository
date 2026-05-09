<?php
    require __DIR__. '/../vendor/autoload.php';
    $client = new MongoDB\Client(getenv('YANODASH_V_DBU_URI'));

    $collection_accounts = $client->yano_dash->account_schema;
    $collection_documents = $client->yano_dash->documents_schema;

    function fetch_public_archive_documents(): array {
        $documentList = [];

        global $collection_documents;
        $results = $collection_documents->find([
            'is_publicized' => true
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