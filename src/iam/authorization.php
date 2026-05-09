<?php
    require_once 'authentication.php';

    function is_admin(array $identity): bool {
        return isset($identity) && $identity['access_level'] === "admin";
    }

    function is_editor(array $identity): bool {
        return isset($identity) && $identity['access_level'] === "editor";
    }

    function can_use_dms(array $identity): bool {
        return is_admin($identity) || is_editor($identity);
    }
    
    function can_access_admin_pages($identity): bool {
        return is_admin($identity);
    }

    function is_president($identity): bool {
        return 
            is_admin($identity) 
            &&
            (
                in_array(strtoupper($identity['organization']), ['OBRERO STUDENT COUNCIL'], true)
                &&
                strtoupper($identity['position']) === 'PRESIDENT'
            );
    }
?>