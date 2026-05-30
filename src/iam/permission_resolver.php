<?php
require_once dirname(dirname(__DIR__)). '/vendor/autoload.php';
require_once dirname(__DIR__). '/database/mongodb.php';

# Resolve the associated user permissions from the database given a userID
function resolve_permissions(?string $userID): ?array {
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

    # Query
    $identity = coll('accounts')
        ->findOne(['_id' => $objectID])
        ->execute();
    if (empty($identity)) return null;

    # Access level and scope-of-access resolution
    $access_level_id = $identity['access_level'] ?? null;
    $access_level = 'viewer';
    $scope = ['public'];
    if (!empty($access_level_id)) {
        $level_result = coll('access_levels')
            ->findOne(['_id' => $access_level_id])
            ->execute();
        if (!empty($level_result)) {
            $access_level = $level_result['level'];
            $scope = $level_result['scope_of_access'] ?? [];
        }
    }

    # Access domain resolution
    $access_domains = $identity['access_domains'] ?? [];

    # Construction
    $result = [
        'user_id' => (string) $identity['_id'],
        'access_level' => $access_level,
        'access_scope' => $scope,
        'access_domains' => $access_domains,
    ];

    # Result caching and return
    return $cache[$key] = $result;
}
?>