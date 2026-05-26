<?php
require_once dirname(dirname(__DIR__)). '/vendor/autoload.php';
require_once dirname(__DIR__). '/database/mongodb_collections.php';

# Resolve the associated identity from the database given a userID
function resolve_identity(?string $userID): ?array {
    # Ignore if user ID is empty
    if (empty($userID)) return null;

    # Caching
    static $cache = [];
    try { 
        $objectID = new MongoDB\BSON\ObjectId($userID); 
    } 
    catch (Exception $e) { 
        return null; 
    }
    $key = (string) $objectID;
    if (isset($cache[$key])) return $cache[$key];

    $identity = coll('accounts')
        ->findOne(['_id' => $objectID])
        ->execute();
    if (empty($identity)) return null;

    # Organization resolution
    $organization_name = null;
    if (!empty($identity['organization'])) {
        $org = coll('organizations')
            ->findOne(["_id" => $identity['organization']])
            ->execute();
        if (!empty($org)) $organization_name = $org['organization_name'];
    }

    # Data normalization
    $position = isset($identity['position'])
        ? strtoupper(trim((string)$identity['position']))
        : null;
    
    # Construction
    $result = [
        'user_id' => (string) $identity['_id'],
        'email' => $identity['email_address'] ?? null,
        'organization' => $organization_name,
        'department' => $identity['department'] ?? "(unknown)",
        'position' => $position
    ];

    # Result caching and return
    return $cache[$key] = $result;
}
?>