<?php
class DateFormat
{
    private const DEFAULT_TIMEZONE = 'Asia/Manila';
    private const FORMATS = [
        'date_only' => 'M d Y',
        'full'      => 'M d Y, g:i A'
    ];

    public static function dateOnly($dt): string {
        if (!is_string($dt) || trim($dt) === '') return '(unknown)';
        try {
            $dateTime = new DateTime($dt);
            $dateTime->setTimezone(new DateTimeZone(self::DEFAULT_TIMEZONE));
            return $dateTime->format(self::FORMATS['date_only']);
        }
        catch (\Throwable $t) { }
        return '(unknown)';
    }

    public static function full($dt): string {
        if (!is_string($dt) || trim($dt) === '') return '(unknown)';
        try {
            $dateTime = new DateTime($dt);
            $dateTime->setTimezone(new DateTimeZone(self::DEFAULT_TIMEZONE));
            return $dateTime->format(self::FORMATS['full']);
        }
        catch (\Throwable $t) { }
        return '(unknown)';
    }
}
?>