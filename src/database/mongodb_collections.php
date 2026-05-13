<?php
declare(strict_types=1);

const DB_NAME = 'yano_dash';
const COLLECTIONS = [
    'access_levels',
    'account_requests',
    'accounts',
    'archive_requests',
    'document_versions',
    'documents',
    'login_credentials',
    'organizations'
];

function coll(string $collectionName, object $client): mixed
{
    if (!in_array($collectionName, COLLECTIONS)) throw new InvalidArgumentException("Collection $collectionName not found in the database!");
    $db = $client->{DB_NAME};
    return $db->{$collectionName};
}
?>