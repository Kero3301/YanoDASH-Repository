<?php
final class DateUtils {
    public static function validateISODateFormat(mixed $value): bool {
        if (!is_string($value)) return false;
        
        $pattern = '/^\d{4}-\d{2}-\d{2}T\d{2}:\d{2}:\d{2}\.\d{3}[+\-]\d{2}:\d{2}$/';
        if (!preg_match($pattern, $value)) return false;

        try { $dt = new DateTimeImmutable($value); return true; }
        catch (Exception $e) { return false; }
    } 
}
?>