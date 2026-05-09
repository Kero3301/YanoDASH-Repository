<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

require __DIR__ . '/../../vendor/autoload.php';

use MongoDB\Client;
use MongoDB\BSON\ObjectId;

function buildQuery($user, $access, $pageType = 'dms') {
    $client = new MongoDB\Client(getenv('YANODASH_V_DBU_URI'));
    $db = $client->yano_dash;

    $sessionOrgName = $user['organization'] ?? 'none';
    $orgDoc = $db->organizations_schema->findOne(['organization_name' => $sessionOrgName]);
    $userOffice = $orgDoc ? (string)$orgDoc->_id : 'none';

    $scopes = $user['scope_of_access'] ?? [];
    if (!in_array('read_docs', $scopes)) {
        return ['_id' => 'DENIED'];
    }

    $role = $user['access_level'] ?? 'viewer';
    $fullName = trim(($user['name']['first_name'] ?? '') . ' ' . ($user['name']['last_name'] ?? ''));

    if ($role === 'viewer' && $pageType !== 'public') {
        return ['_id' => 'DENIED'];
    }

    $finalQuery = [];

    if ($pageType === 'dms') {
        $finalQuery['doc_status'] = ['$nin' => ['APPROVED', 'ARCHIVED']];
    } elseif ($pageType === 'private') {
        $finalQuery['is_publicized'] = false;
        $finalQuery['doc_status'] = ['$in' => ['APPROVED', 'ARCHIVED']];
    } elseif ($pageType === 'public') {
        $finalQuery['is_publicized'] = true;
    }

    if ($role !== 'admin') {
        $finalQuery['$or'] = [
            ['area_of_origin' => $userOffice],
            ['author' => $fullName]
        ];
    }

    return $finalQuery;
}
?>