<?php
declare(strict_types=1);
require_once 'query_builder.php';

// const DB_NAME = 'yano_dash';
const COLLECTIONS = [
    'access_levels',
    'account_requests',
    'accounts',
    'archive_requests',
    'document_versions',
    'documents',
    'folders',
    'login_credentials',
    'organizations',
];

// function coll(string $collectionName, object $client): mixed
// {
//     if (!in_array($collectionName, COLLECTIONS)) throw new InvalidArgumentException("Collection $collectionName not found in the database!");
//     $db = $client->{DB_NAME};
//     return $db->{$collectionName};
// }
function coll(string $collectionName): mixed {
    if (!in_array($collectionName, COLLECTIONS)) throw new InvalidArgumentException("Collection $collectionName not found in database!");
    return (new QueryBuilder())->collection($collectionName);
}
?>