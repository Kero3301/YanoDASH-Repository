<?php
final class Mapper {
    private const MAP = [
        # Access level names
        'admin' => 'Administrator',
        'editor' => 'Editor',
        'viewer' => 'Viewer',

        # OSC office names
        'osc_president_office' 
            => 'Office of the President',
        'osc_ivp_office' 
            => 'Office of the Internal Vice President',
        'osc_evp_office' 
            => 'Office of the External Vice President',
        'osc_gensec_office' 
            => 'Office of the General Secretary',
        'osc_genaud_office' 
            => 'Office of the General Auditor',
        'osc_gentreas_office' 
            => 'Office of the General Treasurer',
        'osc_genpio_office' 
            => 'Office of the General PIO',

        # OSC executive position names
        'osc_president' 
            => 'OSC President',
        'osc_ivp' 
            => 'OSC Internal Vice President',
        'osc_evp' 
            => 'OSC External Vice President',
        'osc_gensec' 
            => 'OSC General Secretary',
        'osc_genaud' 
            => 'OSC General Auditor',
        'osc_gentreas' 
            => 'OSC General Treasurer',
        'osc_genpio' 
            => 'OSC General PIO'
    ];

    # A function for looking up a key's value from an array for the purposes of safe string rendering/presentation
    public static function find(mixed $object, mixed $map = MAP, mixed $fallback = "unknown", bool $strict = false): string {
        # Prevent non-string fallback values
        if (!is_string($fallback)) return "unknown";
        # POSTCONDITIONS: Fallback is a string

        # Ensure character escaping for supplied fallback value
        $fallback = htmlspecialchars($fallback);
        # POSTCONDITIONS: Fallback is a safely escaped string

        # If object is null, strictly return the fallback value
        if ($object === null) return $fallback;
        # POSTCONDITIONS: Object is set with a non-null value

        # If object is neither an int nor a string, strictly return the fallback value
        if (!is_int($object) && !is_string($object)) return $fallback;
        # POSTCONDITIONS: Object is a potentially valid key, either as an int or a string

        # Normalize the object key
        $object = is_string($object)? trim($object) : $object;
        # POSTCONDITIONS: Object key is normalized and safe to use 

        # Prevent non-array maps from being used
        if (!is_array($map)) return $strict? $fallback : htmlspecialchars((string) $object);
        # POSTCONDITIONS: The provided map is an array

        # If the array key corresponding to the object doesn't exist in the map, either return the fallback (if strict) or the object itself (if lax)
        if (!array_key_exists($object, $map)) return $strict? $fallback : htmlspecialchars((string) $object);
        # POSTCONDITIONS: The object exists as an array key in the map

        # Return the found value from the map as an escaped string for safe rendering
        try { return htmlspecialchars((string) $map[$object]); }
        catch (Throwable $e) { 
            error_log(sprintf("Failed to map key [%s] to its corresponding value: %s", (string) $object, $e->getMessage())); 
            return $strict? $fallback : htmlspecialchars((string) $object); 
        }
    }
}
?>