<?php
require_once dirname(dirname(__DIR__)). '/vendor/autoload.php';
require_once dirname(__DIR__). '/database/mongodb.php';

function resolve_user(mixed $userID): ?array {
    if (!valid_oid(oid($userID))) return null;

    static $cache = [];
    if (isset($cache[$key = (string)$userID])) return $cache[$key];

    $account = QueryRunner::tryWithCollections([
        ($C1='accounts') => fn ($C1)=> $C1->findOne(['_id' => oid($userID)])->execute()])
        ->getResults($C1);
    if (empty($account)) return null;

    $accessLevelID = oid($account['access_level']);
    $accessLevel = QueryRunner::tryWithCollections([
        ($C2='access_levels') => fn ($C2)=> $C2->findOne(['_id' => $accessLevelID])->execute()])
        ->getResults($C2);
    
    # IDENTITY
    $EMAIL_ADDRESS = $account['email_address'];
    $ORG_ID = (string) $account['organization'];
    $DEPARTMENT = $account['department'];
    $POSITION = $account['position'];

    # PERMISSIONS
    $ACCESS_LEVEL = isset($accessLevel['level'])
        ? strtolower(trim($accessLevel['level']))
        : 'viewer';
    $ACCESS_SCOPE = isset($accessLevel['scope_of_access'])
        ? $accessLevel['scope_of_access']
        : [];
    $ACCESS_DOMAINS = isset($account['access_domains'])
        ? $account['access_domains']
        : [];
    
    $result = [
        "IDENTITY" => [
            'email' => $EMAIL_ADDRESS,
            'org_id' => $ORG_ID,
            'department' => $DEPARTMENT,
            'position' => $POSITION
        ],
        "PERMISSIONS" => [
            'access_level' => $ACCESS_LEVEL,
            'access_scope' => $ACCESS_SCOPE,
            'access_domains' => $ACCESS_DOMAINS
        ]
    ];
    return $cache[$key] = $result;
}
?>