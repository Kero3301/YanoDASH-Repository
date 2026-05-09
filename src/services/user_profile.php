<?php
require_once dirname(dirname(__DIR__)). '/vendor/autoload.php';
require_once dirname(__DIR__). '/database/mongodb_client.php';
require_once dirname(__DIR__). '/utils/schema_validator.php';

function get_profile(?string $userID): ?array {
    # Ignore if user ID is null
    if (!$userID) return null;

    # Caching
    static $cache = [];

    # User ID storage
    $_id = new MongoDB\BSON\ObjectId($userID);

    # Cached return
    $cacheKey = (string) $_id;
    if (isset($cache[$cacheKey])) return $cache[$cacheKey];

    # Client and collections
    $client = mongodb_client();
    $collection_accounts = $client->yano_dash->account_schema;
    $collection_organizations = $client->yano_dash->organizations_schema;

    # Query
    $condition_profile = ["_id" => $_id];
    $profile = $collection_accounts->findOne($condition_profile);
    if (!$profile || !baseline_schema_validate($profile, 'ACCOUNTS')) return null;

    # Organization resolution
    $organization_name = null;
    if (!empty($identity->organization)) {
        $org = $collection_organizations->findOne(["_id" => $profile->organization]);
        if ($org) $organization_name = $org->organization_name ?? "None";
    }

    #Construction
    $result = [
        'name' => $profile->name->getArrayCopy(),
        'student_id_number' => $profile->student_id_number ?? "(unknown)",
        'position' => $profile->position,
        'doc_bookmarks' => $profile->doc_bookmarks ?? null,
        'avatar_url' => $profile->avatar_url ?? ""
    ];

    # Result caching and return
    return $cache[$cacheKey] = $result;
}

# Gets the full name of the user
function full_name(?array $profile): string {
    if ($profile === null) return 'unknown';
    $name = $profile['name'] ?? [];
    return trim(implode(' ', array_filter([
        $name['first_name'] ?? '',
        $name['middle_name'] ?? '',
        $name['last_name'] ?? ''
    ]))) ?: 'Guest';
}

# Gets the student ID number of the user
function student_id_number(?array $profile): string {
    if ($profile === null) return 'unknown';
    return trim($profile['student_id_number'] ?? 'unknown') ?: 'unknown';
}

function position(?array $profile): string {
    if ($profile === null) return 'unknown';
    return trim($profile['position'] ?? 'unknown') ?: 'unknown';
}

# Gets the avatar (profile picture) of the user
function avatar(?array $profile): array {
    if (!is_array($profile)) 
        return ['type' => 'initials', 'value' => ''];
    
    $avatar = $profile['avatar_url'] ?? '';
    if (is_string($avatar) && trim($avatar) !== '') 
        return ['type' => 'url', 'value' => $avatar];
    
    return ['type' => 'initials', 'value' => initials($profile)];
}

# Gets the initials of the user's name
function initials(?array $profile): string {
    if ($profile === null) return '';
    $name = $profile['name'] ?? [];
    $first = $name['first_name'] ?? '';
    $last  = $name['last_name'] ?? '';
    return (
        ($first[0] ?? '') .
        ($last[0] ?? '')
    );
}
?>