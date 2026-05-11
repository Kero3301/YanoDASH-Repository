<?php
require_once dirname(__DIR__, 2). '/vendor/autoload.php';

# Gets the MongoDB database client for initiating connection, or creates one if there isn't
function mongodb_client(bool $readOnly = false): MongoDB\Client {
    # Caching for performance
    static $cache = [];
    $key = $readOnly ? 'v' : 'rw';
    if (isset($cache[$key])) return $cache[$key];
    
    # Determine the URI to be used for client connection, depending on whether $readOnly is true or false
    $envKey = $readOnly
        ? 'YANODASH_V_DBU_URI'
        : 'YANODASH_RW_DBU_URI';
    $uri = getenv($envKey);
    if (!$uri) throw new RuntimeException("Missing MongoDB URI: $envKey");

    # Return the client
    return $cache[$key] = new MongoDB\Client($uri);
}
?>