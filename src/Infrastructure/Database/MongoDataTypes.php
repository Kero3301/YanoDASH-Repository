<?php

require_once dirname(__DIR__, 3) . '/vendor/autoload.php';
require_once dirname(__DIR__) . '/Logging/Logs.php';

use MongoDB\BSON\Decimal128;
use MongoDB\BSON\ObjectId;

final class MongoDataTypes
{
    public static function oid($value): ?ObjectId
    {
        # Return the value itself if it is already an ObjectId
        if ($value instanceof ObjectId) return $value;

        # Prevent non-strings and empty strings
        if (!is_string($value) || trim($value) === '') return null;

        try {
            return new ObjectId($value);
        } catch (\Throwable $t) {
            $msg = $t->getMessage();
            Logs::write(LogDomain::Database, LogSeverity::Error, "Failed to create MongoDB ObjectId from value '$value': $msg");
        }
        return null;
    }

    public static function validOID($value): bool { return $value instanceof ObjectId; }

    public static function d128($value): ?Decimal128
    {
        # Return the value itself if it is already a Decimal128
        if ($value instanceof Decimal128) return $value;

        # Prevent non-numeric values
        if (!is_numeric($value)) return null;

        try {
            return new Decimal128((string)$value);
        } catch (\Throwable $t) {
            $msg = $t->getMessage();
            Logs::write(LogDomain::Database, LogSeverity::Error, "Failed to create MongoDB Decimal128 from value '$value': $msg");
        }
        return null;
    }

    public static function validD128($value): bool { return $value instanceof Decimal128; }
}