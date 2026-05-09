<?php
require_once dirname(dirname(__DIR__)). '/vendor/autoload.php';
require_once dirname(__DIR__). '/database/mongodb_client.php';
require_once dirname(__DIR__). '/utils/schema_validator.php';

# Resolve the associated identity from the database given a userID
function resolve_identity(?string $userID): ?array {
    # Ignore if user ID is null
    if (!$userID) return null;

    # Caching
    static $cache = [];

    # User ID storage
    $_id = new MongoDB\BSON\ObjectId($userID);
    
    # Cached return
    $cacheKey = (string) $_id;
    if (isset($cache[$cacheKey])) return $cache[$cacheKey];

    # Client and collections
    $client = mongodb_client();
    $collection_accounts = $client->yano_dash->account_schema;
    $collection_organizations = $client->yano_dash->organizations_schema;
    $collection_accessLevels = $client->yano_dash->access_levels_schema;

    # Query
    $condition_identity = ["_id" => $_id];
    $identity = $collection_accounts->findOne($condition_identity);
    if (!$identity || !baseline_schema_validate($identity, 'ACCOUNTS')) return null;

    # Access level and organization resolution
    $access_level = null;
    if (!empty($identity->access_level_id)) {
        $al = $collection_accessLevels->findOne(["_id" => $identity->access_level_id]);
        if ($al) $access_level = $al->level;
    }
    $organization_name = null;
    if (!empty($identity->organization)) {
        $org = $collection_organizations->findOne(["_id" => $identity->organization]);
        if ($org) $organization_name = $org->organization_name ?? "None";
    }
    
    # Construction
    $result = [
        'email' => $identity->email_address ?? null,
        'organization' => $organization_name ?? null,
        'position' => $identity->position ?? null,
        'access_level' => $access_level ?? null,
        'access_domains' => $identity->access_domains ?? null
    ];

    # Result caching and return
    return $cache[$cacheKey] = $result;
}
?>