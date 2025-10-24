<?php


declare( strict_types = 1 );


namespace JDWX\Param;


/**
 * Static utility class for validation without throwing exceptions.
 * 
 * The Validate class provides boolean validation methods that return true/false
 * rather than throwing exceptions. These methods are used by the Parse class
 * to check validity before conversion. All methods are static and the class
 * is marked final to prevent extension.
 */
final class Validate {


    /**
     * Validates that a key exists in an associative array.
     * 
     * @param string $i_stKey The key to check for
     * @param array<string, mixed> $i_r The array to search
     * @return bool True if the key exists, false otherwise
     */
    public static function arrayKey( string $i_stKey, array $i_r ) : bool {
        return array_key_exists( $i_stKey, $i_r );
    }


    /**
     * Validates that a value exists in an array.
     * 
     * @param string $i_stValue The value to search for
     * @param array<int|string, string> $i_r The array of values to search
     * @return bool True if the value exists in the array, false otherwise
     */
    public static function arrayValue( string $i_stValue, array $i_r ) : bool {
        return in_array( $i_stValue, $i_r );
    }


    /**
     * Validates if a string can be parsed as a boolean.
     * 
     * Accepts numeric values and text values like 'true', 'yes', 'on',
     * 'false', 'no', 'off' (case-insensitive).
     * 
     * @param string $i_stBool The string to validate
     * @return bool True if the string can be parsed as a boolean, false otherwise
     */
    public static function bool( string $i_stBool ) : bool {
        if ( is_numeric( $i_stBool ) ) {
            return true;
        }
        return match ( strtolower( trim( $i_stBool ) ) ) {
            'true', 'yes', 'yeah', 'y', 'on', 'false', 'no', 'nope', 'n', 'off' => true,
            default => false,
        };
    }


    /**
     * Validates if a string can be parsed as a currency amount.
     * 
     * @param string $i_stCurrency The string to validate
     * @return bool True if the string can be parsed as currency, false otherwise
     */
    public static function currency( string $i_stCurrency ) : bool {
        return is_numeric( Filter::currency( $i_stCurrency ) );
    }


    /**
     * Validates if a string can be parsed as a date-time.
     * 
     * @param string $i_stDate The string to validate
     * @return bool True if the string can be parsed as a date-time, false otherwise
     */
    public static function dateTime( string $i_stDate ) : bool {
        return strtotime( $i_stDate ) !== false;
    }


    /**
     * Validates if a string is a valid email address.
     * 
     * @param string $i_stEmail The string to validate
     * @return bool True if the string is a valid email address, false otherwise
     */
    public static function emailAddress( string $i_stEmail ) : bool {
        return filter_var( $i_stEmail, FILTER_VALIDATE_EMAIL ) !== false;
    }


    /**
     * Validates if a string is a valid email username (local part).
     * 
     * @param string $i_stUsername The string to validate
     * @return bool True if the string is a valid email username, false otherwise
     */
    public static function emailUsername( string $i_stUsername ) : bool {
        return self::emailAddress( $i_stUsername . '@example.com' );
    }


    /**
     * Validates if a path is an existing directory.
     * 
     * @param string $i_stDir The path to validate
     * @return bool True if the path is an existing directory, false otherwise
     */
    public static function existingDirectory( string $i_stDir ) : bool {
        if ( ! self::existingFilename( $i_stDir ) ) {
            return false;
        }
        return is_dir( $i_stDir );
    }


    /**
     * Validates if a path is an existing file or directory.
     * 
     * @param string $i_stFile The path to validate
     * @return bool True if the path exists, false otherwise
     */
    public static function existingFilename( string $i_stFile ) : bool {
        return file_exists( $i_stFile );
    }


    /**
     * Validates if a string can be parsed as a floating-point number.
     * 
     * @param string $i_stFloat The string to validate
     * @return bool True if the string is numeric, false otherwise
     */
    public static function float( string $i_stFloat ) : bool {
        return is_numeric( $i_stFloat );
    }


    /**
     * Validates if a string is a float within a closed range (exclusive bounds).
     * 
     * @param string $i_stFloat The string to validate
     * @param float $i_fMin The minimum value (exclusive)
     * @param float $i_fMax The maximum value (exclusive)
     * @return bool True if the string is a valid float within the range, false otherwise
     */
    public static function floatRangeClosed( string $i_stFloat, float $i_fMin, float $i_fMax ) : bool {
        if ( ! self::float( $i_stFloat ) ) {
            return false;
        }
        $f = floatval( $i_stFloat );
        return $f > $i_fMin && $f < $i_fMax;
    }


    /**
     * Validates if a string is a float within a half-closed range [min, max).
     * 
     * @param string $i_stFloat The string to validate
     * @param float $i_fMin The minimum value (inclusive)
     * @param float $i_fMax The maximum value (exclusive)
     * @return bool True if the string is a valid float within the range, false otherwise
     */
    public static function floatRangeHalfClosed( string $i_stFloat, float $i_fMin, float $i_fMax ) : bool {
        if ( ! self::float( $i_stFloat ) ) {
            return false;
        }
        $f = floatval( $i_stFloat );
        return $f >= $i_fMin && $f < $i_fMax;
    }


    /**
     * Validates if a string is a float within an open range [min, max].
     * 
     * @param string $i_stFloat The string to validate
     * @param float $i_fMin The minimum value (inclusive)
     * @param float $i_fMax The maximum value (inclusive)
     * @return bool True if the string is a valid float within the range, false otherwise
     */
    public static function floatRangeOpen( string $i_stFloat, float $i_fMin, float $i_fMax ) : bool {
        if ( ! self::float( $i_stFloat ) ) {
            return false;
        }
        $f = floatval( $i_stFloat );
        return $f >= $i_fMin && $f <= $i_fMax;
    }


    /**
     * Validates if a string is a valid hostname.
     * 
     * Requires at least one dot and rejects hostnames ending with a dot.
     * 
     * @param string $i_stHost The string to validate
     * @return bool True if the string is a valid hostname, false otherwise
     */
    public static function hostname( string $i_stHost ) : bool {
        if ( str_ends_with( $i_stHost, '.' ) ) {
            return false;
        }
        if ( ! str_contains( $i_stHost, '.' ) ) {
            return false;
        }
        return filter_var( $i_stHost, FILTER_VALIDATE_DOMAIN, FILTER_FLAG_HOSTNAME ) !== false;
    }


    /**
     * Validates if a string can be parsed as an integer.
     * 
     * @param string $i_stInt The string to validate
     * @return bool True if the string is numeric, false otherwise
     */
    public static function int( string $i_stInt ) : bool {
        return is_numeric( $i_stInt );
    }


    /**
     * Validates if a string is an integer within a closed range (exclusive bounds).
     * 
     * @param string $i_stInt The string to validate
     * @param int $i_nuMin The minimum value (exclusive)
     * @param int $i_nuMax The maximum value (exclusive)
     * @return bool True if the string is a valid integer within the range, false otherwise
     */
    public static function intRangeClosed( string $i_stInt, int $i_nuMin, int $i_nuMax ) : bool {
        if ( ! self::int( $i_stInt ) ) {
            return false;
        }
        $nu = intval( $i_stInt );
        return $nu > $i_nuMin && $nu < $i_nuMax;
    }


    /**
     * Validates if a string is an integer within a half-closed range [min, max).
     * 
     * @param string $i_stInt The string to validate
     * @param int $i_nuMin The minimum value (inclusive)
     * @param int $i_nuMax The maximum value (exclusive)
     * @return bool True if the string is a valid integer within the range, false otherwise
     */
    public static function intRangeHalfClosed( string $i_stInt, int $i_nuMin, int $i_nuMax ) : bool {
        if ( ! self::int( $i_stInt ) ) {
            return false;
        }
        $nu = intval( $i_stInt );
        return $nu >= $i_nuMin && $nu < $i_nuMax;
    }


    /**
     * Validates if a string is an integer within an open range [min, max].
     * 
     * @param string $i_stInt The string to validate
     * @param int $i_nuMin The minimum value (inclusive)
     * @param int $i_nuMax The maximum value (inclusive)
     * @return bool True if the string is a valid integer within the range, false otherwise
     */
    public static function intRangeOpen( string $i_stInt, int $i_nuMin, int $i_nuMax ) : bool {
        if ( ! self::int( $i_stInt ) ) {
            return false;
        }
        $nu = intval( $i_stInt );
        return $nu >= $i_nuMin && $nu <= $i_nuMax;
    }


    /**
     * Validates if a string is a valid IP address (IPv4 or IPv6).
     * 
     * @param string $i_stIP The string to validate
     * @return bool True if the string is a valid IP address, false otherwise
     */
    public static function ip( string $i_stIP ) : bool {
        return filter_var( $i_stIP, FILTER_VALIDATE_IP ) !== false;
    }


    /**
     * Validates if a string is a valid IPv4 address.
     * 
     * @param string $i_stIP The string to validate
     * @return bool True if the string is a valid IPv4 address, false otherwise
     */
    public static function ipv4( string $i_stIP ) : bool {
        return filter_var( $i_stIP, FILTER_VALIDATE_IP, FILTER_FLAG_IPV4 ) !== false;
    }


    /**
     * Validates if a string is a valid IPv6 address.
     * 
     * @param string $i_stIP The string to validate
     * @return bool True if the string is a valid IPv6 address, false otherwise
     */
    public static function ipv6( string $i_stIP ) : bool {
        return filter_var( $i_stIP, FILTER_VALIDATE_IP, FILTER_FLAG_IPV6 ) !== false;
    }


    /**
     * Validates that a filename path does not exist.
     * 
     * Also validates that the parent directory exists if specified.
     * 
     * @param string $i_stFile The file path to validate
     * @return bool True if the file does not exist and parent directory is valid, false otherwise
     */
    public static function nonexistentFilename( string $i_stFile ) : bool {
        if ( '' === $i_stFile ) {
            return false;
        }
        if ( file_exists( $i_stFile ) ) {
            return false;
        }
        $stDir = dirname( $i_stFile );
        if ( $stDir && ! is_dir( $stDir ) ) {
            return false;
        }
        return true;
    }


    /**
     * Validates if a string is a positive floating-point number (> 0).
     * 
     * @param string $i_stFloat The string to validate
     * @return bool True if the string is a positive float, false otherwise
     */
    public static function positiveFloat( string $i_stFloat ) : bool {
        return self::floatRangeOpen( $i_stFloat, PHP_FLOAT_EPSILON, PHP_FLOAT_MAX );
    }


    /**
     * Validates if a string is a positive integer (> 0).
     * 
     * @param string $i_stInt The string to validate
     * @return bool True if the string is a positive integer, false otherwise
     */
    public static function positiveInt( string $i_stInt ) : bool {
        return self::intRangeOpen( $i_stInt, 1, PHP_INT_MAX );
    }


    /**
     * Validates if a string is a non-negative floating-point number (>= 0).
     * 
     * @param string $i_stInt The string to validate
     * @return bool True if the string is a non-negative float, false otherwise
     */
    public static function unsignedFloat( string $i_stInt ) : bool {
        return self::floatRangeOpen( $i_stInt, 0.0, PHP_FLOAT_MAX );
    }


    /**
     * Validates if a string is a non-negative integer (>= 0).
     * 
     * @param string $i_stInt The string to validate
     * @return bool True if the string is a non-negative integer, false otherwise
     */
    public static function unsignedInt( string $i_stInt ) : bool {
        return self::intRangeOpen( $i_stInt, 0, PHP_INT_MAX );
    }


}
