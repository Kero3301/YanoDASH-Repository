<?php
require_once dirname(__DIR__). '/iam/authentication.php';
require_once 'user_profile.php';

$profile = get_profile($_SESSION['user_id'] ?? null);
$identity = resolve_identity($_SESSION['user_id'] ?? null);
?>