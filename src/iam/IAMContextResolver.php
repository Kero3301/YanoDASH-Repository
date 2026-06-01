<?php
require_once dirname(dirname(__DIR__)). '/vendor/autoload.php';
require_once dirname(__DIR__). '/database/mongodb.php';

final class IAMContextResolver {
    public static function resolve(mixed $userID): ?array {
        # Return null if provided userID is null
        if ($userID === null) return null;
        # POSTCONDITIONS: The supplied userID is not null

        # Ignore invalid ObjectIds
        if (!valid_oid(oid($userID))) return null;
        # POSTCONDITIONS: Supplied userID is valid and safe to use

        # Return null if user ID is not set, triggering an automatic context nullification
        if (!isset($_SESSION['user_id'])) return null;
        # POSTCONDITIONS: user_id is set in session

        # Prevent arbitrary resolution of userIDs that are not the same as current session's user ID to prevent potential abuse
        if ($_SESSION['user_id'] !== (string)$userID) return null;
        # POSTCONDITIONS: The session user_id is the same as the supplied userID

        # Attempt to read from cache
        static $cache = [];
        if (isset($cache[$key = (string)$userID])) return $cache[$key];
        # POSTCONDITIONS: Data is still uncached

        # Query account from database using the userID
        $account = QueryRunner::tryWithCollections([
            ($C1='accounts') => fn ($C1)=> $C1->findOne(['_id' => oid($userID)])->execute()])
            ->getResults($C1);
        if (empty($account)) return null;
        # POSTCONDITIONS: The appropriate account was found

        # Query user's associated access level from database
        $accessLevelID = oid($account['access_level'] ?? null);
        $accessLevel = QueryRunner::tryWithCollections([
            ($C2='access_levels') => fn ($C2)=> $C2->findOne(['_id' => $accessLevelID])->execute()])
            ->getResults($C2);
        # POSTCONDITIONS: The user's access level may or may not have been found
        
        # IDENTITY DATA
        $EMAIL_ADDRESS = $account['email_address'];
        $ORG_ID = (string) $account['organization'];
        $DEPARTMENT = $account['department'];
        $POSITION = $account['position'];

        # PERMISSIONS DATA
        $ACCESS_LEVEL = isset($accessLevel['level'])
            ? strtolower(trim($accessLevel['level']))
            : 'viewer';
        $ACCESS_SCOPE = isset($accessLevel['scope_of_access'])
            ? $accessLevel['scope_of_access']
            : ['view_docs', 'download_docs'];
        $ACCESS_DOMAINS = isset($account['access_domains'])
            ? $account['access_domains']
            : ['public'];
        
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
}
?>