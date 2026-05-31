<?php
require_once dirname(__DIR__). '/database/mongodb.php';

final class IAMContextValidator {
    # true: user is valid and may exist
    # false: user is invalid or there is no user
    public static function validate(mixed $user): bool {
        # Ignore non-arrays
        if (!is_array($user)) return false;
        # POSTCONDITIONS: User is an array and can be worked with

        # Top-level check for basic structure
        if (!is_array($user['IDENTITY'] ?? null) || !is_array($user['PERMISSIONS'] ?? null)) return false;
        # POSTCONDITIONS: User contains both IDENTITY and PERMISSIONS arrays

        # Verify that email exists in the IDENTITY array
        if (!isset($user['IDENTITY']['email'])) return false;
        # POSTCONDITIONS: User IDENTITY array has a non-null email

        # Top-level array key existence checks for nullable IDENTITY fields
        if (
            !array_key_exists('org_id', $user['IDENTITY']) ||
            !array_key_exists('department', $user['IDENTITY']) ||
            !array_key_exists('position', $user['IDENTITY'])
        ) return false;
        # POSTCONDITIONS: org_id, department, and position exist in the user IDENTITY array
        
        # Store nullable field types temporarily for checking
        $orgID = $user['IDENTITY']['org_id'];
        $dept = $user['IDENTITY']['department'];
        $position = $user['IDENTITY']['position'];

        # Verify orgID type validity
        if (!is_null($orgID) && !valid_oid(oid($orgID))) return false;    
        # POSTCONDITIONS: orgID is either null or a valid stringified ObjectId
        
        # Verify type validity for dept and position
        if (!is_null($dept) && !is_string($dept)) return false;
        if (!is_null($position) && !is_string($position)) return false;
        # POSTCONDITIONS: User department and position are either null or valid strings

        # Prevent council members from having null departments and positions
        $councils = [
            '69f88cdcd1dd355cb895ded2' => true,
            '69f8b706e21ef9d721f4ce8b' => true,
            '69f8b779e21ef9d721f4ce8d' => true,
            '6a041b8736f5cdac75349564' => true,
            '6a041b8736f5cdac75349565' => true,
            '6a041b8736f5cdac75349566' => true,
            '6a041b8736f5cdac75349567' => true,
            '6a041b8736f5cdac75349568' => true
        ];
        if (isset($councils[$orgID]) && (is_null($dept) || is_null($position))) return false;
        # POSTCONDITIONS: Only non-null department and position values are allowed for council members
        
        # Top-level set check for non-nullable PERMISSIONS fields
        if (
            !isset($user['PERMISSIONS']['access_level']) ||
            !isset($user['PERMISSIONS']['access_scope']) ||
            !isset($user['PERMISSIONS']['access_domains'])
        ) return false;
        # POSTCONDITIONS: Access level, scope, and domains are set with non-null values

        # Prevent non-admin, non-editor, and non-viewer access level types
        $accessLevels = [
            'admin' => true, 
            'editor' => true, 
            'viewer' => true
        ];
        if (!isset($accessLevels[$user['PERMISSIONS']['access_level']])) return false;
        # POSTCONDITIONS: User is any one of the following: admin, editor, viewer

        # Type validity checks for access scope and domains
        if (!is_array($user['PERMISSIONS']['access_scope']) || !is_array($user['PERMISSIONS']['access_domains'])) return false;
        # POSTCONDITIONS: User's access scope and domains are valid array types

        # Prevent incorrect or non-matching access scopes
        $accessScopes = [
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
            ]
        ];
        $expected = $accessScopes[$user['PERMISSIONS']['access_level']];
        $actual = $user['PERMISSIONS']['access_scope'];
        sort($expected);
        sort($actual);
        if ($expected !== $actual) return false;
        # POSTCONDITIONS: User's access scopes are valid according to access level type

        return true;
    }
}
?>