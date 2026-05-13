<?php
require_once dirname(dirname(__DIR__)). '/vendor/autoload.php';
require_once dirname(__DIR__). '/database/mongodb_client.php';
require_once dirname(__DIR__). '/database/mongodb_collections.php';
require_once dirname(__DIR__). '/utils/schema_validator.php';

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

    # Client and collections
    $client = mongodb_client();
    $collection_accounts = coll('accounts', $client);
    $collection_accessLevels = coll('access_levels', $client);

    # Query
    $identity = $collection_accounts->findOne(['_id' => $objectID]);
    if (!$identity) return null;

    # Access level and scope-of-access resolution
    $access_level_id = $identity->access_level ?? null;
    $access_level = 'viewer';
    $scope = ['public'];
    if (!empty($access_level_id)) {
        $level_result = $collection_accessLevels->findOne(['_id' => $access_level_id]);
        if ($level_result) {
            $access_level = $level_result->level;
            $scope = $level_result->scope_of_access ?? [];
        }
    }

    # Access domain resolution
    $access_domains = $identity->access_domains ?? [];

    # Construction
    $result = [
        'user_id' => (string) $identity->_id,
        'access_level' => $access_level,
        'access_scope' => $scope,
        'access_domains' => $access_domains->getArrayCopy(),
    ];

    # Result caching and return
    return $cache[$key] = $result;
}
?>