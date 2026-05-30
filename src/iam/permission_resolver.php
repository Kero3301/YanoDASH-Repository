<?php
require_once dirname(dirname(__DIR__)). '/vendor/autoload.php';
require_once dirname(__DIR__). '/database/mongodb.php';
require_once 'context_validator.php';

# Resolve the associated user permissions from the database given an identity array
function resolve_permissions(array $identity): ?array {
    # [STEP 1] Validate the given identity's format
    if (!validate_identity($identity)) return null;
    # POSTCONDITION(S): The given identity is valid

    # [STEP 2] Extract the user ID from the identity array
    $uid = $identity['user_id'];

    # [STEP 3] Caching
    static $cache = [];
    if (isset($cache[$key = (string) $uid])) return $cache[$key];
    # POSTCONDITION(S): User permissions have not been cached yet and is ready for fresh database retrieval and resolution

    # [STEP 4] Definition and execution of main queries
    $account = QueryRunner::tryWithCollections([
        ($C1='accounts') 
            => fn ($C1)=> $C1->findOne(['_id' => oid($uid)])->execute()]) 
        ->getResults($C1);
    if (empty($account) || !valid_oid(oid($account['access_level'] ?? null))) return [
        'access_level' => 'viewer',
        'access_scope' => [],
        'access_domains' => []
    ];
    $accessLevelID = oid($account['access_level']);
    $accessLevel = QueryRunner::tryWithCollections([
        ($C1='access_levels')
            => fn ($C1)=> $C1->findOne(['_id' => $accessLevelID])->execute()])
        ->getResults($C1);
    if (empty($accessLevel)) return [
        'access_level' => 'viewer',
        'access_scope' => [],
        'access_domains' => []
    ];
    # POSTCONDITION(S): Account and associated access level are valid

    # [STEP 5] Extract and define core identity fields
    $ACCESS_LEVEL = isset($accessLevel['level']) 
        ? strtolower(trim($accessLevel['level']))
        : 'viewer';
    $ACCESS_SCOPE = isset($accessLevel['scope_of_access'])
        ? $accessLevel['scope_of_access']
        : [];
    $ACCESS_DOMAINS = isset($account['access_domains'])
        ? $account['access_domains']
        : [];

    # [STEP 6] Result construction, caching, and return
    $result = [
        'access_level'      => $ACCESS_LEVEL,
        'access_scope'      => $ACCESS_SCOPE,
        'access_domains'    => $ACCESS_DOMAINS,
    ];
    return $cache[$key] = $result;
}
?>