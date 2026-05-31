<?php
require_once 'Profile.php';
require_once dirname(__DIR__). '/iam/IAMContextResolver.php';
require_once dirname(__DIR__). '/database/mongodb.php';

final class UserContext {
    public static function constructFromUID(mixed $uid): ?array {
        if (!valid_oid(oid($uid))) return null;

        static $cache = [];
        if (isset($cache[$key = (string)$uid])) return $cache[$key];

        $CONTEXT = [];

        $userIA = IAMContextResolver::resolve($uid);
        if (is_null($userIA)) return null;

        $CONTEXT = $userIA;

        $userProfile = Profile::resolve($userIA, (string)$uid);
        if (is_null($userProfile)) return null;

        $CONTEXT['PROFILE'] = $userProfile;
        
        return $cache[$key] = $CONTEXT;
    }
}
?>