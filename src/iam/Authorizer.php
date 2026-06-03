<?php
final class Authorizer {
    # ====== SECTION 0.0: BUSINESS CONSTANTS ======

    private const COUNCIL_IDS = [
        '69f88cdcd1dd355cb895ded2' => true,
        '69f8b706e21ef9d721f4ce8b' => true,
        '69f8b779e21ef9d721f4ce8d' => true,
        '6a041b8736f5cdac75349564' => true,
        '6a041b8736f5cdac75349565' => true,
        '6a041b8736f5cdac75349566' => true,
        '6a041b8736f5cdac75349567' => true,
        '6a041b8736f5cdac75349568' => true
    ];
    
    private const OSC_OFFICES = [
        'osc_president_office' => true,
        'osc_ivp_office' => true,
        'osc_evp_office' => true,
        'osc_gensec_office' => true,
        'osc_genaud_office' => true,
        'osc_gentreas_office' => true,
        'osc_genpio_office' => true
    ];

    private const OSC_EXECUTIVES = [
        'osc_president' => true,
        'osc_ivp' => true,
        'osc_evp' => true,
        'osc_gensec' => true,
        'osc_genaud' => true,
        'osc_gentreas' => true,
        'osc_genpio' => true
    ];

    private const OSC_EXECUTIVE_MAP = [
        'osc_president_office' => 'osc_president',
        'osc_ivp_office'       => 'osc_ivp',
        'osc_evp_office'       => 'osc_evp',
        'osc_gensec_office'    => 'osc_gensec',
        'osc_genaud_office'    => 'osc_genaud',
        'osc_gentreas_office'  => 'osc_gentreas',
        'osc_genpio_office'    => 'osc_genpio'
    ];

    # ====== SECTION 0.1: ACCESS MANAGEMENT CONSTANTS ======

    public const ACCESS_LEVELS = [
        'admin' => true,
        'editor' => true,
        'viewer' => true
    ];

    public const ACCESS_SCOPES = [
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

    # ====== SECTION 1: INTEGRITY CHECKS ======
    # Evaluate if a given user's IAM context is valid
    public static function validateIAM(mixed $user): bool {
        # Ignore non-arrays
        if (!is_array($user)) return false;
        # POSTCONDITIONS: User is an array and can be worked with

        # Gate for basic top-level structure compliance
        if (!is_array($user['IDENTITY'] ?? null) || !is_array($user['PERMISSIONS'] ?? null)) return false;
        # POSTCONDITIONS: User array contains both IDENTITY and PERMISSION arrays

        # Verify that email exists in the IDENTITY array and is valid
        if (!isset($user['IDENTITY']['email'])) return false;
        if (!filter_var($user['IDENTITY']['email'], FILTER_VALIDATE_EMAIL)) return false;
        # POSTCONDITIONS: User IDENTITY array has a valid non-null email

        # Top-level array key existence check for nullable IDENTITY fields
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
        if (isset(self::COUNCIL_IDS[$orgID]) && (
            is_null($dept) || 
            is_null($position))
        ) return false;
        # POSTCONDITIONS: Only non-null department and position values are allowed for council members

        # Top-level set check for non-nullable PERMISSIONS fields
        if (
            !isset($user['PERMISSIONS']['access_level']) ||
            !isset($user['PERMISSIONS']['access_scope']) ||
            !isset($user['PERMISSIONS']['access_domains'])
        ) return false;
        # POSTCONDITIONS: Access level, scope, and domains are set with non-null values

        # Prevent non-admin, non-editor, and non-viewer access level types
        if (!isset(self::ACCESS_LEVELS[$user['PERMISSIONS']['access_level']])) return false;
        # POSTCONDITIONS: User is any one of the following: admin, editor, viewer

        # Type validity checks for access scope and domains
        if (!is_array($user['PERMISSIONS']['access_scope']) || !is_array($user['PERMISSIONS']['access_domains'])) return false;
        # POSTCONDITIONS: User's access scope and domains are valid arrays

        # Prevent incorrect or non-matching access scopes
        $expected = self::ACCESS_SCOPES[$user['PERMISSIONS']['access_level']];
        $actual = $user['PERMISSIONS']['access_scope'];
        sort($expected);
        sort($actual);
        if ($expected !== $actual) return false;
        # POSTCONDITIONS: User's access scopes are valid according to access level type

        return true;
    }


    # ====== SECTION 2: CAPABILITY CHECKS ======
    # Evaluate if a user belongs to a legitimate council
    public static function isCouncilOfficial(mixed $user): bool {
        if (!Authorizer::validateIAM($user)) return false;
        return isset(self::COUNCIL_IDS[$user['IDENTITY']['org_id']]);
    }

    # Evaluate if a user is a legitimate OSC official
    public static function isOSCOfficial(mixed $user): bool {
        if (!Authorizer::isCouncilOfficial($user)) return false;
        return $user['IDENTITY']['org_id'] === '69f88cdcd1dd355cb895ded2';
    }

    # Evaluate if a user is a legitimate OSC Adviser
    public static function isOSCAdviser(mixed $user): bool {
        if (!Authorizer::isOSCOfficial($user)) return false;
        return 
            trim($user['IDENTITY']['position']) === 'osc_adviser' &&
            Authorizer::hasAdminClaims($user);
    }

    # Evaluate if a user is a legitimate OSC Executive
    public static function isOSCExecutive(mixed $user): bool {
        if (!Authorizer::isOSCOfficial($user)) return false;
        $dept = trim($user['IDENTITY']['department']);
        $position = trim($user['IDENTITY']['position']);
        $mapPosition = self::OSC_EXECUTIVE_MAP[$dept] ?? null;
        $hasMapMatch = ($mapPosition === $position);
        $hasAdminClaims = Authorizer::hasAdminClaims($user);
        $accessDomains = $user['PERMISSIONS']['access_domains'] ?? [];
        $hasWildcardAccess = in_array('*', $accessDomains, true);
        $hasExactDeptScope =
            is_array($accessDomains) &&
            count($accessDomains) === 1 &&
            $accessDomains[0] === $dept;

        if ($position === 'osc_president') 
            return $hasMapMatch && $hasAdminClaims && $hasWildcardAccess;
        return $hasMapMatch && $hasAdminClaims && $hasExactDeptScope;
    }

    # Evaluate if a user is a legitimate OSC President
    public static function isOSCPresident(mixed $user): bool {
        if (!Authorizer::isOSCExecutive($user)) return false;
        $dept = $user['IDENTITY']['department'];
        $position = $user['IDENTITY']['position'];
        $accessDomains = $user['PERMISSIONS']['access_domains'];
        return 
            trim($dept) === 'osc_president_office' &&
            trim($position) === 'osc_president' &&
            in_array('*', $accessDomains, true);
    }

    # Evaluate if the user is a genuine Admin-level user
    public static function isAdmin(mixed $user): bool {
        if (Authorizer::isOSCPresident($user)) return true;
        return 
            Authorizer::isOSCExecutive($user) ||
            Authorizer::isOSCAdviser($user);
    }

    # Evaluate if the user is a genuine Editor-level user
    public static function isEditor(mixed $user): bool {
        if (!Authorizer::isCouncilOfficial($user)) return false;
        if (!Authorizer::hasEditorClaims($user)) return false;
        $dept = $user['IDENTITY']['department'];
        $accessDomains = $user['PERMISSIONS']['access_domains'] ?? [];
        $hasExactDeptScope = 
            is_array($accessDomains) &&
            count($accessDomains) === 1 &&
            $accessDomains[0] === $dept;
        return $hasExactDeptScope;
    }

    # Evaluate if the user is a genuine Viewer-level user
    public static function isViewer(mixed $user): bool {
        if (!Authorizer::validateIAM($user)) return false;
        $permissions = $user['PERMISSIONS'];
        if (trim($permissions['access_level']) !== 'viewer') return false;
        $expected = array_flip(self::ACCESS_SCOPES['viewer']);
        $actual = array_flip($permissions['access_scope']);
        return 
            empty(array_diff_key($expected, $actual)) &&
            empty(array_diff_key($actual, $expected));
    }

    # Evaluate if a user, given their permissions, has admin claims
    private static function hasAdminClaims(mixed $user): bool {
        if (!Authorizer::validateIAM($user)) return false;
        $permissions = $user['PERMISSIONS'];
        if (trim($permissions['access_level']) !== 'admin') return false;
        $expected = array_flip(self::ACCESS_SCOPES['admin']);
        $actual = array_flip($permissions['access_scope']);
        return 
            empty(array_diff_key($expected, $actual)) && 
            empty(array_diff_key($actual, $expected));
    }

    # Evaluate if a user, given their permissions, has editor claims
    private static function hasEditorClaims(mixed $user): bool {
        if (!Authorizer::validateIAM($user)) return false;
        $permissions = $user['PERMISSIONS'];
        if (trim($permissions['access_level']) !== 'editor') return false;
        $expected = array_flip(self::ACCESS_SCOPES['editor']);
        $actual = array_flip($permissions['access_scope']);
        return 
            empty(array_diff_key($expected, $actual)) && 
            empty(array_diff_key($actual, $expected));
    }

    # Evaluate if a user, given their permissions, can use the common DMS functions
    public static function canUseDMS(mixed $user): bool {
        return Authorizer::isAdmin($user) || Authorizer::isEditor($user);
    }

    # Evaluate if a user, given their permissions, can access the common admin pages
    public static function canAccessAdminPages(mixed $user): bool {
        return Authorizer::isAdmin($user);
    }

    # Evaluate if a user can access a specific resource or perform a specific action
    public static function can(mixed $user, mixed $requirements): bool {
        # Verify that passed requirements is an array
        if (!is_array($requirements)) return false;
        # POSTCONDITIONS: Requirements is an array

        # Allow only two array members for requirements to avoid mixing arbitrary requirements
        if (count($requirements) !== 2) return false;
        # POSTCONDITIONS: Requirements has exactly 2 elements

        # Ensure both elements are arrays
        if (!is_array($requirements['scopes'] ?? null) || !is_array($requirements['domains'] ?? null)) return false;
        # Store the required scope and domain arrays
        $requiredScopes = $requirements['scopes'];
        $requiredDomains = $requirements['domains'];
        # POSTCONDITIONS: Both required scopes and domains are arrays
        # CONTRACT: 
        #   requiredScopes may be [], which means there are NO scope restrictions
        #   requiredDomains may be [], which means there are NO domain restrictions

        # Open-access (guest) override if there are no scope and domain restrictions
        if (count($requiredScopes) === 0 && count($requiredDomains) === 0) return true;
        # POSTCONDITIONS: There is at least 1 restriction in either array
        
        # Ensure there aren't too many specified scope and domain requirements
        if (count($requiredScopes) > 16) return false;      # Too many required scopes
        if (count($requiredDomains) > 64) return false;     # Too many required domains
        # POSTCONDITIONS: There are an adequate number of required scopes and domains to safely process
        # CONTRACT:
        #   requiredScopes may only contain up to 16 elements
        #   requiredDomains may only contain up to 64 elements

        # Ensure invalid scopes and domains (i.e. non-strings) are rejected
        foreach ($requiredScopes as $s) if (!is_string($s)) return false;   # A non-string scope has been found
        foreach ($requiredDomains as $d) if (!is_string($d)) return false;  # A non-string domain has been found
        # POSTCONDITIONS: The elements of both required scopes and domains are all valid strings
        # CONTRACT: 
        #   requiredScopes and requiredDomains may only accept string values as elements
        
        # If explicit restrictions are specified, evaluate whether they still count as open-access restrictions or not
        foreach ($requiredScopes as $s) if (!in_array($s, self::ACCESS_SCOPES['common'], true)) return (
            # If a required access scope has been found to no longer belong to the common scope set, allow ONLY IF the user IAM context is validated and contains that scope
            Authorizer::validateIAM($user) &&
            in_array($s, $user['PERMISSIONS']['access_scope']) &&
            (
                count($requiredDomains) === 0 ||                # No required domain
                in_array('public', $requiredDomains) ||         # Same as above
                (                                               # Required domain with presidential override
                    !empty(array_intersect($requiredDomains, $user['PERMISSIONS']['access_domains'])) ||
                    Authorizer::isOSCPresident($user)
                )
            )
        );
        if (
            count($requiredDomains) === 0 ||
            !empty(array_intersect($requiredDomains, ['public']))
        ) return true;        
        # POSTCONDITIONS: The resource or action is definitively not open-access and the user is not a guest, therefore user IAM context must be verified 
        
        # Validate the user's IAM context
        if (!Authorizer::validateIAM($user)) return false;
        # POSTCONDITION: The user's IAM context is validated; PERMISSIONS associated array exists with its baseline schema and is safe to access
    
        # Read the user's IAM context information for PERMISSIONS information, namely: access scope and domains
        $permissions = $user['PERMISSIONS'];
            $accessScope = $permissions['access_scope'] ?? self::ACCESS_SCOPES['viewer'];
            $accessDomains = $permissions['access_domains'] ?? ['public'];

        # Perform scope and domain checks
        # Scope check: required scopes are cumulative; all action types defined in required scopes MUST be within the user's access scope  
        foreach ($requiredScopes as $s) if (!in_array($s, $accessScope, true)) return false;
        # POSTCONDITIONS: The user's access scope meets the minimum required scope

        # Domain check: required domains MUST have an intersection with the user's access domains
        # Presidential override: OSC President can access ANY domain
        if (Authorizer::isOSCPresident($user)) return true;
        # POSTCONDITIONS: User is not an OSC President, therefore domains must be verified

        # Check if domain is permitted for access
        $domainAllowed = 
            count($requiredDomains) === 0 ||                                # No required domains, open to all domains
            in_array('public', $requiredDomains, true) ||                   # Same concept as above semantically
            !empty(array_intersect($requiredDomains, $accessDomains));      # User's access domain(s) is/are listed in the required domains
        return $domainAllowed;
    }

    # Templates for specific authorship rules
    public static function canAuthor(mixed $docCategory, mixed $user): bool {
        if (!Authorizer::validateIAM($user)) return false;
        if (!is_string($docCategory)) return false;

        $isCouncilOfficial = Authorizer::isCouncilOfficial($user);
        $isOSCOfficial = Authorizer::isOSCOfficial($user);
        $isHOROfficial = $isOSCOfficial && trim($user['IDENTITY']['department']) === 'osc_hor';
        $isOSCExec = Authorizer::isOSCExecutive($user);
        $isOSCGenSecOfficial = ($isOSCOfficial && trim($user['IDENTITY']['department']) === 'osc_gensec_office');
        $isOSCGenSec = ($isOSCGenSecOfficial && trim($user['IDENTITY']['position']) === 'osc_gensec');
        $isOSCGenTreas = ($isOSCExec && trim($user['IDENTITY']['position']) === 'osc_gentreas');

        $canAuthorCategory = match 
        (strtoupper(trim($docCategory))) {
            default => false,
            'ACTIVITY_DESIGN', 'ATTENDANCE' => $isOSCGenSecOfficial,
            'ACCOMPLISHMENT_REPORT', 'MEETING_MINUTES', 'MEETING_NOTICE' => $isOSCGenSec,
            'FINANCIAL_STATEMENT' => $isOSCGenTreas,
            'MEMORANDUM' => $isOSCExec,
            'PROJECT_PROPOSAL' => $isCouncilOfficial,
            'MERCH_DOC' => $isHOROfficial
        };
        return $canAuthorCategory;
    }
}
?>