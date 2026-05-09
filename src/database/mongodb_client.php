<?php
    require_once dirname(dirname(__DIR__)). '/vendor/autoload.php';

    # Gets the MongoDB database client for initiating connection, or creates one if there isn't
    function mongodb_client(bool $readOnly = true): MongoDB\Client {
        # Caching for performance
        static $cache = [];

        # Define the key used in caching
        $key = $readOnly ? 'v' : 'rw';

        # Return cached client if it is already set
        if (isset($cache[$key])) return $cache[$key];
        
        # Decide whether to use the view-only or read-write URI, depending on whether $readOnly is true or false
        $envKey = $readOnly
            ? 'YANODASH_V_DBU_URI'
            : 'YANODASH_RW_DBU_URI';

        # Define the URI needed for client connection
        $uri = getenv($envKey);

        # Fail if MongoDB connection string was not found
        if (!$uri)
            throw new RuntimeException("Missing MongoDB URI: $envKey");

        # Return the client
        return $cache[$key] = new MongoDB\Client($uri);
    }
?>