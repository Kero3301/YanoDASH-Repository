<?php
require_once 'user_profile.php';
require_once dirname(__DIR__). '/iam/identity_resolver.php';
require_once dirname(__DIR__). '/iam/permission_resolver.php';

# Safety guard for session status
if (session_status() !== PHP_SESSION_ACTIVE) session_start();

# Store the user ID for quicker access
$USER_ID = $_SESSION['user_id'] ?? null;

# Assume guest/null user by default
$profile = null;
$identity = null;
$permissions = null;

# Populate data only if USER_ID is truly set
if ($USER_ID !== null) {
    $profile = get_profile($USER_ID);
    $identity = resolve_identity($USER_ID);
    $permissions = resolve_permissions($USER_ID);
}
?>