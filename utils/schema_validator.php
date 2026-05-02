<?php
    function normalize_mongo_value($value)
    {
        if ($value instanceof MongoDB\Model\BSONDocument) {
            return $value->getArrayCopy();
        }

        if ($value instanceof Traversable) {
            return iterator_to_array($value);
        }

        return $value;
    }

    function validate_field($value, array $rules): bool
    {
        $value = normalize_mongo_value($value);

        if ($value === null) {
            return false;
        }

        switch ($rules['type']) {
            case 'date':
                return $value instanceof MongoDB\BSON\UTCDateTime;

            case 'string':
                if (!is_string($value)) {
                    return false;
                }

                if (isset($rules['allow_empty']) && $rules['allow_empty'] === false) {
                    if (trim($value) === '') {
                        return false;
                    }
                }

                if (isset($rules['min_length']) && strlen($value) < $rules['min_length']) {
                    return false;
                }

                if (isset($rules['regex']) && !preg_match($rules['regex'], $value)) {
                    return false;
                }

                if (isset($rules['enum'])) {
                    $enum = $rules['enum'];

                    if (!empty($rules['enum_case_insensitive'])) {

                        $valueNorm = strtolower($value);
                        $enumNorm = array_map('strtolower', $enum);

                        if (!in_array($valueNorm, $enumNorm, true)) {
                            return false;
                        }

                    } else {

                        if (!in_array($value, $enum, true)) {
                            return false;
                        }
                    }
                }

                return true;

            case 'array':
                if (!is_array($value)) {
                    return false;
                }

                if (isset($rules['min_items']) && count($value) < $rules['min_items']) {
                    return false;
                }

                if (isset($rules['items_type'])) {
                    foreach ($value as $item) {

                        switch ($rules['items_type']) {

                            case 'string':
                                if (!is_string($item) || trim($item) === '') {
                                    return false;
                                }
                                break;

                            case 'objectid':
                                if (!($item instanceof MongoDB\BSON\ObjectId)) {
                                    return false;
                                }
                                break;

                            case 'date':
                                if (!($item instanceof MongoDB\BSON\UTCDateTime)) {
                                    return false;
                                }
                                break;

                            default:
                                return false;
                        }
                    }
                }
                return true;

            case 'boolean':
                return is_bool($value);

            case 'objectid':
                return $value instanceof MongoDB\BSON\ObjectId;

            case 'object':

                if ($value instanceof MongoDB\Model\BSONDocument) {
                    $value = $value->getArrayCopy();
                }

                if (!is_array($value)) {
                    return false;
                }

                foreach ($rules['schema'] as $subField => $subRules) {

                    if (!array_key_exists($subField, $value)) {
                        if (!isset($subRules['optional']) || $subRules['optional'] !== true) {
                            return false;
                        }
                        continue;
                    }

                    if ($value[$subField] === null && (!isset($subRules['optional']) || $subRules['optional'] !== true)) {
                        return false;
                    }

                    if (!validate_field($value[$subField], $subRules)) {
                        return false;
                    }
                }

                return true;

            default:
                return false;
        }
    }

    function baseline_schema_validate($schemaInstance, string $schema): bool
    {
        if ($schemaInstance instanceof MongoDB\Model\BSONDocument) {
            $data = $schemaInstance->getArrayCopy();
        } elseif ($schemaInstance instanceof Traversable) {
            $data = iterator_to_array($schemaInstance);
        } elseif (is_array($schemaInstance)) {
            $data = $schemaInstance;
        } else {
            return false;
        }

        $schemas = [

            'DOCUMENTS' => [
                'doc_title' => ['type' => 'string'],

                'doc_type' => [
                    'type' => 'string',
                    'optional' => true
                ],

                'doc_categories' => [
                    'type' => 'array',
                    'min_items' => 1
                ],

                'doc_status' => [
                    'type' => 'string',
                    'enum' => ['draft', 'under review', 'pending review', 'approved', 'finalized', 'archived'],
                    'enum_case_insensitive' => true
                ],

                'description' => ['type' => 'string', 'optional' => true],
                'keywords' => [
                    'type' => 'array',
                    'items_type' => 'string',
                    'optional' => true
                ],

                'area_of_origin' => ['type' => 'string'],

                'author' => ['type' => 'string'],

                'dates' => [
                    'type' => 'object',
                    'schema' => [
                        'date_added' => ['type' => 'date'],

                        'date_finalized' => [
                            'type' => 'date',
                            'optional' => true
                        ],

                        'date_archived' => [
                            'type' => 'date',
                            'optional' => true
                        ]
                    ]
                ],

                'current_version_id' => ['type' => 'objectid'],

                'tracking_code' => ['type' => 'string'],

                'is_publicized' => ['type' => 'boolean'],

                'view_password_hash' => ['type' => 'string', 'optional' => true],
                'view_password_expiry_date' => ['type' => 'date', 'optional' => true]
            ],

            'DOCUMENT_VERSIONS' => [
                'doc_id' => ['type' => 'objectid'],

                'version_number' => ['type' => 'string'],

                'file_path' => ['type' => 'string'],

                'date_added' => ['type' => 'date']
            ],

            'ACCESS_LEVELS' => [
                'level' => [
                    'type' => 'string',
                    'enum' => ['admin', 'editor', 'viewer']
                ],
                'scope_of_access' => [
                    'type' => 'array',
                    'min_items' => 1
                ]
            ],

            'ACCOUNTS' => [
                'name' => [
                    'type' => 'object',
                    'schema' => [
                        'first_name' => ['type' => 'string'],
                        'middle_name' => ['type' => 'string', 'optional' => true],
                        'last_name' => ['type' => 'string']
                    ]
                ],
                'student_id_number' => [
                    'type' => 'string',
                    'optional' => true,
                    'regex' => '/^\d{4}-\d{5}$/'
                ],
                'organization' => ['type' => 'string'],
                'position' => ['type' => 'string'],

                'email_address' => [
                    'type' => 'string',
                    'regex' => '/^[^@\s]+@[^@\s]+\.[^@\s]+$/'
                ],

                'access_level_id' => ['type' => 'objectid'],
                'access_domains' => ['type' => 'array', 'min_items' => 1],

                'doc_bookmarks' => [
                    'type' => 'array',
                    'items_type' => 'string',
                    'optional' => true
                ],
            ],

            'LOGIN_CREDENTIALS' => [
                'user' => ['type' => 'objectid'],
                'password_hash' => ['type' => 'string', 'min_length' => 20],
                'email_2fa_enabled' => ['type' => 'boolean']
            ]
        ];

        $schema = strtoupper($schema);

        if (!isset($schemas[$schema])) {
            return false;
        }

        foreach ($schemas[$schema] as $field => $rules) {

            if (!array_key_exists($field, $data)) {
                if (isset($rules['optional']) && $rules['optional'] === true) {
                    continue;
                }

                return false;
            }

            if (!validate_field($data[$field], $rules)) {
                return false;
            }
        }

        return true;
}