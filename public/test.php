<?php
require_once '../bootstrap/app.php';
load('mongodb', 'user_resolver', 'profile', 'user_context');

$userContext = UserResolver::resolve($_SESSION['user_id'] ?? null);
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