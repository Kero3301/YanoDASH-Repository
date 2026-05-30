<?php
require_once dirname(dirname(__DIR__)). '/vendor/autoload.php';
require_once dirname(__DIR__). '/database/mongodb.php';

# Resolve the associated identity from the database given a userID. Mixed type for $userID to account for adversarial conditions.
function resolve_identity(mixed $userID): ?array {
    # [STEP 1] Safeguard to ignore obviously incorrect types and values
    if (!valid_oid(oid($userID))) return null;      # Ignore if the returned result of $userID conversion is not an instance of MongoDB\BSON\ObjectId
    # POSTCONDITION(S): Supplied userID is either an existing ObjectId or a string compatible with ObjectId format, and is safe to use

    # [STEP 2] Caching
    static $cache = [];
    if (isset($cache[$key = (string)$userID])) return $cache[$key];
    # POSTCONDITION(S): User identity has not been cached yet and is ready for fresh database retrieval and resolution

    # [STEP 3] Definition and execution of main query
    $account = QueryRunner::tryWithCollections([
        ($C1='accounts') 
            => fn ($C1)=> $C1->findOne(['_id' => oid($userID)])->execute()]) 
        ->getResults($C1);
    if (empty($account)) return null;
    # POSTCONDITION(S): A valid document of the `accounts` collection with the given conditions was found from the query

    # [STEP 4] Extract and define core identity fields
    $USER_ID = (string) $account['_id'];
    $EMAIL_ADDRESS = $account['email_address'];
    $ORG_ID = (string) $account['organization'];
    $DEPARTMENT = $account['department'];
    $POSITION = $account['position'];
    
    # [STEP 5] Result construction, caching, and return
    $result = [
        'user_id'       => $USER_ID,
        'email'         => $EMAIL_ADDRESS,
        'org_id'        => $ORG_ID,
        'department'    => $DEPARTMENT,
        'position'      => $POSITION
    ];
    return $cache[$key] = $result;
}
?>