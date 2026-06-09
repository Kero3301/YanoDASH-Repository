<?php

class MongoPolicies
{
    public const VALID_COLLECTIONS = [
        'access_levels' => true,
        'account_requests' => true,
        'accounts' => true,
        'archive_requests' => true,
        'document_versions' => true,
        'documents' => true,
        'folders' => true,
        'login_credentials' => true,
        'organizations' => true
    ];

    public const VALID_OPERATIONS = [
        'find' => true,
        'findOne' => true,
        'insertOne' => true,
        'updateOne' => true,
        'deleteOne' => true,
        'deleteMany' => true,
        'countDocuments' => true
    ];

    public const VALID_OPERATORS = [
        '$set' => true,
        '$inc' => true,
        '$gt' => true,
        '$gte' => true,
        '$lt' => true,
        '$lte' => true,
        '$ne' => true,
        '$in' => true,
        '$nin' => true,
        '$or' => true,
        '$and' => true
    ];

    public const VALID_TYPES = [
        '$oid' => true,
        '$date' => true
    ];
}