<?php
require_once 'IAMContextValidator.php';

final class Authorizer {
    # Evaluate if a user, given their permissions, is an admin
    public static function isAdmin(?array $permissions): bool {
        if (!is_array($permissions)) return false;
        return ($permissions['access_level'] ?? null) === 'admin';
    }

    # Evaluate if a user, given their permissions, is an editor
    public static function isEditor(?array $permissions): bool {
        if (!is_array($permissions)) return false;
        return ($permissions['access_level'] ?? null) === 'editor';
    }

    # Evaluate if a user, given their permissions, can use the common DMS functions
    public static function canUseDMS(?array $permissions): bool {
        if (!is_array($permissions)) return false;
        return Authorizer::isAdmin($permissions) || Authorizer::isEditor($permissions);
    }

    # Evaluate if a user, given their permissions, can access the common admin pages
    public static function canAccessAdminPages(?array $permissions): bool {
        if (!is_array($permissions)) return false;
        return isAdmin($permissions);
    }

    # Evaluate if a user, given their identity and permissions, meets the override specification for president
    public static function isPresident(mixed $user): bool {
        if (!IAMContextValidator::validate($user)) return false;

        $orgID = $user['IDENTITY']['org_id'];
        $dept = $user['IDENTITY']['department'];
        $position = $user['IDENTITY']['position'];
        
        if ($orgID !== '69f88cdcd1dd355cb895ded2') return false;
        if (trim($dept) !== 'osc_president_office') return false;
        if (strtoupper(trim($position)) !== 'PRESIDENT') return false;

        $accessLevel = $user['PERMISSIONS']['access_level'];
        $accessDomains = $user['PERMISSIONS']['access_domains'];

        if (trim($accessLevel) !== 'admin') return false;
        if (!in_array('*', $accessDomains, true)) return false;

        return true;
    }

    # Evaluate if a user, given their identity and permissions, can access a specific page or action given its requirements
    function can_access(?array $identity, ?array $permissions, array $req): bool {
        if (!is_array($identity) || !is_array($permissions)) return false;

        $accessLevel = $permissions['access_level'] ?? 'viewer';
        $accessScope = $permissions['access_scope'] ?? [];
        $accessDomains  = $permissions['access_domains'] ?? ['public'];
        $position = $identity['position'] ?? null;

        $req_scope  = $req['scope'] ?? [];
        $req_domain = $req['domain'] ?? null;

        if (is_president($identity, $permissions)) return true;
        
        if ($req_domain !== null) {
            $domain_allowed =
                in_array('*', $accessDomains, true) ||
                in_array($req_domain, $accessDomains, true);

            if (!$domain_allowed) return false;
        }

        foreach ($req_scope as $perm) {
            if (!in_array($perm, $accessScope, true)) return false;
        }

        return true;
    }
}
?>