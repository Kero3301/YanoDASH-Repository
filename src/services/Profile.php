<?php
require_once dirname(__DIR__). '/database/mongodb.php';
require_once dirname(__DIR__). '/iam/IAMContextValidator.php';

final class Profile {
    public static function resolve(mixed $user, mixed $userID) {
        if (!IAMContextValidator::validate($user) || !valid_oid(oid($userID))) return null;

        static $cache = [];
        if (isset($cache[$key = (string)$userID])) return $cache[$key];

        $uid = oid($userID);
        $account = QueryRunner::tryWithCollections([
            ($C1="accounts")
                => fn ($C1)=> $C1->findOne(['_id' => $uid])->execute()
        ])->getResults($C1);
        if (empty($account)) return null;

        $NAME = $account['name'];

        $STUDENT_ID_NUMBER = "(unknown)";
        if (isset($account['student_id_number'])) $STUDENT_ID_NUMBER = $account['student_id_number'];

        $ORG_NAME = "(none/unknown)";
        if (!is_null(oid($user['IDENTITY']['org_id']))) {
            $orgID = oid($user['IDENTITY']['org_id']);
            $org = QueryRunner::tryWithCollections([
                ($C2="organizations")
                    => fn ($C2)=> $C2->findOne(['_id' => $orgID])->execute()  
            ])->getResults($C2);
            if (!empty($org)) $ORG_NAME = $org['organization_name'] ?? '(none/unknown)';
        }

        $AVATAR_URL = null;
        if (array_key_exists('avatar_url', $account)) {

        }

        $DOC_BOOKMARKS = [];
        if (isset($account['doc_bookmarks']) && is_array($account['doc_bookmarks'])) 
            $DOC_BOOKMARKS = $account['doc_bookmarks'];

        $DATE_JOINED = "(unknown)";
        if (isset($account['date_joined'])) 
            $DATE_JOINED = (new DateTime($account['date_joined']))->setTimezone(new DateTimeZone('Asia/Manila'))->format('M d Y, g:i A');

        $result = [
            "name" => $NAME,
            "student_id_number" => $STUDENT_ID_NUMBER,
            "org_name" => $ORG_NAME,
            "avatar_url" => $AVATAR_URL,
            "doc_bookmarks" => $DOC_BOOKMARKS,
            "date_joined" => $DATE_JOINED
        ];
        return $cache[$key] = $result;
    }

    public static function fullName(mixed $profile): string {
        if (!is_array($profile)) return "(unknown)";
        if (!array_key_exists('name', $profile)) return "(unknown)";

        $name = $profile['name'];
        return $name['first_name']. ' '. $name['last_name'];
    }

    public static function initials(mixed $profile): string {

    }

    public static function avatar(mixed $profile): string {
        if (!validate($profile)) return "";
    }

    public static function validate(mixed $profile): bool {
        if (!is_array($profile)) return false;
        if (empty($profile)) return false;
        if (!array_key_exists('name', $profile)) return false;
        // if (!array_key_exists(''))

    }
}
?>