<?php
    if (!defined('APP_URL')) {
        $url = getenv('APP_URL') ?: 'http://localhost/yanodash-repository/public';

        if (!preg_match('#^https?://#', $url))
            throw new RuntimeException('APP_URL must include http:// or https://');

        $url = rtrim($url, '/');
        if (!filter_var($url, FILTER_VALIDATE_URL))
            throw new RuntimeException("Invalid APP_URL: {$url}");

        define('APP_URL', $url);
    }

    $app_url = APP_URL;
?>