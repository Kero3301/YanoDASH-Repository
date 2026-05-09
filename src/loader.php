<?php
# Definitions
define('SOURCES_PATH', __DIR__);
define('COMPONENT_BASE_PATH', SOURCES_PATH. '/views/components/');
define('VENDOR_BASE_PATH', dirname(__DIR__). '/vendor/');

# Bootstrap inclusion of basic configurational files
require_once SOURCES_PATH. '/../config/directory.php';
require_once SOURCES_PATH. '/services/auth_bootstrap.php';
require_once SOURCES_PATH. '/views/components/head.php';

# Safe whitelist of allowed sources to prevent arbitrary file inclusion
define('ALLOWED_SOURCES', [
    # Non-components
    'vendor_autoload' => '../vendor/autoload',        
    'mongodb_client' => 'database/mongodb_client',
    'authentication' => 'iam/authentication',
    'authorization' => 'iam/authorization',
    'doc_ed' => 'models/DocEd',
    'user_profile' => 'models/UserProfile',
    'auth_bootstrap' => 'services/auth_bootstrap',
    'user_profile_service' => 'services/user_profile',
    'csrf_token' => 'utils/csrf_token',
    'doc_query' => 'utils/doc_query',
    'document_factory' => 'utils/document_factory',
    'document_fetcher' => 'utils/document_fetcher',
    'routing' => 'utils/routing',
    'schema_validator' => 'utils/schema_validator',
    'text_utils' => 'utils/text_utils',
    
    # Components
    'accordion' => 'views/components/accordion',
    'document_card' => 'views/components/document_card',
    'document_list' => 'views/components/document_list',
    'document_modal' => 'views/components/document_modal',
    'filter_chips' => 'views/components/filter_chips',
    'footer' => 'views/components/footer',
    'main_section' => 'views/components/main_section',
    'menu' => 'views/components/menu',
    'navbar' => 'views/components/navbar',
    'page_header' => 'views/components/page_header',
    'password_input' => 'views/components/password_input',
    'sliding_switch' => 'views/components/sliding_switch',
    'user_form' => 'views/components/user_form'
]);

# Function to load a list of sources by name
function load(string ...$sources) {
    foreach (array_unique($sources) as $source) {
        # Ignore non-whitelisted sources
        if (!array_key_exists($source, ALLOWED_SOURCES)) continue;
        
        # Build absolute path
        $path = SOURCES_PATH . '/'. ALLOWED_SOURCES[$source]. '.php';

        # Load only if file exists
        if (file_exists($path)) require_once $path;
    }
}
?>