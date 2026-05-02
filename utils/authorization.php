<?php
    function can_use_dms(): bool {
        return 
            isset($_SESSION['auth']) # Logged in
            && ($_SESSION['auth']['access_level'] === 'admin' || $_SESSION['auth']['access_level'] === 'editor'); # Is an editor or an admin
    }
    
    function can_access_admin_pages(): bool {
        return 
            isset($_SESSION['auth']) # Logged in
            && $_SESSION['auth']['access_level'] === 'admin'; # Is an admin
    }

    function is_president(): bool {
        return 
            isset($_SESSION['auth'])
            && 
            (
                in_array(strtoupper($_SESSION['auth']['organization']), ['OBRERO STUDENT COUNCIL', 'OSC'], true)
                && 
                strtoupper($_SESSION['auth']['position']) === 'PRESIDENT'
            );
    }
?>