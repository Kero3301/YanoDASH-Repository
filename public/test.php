<?php
require_once '../bootstrap/app.php';
load('mongodb', 'user_resolver', 'profile', 'user_context');

echo (Authorizer::validateIAM($_CURRENTUSER)? "IAM Context Valid": "IAM Context Invalid"). '<br>';
echo (Authorizer::isOSCPresident($_CURRENTUSER)? "OSC President": "Not OSC President"). '<br>';
echo (Authorizer::isOSCExecutive($_CURRENTUSER)? "OSC Executive": "Not OSC Executive"). '<br>';
echo (Authorizer::isOSCAdviser($_CURRENTUSER)? "OSC Adviser": "Not OSC Adviser"). '<br>';
echo (Authorizer::isOSCOfficial($_CURRENTUSER)? "OSC Official": "Not OSC Official"). '<br>';
echo (Authorizer::isAdmin($_CURRENTUSER)? "Admin": "Not Admin"). '<br>';
echo (Authorizer::isEditor($_CURRENTUSER)? "Editor": "Not Editor"). '<br>';
echo (Authorizer::isCouncilOfficial($_CURRENTUSER)? "Council Official": "Not Council Official"). '<br>';
echo (Authorizer::canUseDMS($_CURRENTUSER)? "Can Use DMS": "Cannot Use DMS"). '<br>';
echo (Authorizer::canAccessAdminPages($_CURRENTUSER)? "Can Access Admin Pages": "Cannot Access Admin Pages"). '<br>';

// $userContext = UserResolver::resolve($_SESSION['user_id'] ?? null);
$results = QueryRunner::tryWithCollections([
    ($C1='documents')
        => fn ($C1)=> $C1->find(['doc_status' => 'ARCHIVED'])->execute(),
    ($C2='archive_requests')
        => fn ($C2)=> $C2->find(['status' => 'approved'])->execute(),
    ($C3='accounts')
        => fn ($C3)=> $C3->findOne(['name.first_name' => 'Alex'])->execute()])
    ->getResults();
?>
<!DOCTYPE html>
<html>
    <head>
        <?php initialize_page('Batch Query Test');?>
    </head>
    <body>
        <pre style="font-family: monospace; background: lightgray; padding: 8px; border-radius: 16px; width: max-content">
            <?php var_dump(UserContext::constructFromUID($_SESSION['user_id'] ?? null))?>
        </pre>
    </body>
</html>