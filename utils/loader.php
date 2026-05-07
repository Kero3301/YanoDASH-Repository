<?php
    # Definitions
    define('UTIL_BASE_PATH', __DIR__. '/');
    define('COMPONENT_BASE_PATH', dirname(UTIL_BASE_PATH). '/components/');
    define('ALLOWED_COMPONENTS', [
        'accordion',
        'button_list',
        'document_modal',
        'document_card',
        'document_list',
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
        'authentication',
        'authorization',
        'csrf_token',
        'document_factory',
        'document_fetcher',
        'schema_validator',
        'text_utils',
        
        'data/DocEd'
    ]);

    # Bootstrap inclusion of basic configurational files
    require_once dirname(UTIL_BASE_PATH). '/directory_config.php';
    require_once COMPONENT_BASE_PATH. 'head.php';

    # Loader function for loading a list of utils by name
    function load_utils(string ...$utils) {
        foreach ($utils as $util) {
            if ($util === 'loader') continue;
            if (!in_array($util, ALLOWED_UTILS, true)) continue;
            $utilPath = UTIL_BASE_PATH. $util. '.php';
            if (!is_file($utilPath)) continue;
            require_once $utilPath;
        }
    }

    # Loader function for loading a list of components by name
    function load_components(string ...$components) {
        foreach ($components as $component) {
            if ($component === 'head') continue;
            if (!in_array($component, ALLOWED_COMPONENTS, true)) continue;
            $componentPath = COMPONENT_BASE_PATH. $component. '.php';
            if (!is_file($componentPath)) continue;
            require_once $componentPath;
        }
    }
?>