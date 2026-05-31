<?php
require_once dirname(__DIR__). '/src/services/UserContext.php';

# Safety guard for session status
if (session_status() !== PHP_SESSION_ACTIVE) session_start();

# Resolve current user context from session user_id if it is not null
$_CURRENTUSER = UserContext::constructFromUID($_SESSION['user_id'] ?? null);
?>