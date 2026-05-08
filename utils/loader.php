<?php
    # Definitions
    define('UTIL_BASE_PATH', __DIR__. '/');
    define('COMPONENT_BASE_PATH', dirname(UTIL_BASE_PATH). '/components/');
    define('VENDOR_BASE_PATH', dirname(__DIR__). '/vendor/');

    # Bootstrap inclusion of basic configurational files
    require_once UTIL_BASE_PATH. 'directory.php';
    require_once COMPONENT_BASE_PATH. 'head.php';

    # Safe whitelist of allowed components and utils
    define('ALLOWED_COMPONENTS', [
        'accordion',
        'document_card',
        'document_list',
        'document_modal',
        'filter_chips',
        'footer',
        'head',
        'main_section',
        'menu',
        'navbar',
        'page_header',
        'password_input',
        'sliding_switch',
        'user_form',
    ]);
    define('ALLOWED_UTILS', [        
        'data/DocEd',
        'authentication',
        'authorization',
        'autoload',
        'csrf_token',
        'directory',
        'doc_query',
        'document_factory',
        'document_fetcher',
        'edit_logic',
        'routing',
        'schema_validator',
        'text_utils',
    ]);

    # Loader function for loading a list of components by name
    function load_components(string ...$components) {
        foreach ($components as $component) {
            # Ignore non-string values
            if (!is_string($component)) continue;
            # Ignore loading of non-whitelisted components
            if (!in_array($component, ALLOWED_COMPONENTS, true)) continue;

            $componentPath = COMPONENT_BASE_PATH. $component. '.php';
            # Ignore if the component does not have a valid file
            if (!is_file($componentPath)) continue;

            require_once $componentPath;
        }
    }

    # Loader function for loading a list of utils by name
    function load_utils(string ...$utils) {
        foreach ($utils as $util) {
            # Ignore non-string values
            if (!is_string($util)) continue;
            # Ignore loading of non-whitelisted utils
            if (!in_array($util, ALLOWED_UTILS, true)) continue;

            $utilPath = ($util === 'autoload'? VENDOR_BASE_PATH : UTIL_BASE_PATH). $util. '.php';
            # Ignore if the util does not have a valid file
            if (!is_file($utilPath)) continue;

            require_once $utilPath;
        }
    }
?>