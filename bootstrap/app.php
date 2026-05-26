<?php
# Root directory definition
define('ROOT', dirname(__DIR__));

# Top-level require
require_once ROOT. '/vendor/autoload.php';                  # Require vendor autoloader for Composer libraries
use Dotenv\Dotenv;                                          # Use the Dotenv class for loading .env file

# Contract-based .env path definition
$prod_envPath = ROOT;                                       # Production path (deployment context)
$dev_envPath  = dirname(ROOT, 2) . '/apache';               # Development path (local dev context)

# Attempt to load .env file
$envLoaded = load_env($prod_envPath);                       # Initial Attempt: production path
if (!$envLoaded) $envLoaded = load_env($dev_envPath);       # Fallback Attempt: development path
if (!$envLoaded) {                                          # If both attempts fail, explicitly fail and log error
    $request = $_SERVER['REQUEST_URI'] ?? 'CLI/unknown';
    $host = $_SERVER['HTTP_HOST'] ?? 'unknown';
    error_log("[YanoDASH/CRITICAL] env load failed | host={$host} | request={$request}");

    # Show an error page to the user
    http_response_code(500);
    exit("500 Internal Server Error: Environment was not configured properly. Please contact a system administrator.");
}

# Session bootstrapping
if (session_status() !== PHP_SESSION_ACTIVE) session_start();

# Application URL resolution
$app_url = rtrim($_ENV['YD_APP_URL'] ?? getenv('YD_APP_URL') ?? $_SERVER['YD_APP_URL'] ?? '', '/');

if (!$app_url) {
    http_response_code(500);
    exit("500 Internal Server Error: YD_APP_URL is missing. Please contact a system administrator.");
}

if (!preg_match('#^https?://#', $app_url) || !filter_var($app_url, FILTER_VALIDATE_URL)) {
    http_response_code(500);
    exit("500 Internal Server Error: YD_APP_URL is malformed. Please contact a system administrator.");
}

# Subsequent requires
require_once ROOT. '/src/loader.php';                       # Require own lightweight source loader
require_once ROOT. '/src/services/auth_bootstrap.php';      # Require auth context bootstrapper
require_once ROOT. '/src/views/components/head.php';        # Require component for initializing page <head>

# Load a .env file from a directory
function load_env(string $dir, bool $debug = false): bool {
    # Return false early if the directory is invalidly formed
    if (!is_dir($dir)) {
        if ($debug) error_log("[dotenv/ERROR] path not found: " . $dir);
        return false;
    }

    # Attempt to load .env file from directory and return true, granted it is validly formed
    try {
        Dotenv::createImmutable($dir)->load();
        if ($debug) error_log("[dotenv/SUCCESS] loaded successfully");
        return true;
    } 
    # If loading fails, return false
    catch (Throwable $e) {
        error_log("[dotenv] failed: " . $e->getMessage());
        return false;
    }
}