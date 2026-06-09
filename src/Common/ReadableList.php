<?php
final class ReadableList {
    private static function normalize($value): string {
        if (is_null($value)) return 'null';
        if (is_bool($value)) return $value ? 'true' : 'false';

        return (string)$value;
    }

    public static function format($list): string {
        if (!is_array($list) || count($list) === 0) return '';

        $list = array_map([self::class, 'normalize'], $list);

        $count = count($list);

        if ($count === 1) return $list[0];
        if ($count === 2) return "{$list[0]} and {$list[1]}";

        $last = array_pop($list);
        return implode(', ', $list) . ", and {$last}";
    }
}
?>