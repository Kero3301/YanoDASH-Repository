<?php
# Evaluate if a user, given their permissions, is an admin
function is_admin(?array $permissions): bool {
    if (!is_array($permissions)) return false;
    return ($permissions['access_level'] ?? null) === 'admin';
}

# Evaluate if a user, given their permissions, is an editor
function is_editor(?array $permissions): bool {
    if (!is_array($permissions)) return false;
    return ($permissions['access_level'] ?? null) === 'editor';
}

# Evaluate if a user, given their permissions, can use the common DMS functions
function can_use_dms(?array $permissions): bool {
    if (!is_array($permissions)) return false;
    return is_admin($permissions) || is_editor($permissions);
}

# Evaluate if a user, given their permissions, can access the common admin pages
function can_access_admin_pages(?array $permissions): bool {
    if (!is_array($permissions)) return false;
    return is_admin($permissions);
}

# Evaluate if a user, given their identity and permissions, meets the override specification for president
function is_president(?array $identity, ?array $permissions): bool {
    if (!is_array($identity) || !is_array($permissions)) return false;

    $position = $identity['position'] ?? null;
    $accessLevel = $permissions['access_level'] ?? null;
    $accessDomains = $permissions['access_domains'] ?? [];
    
    return 
        strtoupper(trim($position)) === 'PRESIDENT' &&
        $accessLevel === 'admin' &&
        in_array('*', $accessDomains, true);
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
?>