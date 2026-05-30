<?php
require_once dirname(__DIR__). '/database/mongodb.php';

# Validate an identity array
function validate_identity(mixed $identity) {
    $isUserIDValid = valid_oid(oid($identity['user_id'] ?? null));
    $isEmailValid = isset($identity['email']);
    $isOrgIDValid = array_key_exists('org_id', $identity) && 
        (valid_oid(oid($identity['org_id'])) || $identity['org_id'] === null);
    $isDepartmentValid = array_key_exists('department', $identity);
    $isPositionValid = array_key_exists('position', $identity);

    return
        is_array($identity) &&
        $isUserIDValid &&
        $isEmailValid &&
        $isOrgIDValid &&
        $isDepartmentValid &&
        $isPositionValid;
}

# Validate a permissions array
function validate_permissions(mixed $permissions) {
    
}
?>