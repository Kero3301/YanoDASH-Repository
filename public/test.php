<?php
require_once '../bootstrap/app.php';
load('mongodb', 'user_resolver', 'profile', 'document_list', 'user_context', 'doc_query');

echo (Authorizer::validateIAM($_CURRENTUSER)? "IAM Context Valid": "IAM Context Invalid"). '<br>';
echo (Authorizer::isOSCPresident($_CURRENTUSER)? "OSC President": "Not OSC President"). '<br>';
echo (Authorizer::isOSCExecutive($_CURRENTUSER)? "OSC Executive": "Not OSC Executive"). '<br>';
echo (Authorizer::isOSCAdviser($_CURRENTUSER)? "OSC Adviser": "Not OSC Adviser"). '<br>';
echo (Authorizer::isOSCOfficial($_CURRENTUSER)? "OSC Official": "Not OSC Official"). '<br>';
echo (Authorizer::isAdmin($_CURRENTUSER)? "Admin": "Not Admin"). '<br>';
echo (Authorizer::isEditor($_CURRENTUSER)? "Editor": "Not Editor"). '<br>';
echo (Authorizer::isViewer($_CURRENTUSER)? "Viewer": "Not Viewer"). '<br>';
echo (Authorizer::isCouncilOfficial($_CURRENTUSER)? "Council Official": "Not Council Official"). '<br>';
echo (Authorizer::canUseDMS($_CURRENTUSER)? "Can Use DMS": "Cannot Use DMS"). '<br>';
echo (Authorizer::canAccessAdminPages($_CURRENTUSER)? "Can Access Admin Pages": "Cannot Access Admin Pages"). '<br>';

?>
<!DOCTYPE html>
<html>
    <head>
        <?php initialize_page('Batch Query Test');?>
    </head>
    <body>
        <!-- <pre style="font-family: monospace; background: lightgray; padding: 8px; border-radius: 16px; width: max-content"> -->
            <?php /* var_dump(UserContext::constructFromUID($_SESSION['user_id'] ?? null)) */ ?>
            <?php
                $docs = DocQuery::get(fn ($_)=> $_->find(['doc_status' => 'EDITING'])->execute());
                list_all_documents($docs);
            ?>
        <!-- </pre> -->
    </body>
</html>