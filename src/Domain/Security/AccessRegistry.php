<?php

final class AccessRegistry
{
    public const LEVELS = [
        'admin' => true,
        'editor' => true,
        'viewer' => true
    ];

    public const SCOPES = [
        'admin' => [
            'add_docs',
            'view_docs',
            'edit_docs',
            'delete_docs',
            'approve_docs',
            'archive_docs',
            'download_docs',
            'bookmark_docs',
            'manage_users',
            'manage_security'
        ],
        'editor' => [
            'add_docs',
            'view_docs',
            'edit_docs',
            'delete_docs',
            'download_docs',
            'bookmark_docs'
        ],
        'viewer' => [
            'view_docs',
            'download_docs',
            'bookmark_docs'
        ],
        'common' => [
            'view_docs',
            'download_docs'
        ]
    ];
}
?>