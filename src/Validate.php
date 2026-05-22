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
     * @param ?string              $i_nstKey The key to check for
     * @param array<string, mixed> $i_r      The array to search
     * @return bool True if the key exists, false otherwise
     */
    public static function arrayKey( ?string $i_nstKey, array $i_r ) : bool {
        if ( is_null( $i_nstKey ) ) {
            return false;
        }
        return array_key_exists( $i_nstKey, $i_r );
    }


    /**
     * Validates that a value exists in an array.
     *
     * @param mixed                     $i_xValue The value to search for
     * @param array<int|string, string> $i_r      The array of values to search
     * @return bool True if the value exists in the array, false otherwise
     *
     * Strict comparison is always used.
     */
    public static function arrayValue( mixed $i_xValue, array $i_r ) : bool {
        return in_array( $i_xValue, $i_r, true );
    }


    /**
     * Validates if a string can be parsed as a boolean.
     *
     * Accepts numeric values and text values like 'true', 'yes', 'on',
     * 'false', 'no', 'off' (case-insensitive).
     *
     * @param ?string $i_nstBool The string to validate
     * @return bool True if the string can be parsed as a boolean, false otherwise
     */
    public static function bool( ?string $i_nstBool ) : bool {
        if ( ! is_string( $i_nstBool ) ) {
            return false;
        }
        if ( is_numeric( $i_nstBool ) ) {
            return true;
        }
        return match ( strtolower( trim( $i_nstBool ) ) ) {
            'true', 'yes', 'yeah', 'y', 'on', 'false', 'no', 'nope', 'n', 'off' => true,
            default => false,
        };
    }


    /**
     * Validates if a string can be parsed as a currency amount.
     *
     * @param ?string $i_nstCurrency The string to validate
     * @return bool True if the string can be parsed as currency, false otherwise
     */
    public static function currency( ?string $i_nstCurrency ) : bool {
        if ( ! is_string( $i_nstCurrency ) ) {
            return false;
        }
        return is_numeric( Filter::currency( $i_nstCurrency ) );
    }


    /**
     * Validates if a string can be parsed as a date-time.
     *
     * @param ?string $i_nstDate The string to validate
     * @return bool True if the string can be parsed as a date-time, false otherwise
     */
    public static function dateTime( ?string $i_nstDate ) : bool {
        if ( ! is_string( $i_nstDate ) ) {
            return false;
        }
        return strtotime( $i_nstDate ) !== false;
    }


    /**
     * Validates if a string is a valid email address.
     *
     * @param ?string $i_nstEmail The string to validate
     * @return bool True if the string is a valid email address, false otherwise
     */
    public static function emailAddress( ?string $i_nstEmail ) : bool {
        if ( ! $i_nstEmail ) {
            return false;
        }
        return filter_var( $i_nstEmail, FILTER_VALIDATE_EMAIL ) !== false;
    }


    /**
     * Validates if a string is a valid email username (local part).
     *
     * @param ?string $i_nstUsername The string to validate
     * @return bool True if the string is a valid email username, false otherwise
     */
    public static function emailUsername( ?string $i_nstUsername ) : bool {
        if ( ! $i_nstUsername ) {
            return false;
        }
        return self::emailAddress( $i_nstUsername . '@example.com' );
    }


    /**
     * Validates if a path is an existing directory.
     *
     * @param ?string $i_nstDir The path to validate
     * @return bool True if the path is an existing directory, false otherwise
     */
    public static function existingDirectory( ?string $i_nstDir ) : bool {
        if ( ! self::existingPath( $i_nstDir ) ) {
            return false;
        }
        assert( is_string( $i_nstDir ) );
        return is_dir( $i_nstDir );
    }


    /**
     * Validates if a path is an existing file or directory.
     *
     * @param ?string $i_nstFile The path to validate
     * @return bool True if the path exists and is a file, false otherwise
     */
    public static function existingFilename( ?string $i_nstFile ) : bool {
        if ( ! self::existingPath( $i_nstFile ) ) {
            return false;
        }
        assert( is_string( $i_nstFile ) );
        return is_file( $i_nstFile );
    }


    /**
     * Validates if a path exists. Does not make any statement
     * about what is at that path (file, directory, fifo, device node...)
     *
     * @param ?string $i_nstPath
     * @return bool
     */
    public static function existingPath( ?string $i_nstPath ) : bool {
        if ( ! is_string( $i_nstPath ) ) {
            return false;
        }
        return file_exists( $i_nstPath );
    }


    /**
     * Validates if a string can be parsed as a floating-point number.
     *
     * @param ?string $i_nstFloat The string to validate
     * @return bool True if the string is numeric, false otherwise
     */
    public static function float( ?string $i_nstFloat ) : bool {
        if ( ! is_string( $i_nstFloat ) ) {
            return false;
        }
        return is_numeric( $i_nstFloat );
    }


    /**
     * Validates if a string is a float within a closed range (exclusive bounds).
     *
     * @param ?string $i_nstFloat The string to validate
     * @param float   $i_fMin     The minimum value (exclusive)
     * @param float   $i_fMax     The maximum value (exclusive)
     * @return bool True if the string is a valid float within the range, false otherwise
     */
    public static function floatRangeClosed( ?string $i_nstFloat, float $i_fMin, float $i_fMax ) : bool {
        if ( ! self::float( $i_nstFloat ) ) {
            return false;
        }
        $f = floatval( $i_nstFloat );
        return $f > $i_fMin && $f < $i_fMax;
    }


    /**
     * Validates if a string is a float within a half-closed range [min, max).
     *
     * @param ?string $i_nstFloat The string to validate
     * @param float   $i_fMin     The minimum value (inclusive)
     * @param float   $i_fMax     The maximum value (exclusive)
     * @return bool True if the string is a valid float within the range, false otherwise
     */
    public static function floatRangeHalfClosed( ?string $i_nstFloat, float $i_fMin, float $i_fMax ) : bool {
        if ( ! self::float( $i_nstFloat ) ) {
            return false;
        }
        $f = floatval( $i_nstFloat );
        return $f >= $i_fMin && $f < $i_fMax;
    }


    /**
     * Validates if a string is a float within an open range [min, max].
     *
     * @param ?string $i_nstFloat The string to validate
     * @param float   $i_fMin     The minimum value (inclusive)
     * @param float   $i_fMax     The maximum value (inclusive)
     * @return bool True if the string is a valid float within the range, false otherwise
     */
    public static function floatRangeOpen( ?string $i_nstFloat, float $i_fMin, float $i_fMax ) : bool {
        if ( ! self::float( $i_nstFloat ) ) {
            return false;
        }
        $f = floatval( $i_nstFloat );
        return $f >= $i_fMin && $f <= $i_fMax;
    }


    /**
     * Loose validation of FQDNs. For stricter validation, use Validate::hostname().
     *
     * @param string|null $i_nstFQDN
     * @param bool        $i_bAllowSpaces Allow spaces in FQDN? Default is false.
     * @return bool
     */
    public static function fqdn( ?string $i_nstFQDN, bool $i_bAllowSpaces = false ) : bool {
        if ( ! is_string( $i_nstFQDN ) ) {
            return false;
        }
        $i_nstFQDN = strtolower( trim( $i_nstFQDN ) );
        if ( ! $i_bAllowSpaces && str_contains( $i_nstFQDN, ' ' ) ) {
            return false;
        }
        return filter_var( $i_nstFQDN, FILTER_VALIDATE_DOMAIN ) !== false;
    }


    /**
     * Validates if a string is a valid hostname.
     *
     * Requires at least one dot and rejects hostnames ending with a dot. This
     * is for hostnames specifically, not any FQDN. (E.g., do not use this
     * for SRV records.)
     *
     * @param ?string $i_stHost The string to validate
     * @return bool True if the string is a valid hostname, false otherwise
     */
    public static function hostname( ?string $i_stHost ) : bool {
        if ( ! is_string( $i_stHost ) ) {
            return false;
        }
        $i_stHost = strtolower( trim( $i_stHost ) );
        if ( 'localhost' === $i_stHost ) {
            return true;
        }
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
     * @param ?string $i_nstInt The string to validate
     * @return bool True if the string is numeric, false otherwise
     */
    public static function int( ?string $i_nstInt ) : bool {
        if ( ! is_numeric( $i_nstInt ) ) {
            return false;
        }
        if ( '-0' === $i_nstInt ) {
            return true;
        }
        return strval( intval( $i_nstInt ) ) === $i_nstInt;
    }


    /**
     * Validates if a string is an integer within a closed range (exclusive bounds).
     *
     * @param ?string $i_nstInt The string to validate
     * @param int     $i_nuMin  The minimum value (exclusive)
     * @param int     $i_nuMax  The maximum value (exclusive)
     * @return bool True if the string is a valid integer within the range, false otherwise
     */
    public static function intRangeClosed( ?string $i_nstInt, int $i_nuMin, int $i_nuMax ) : bool {
        if ( ! self::int( $i_nstInt ) ) {
            return false;
        }
        $nu = intval( $i_nstInt );
        return $nu > $i_nuMin && $nu < $i_nuMax;
    }


    /**
     * Validates if a string is an integer within a half-closed range [min, max).
     *
     * @param ?string $i_nstInt The string to validate
     * @param int     $i_nuMin  The minimum value (inclusive)
     * @param int     $i_nuMax  The maximum value (exclusive)
     * @return bool True if the string is a valid integer within the range, false otherwise
     */
    public static function intRangeHalfClosed( ?string $i_nstInt, int $i_nuMin, int $i_nuMax ) : bool {
        if ( ! self::int( $i_nstInt ) ) {
            return false;
        }
        $nu = intval( $i_nstInt );
        return $nu >= $i_nuMin && $nu < $i_nuMax;
    }


    /**
     * Validates if a string is an integer within an open range [min, max].
     *
     * @param ?string $i_nstInt The string to validate
     * @param int     $i_nuMin  The minimum value (inclusive)
     * @param int     $i_nuMax  The maximum value (inclusive)
     * @return bool True if the string is a valid integer within the range, false otherwise
     */
    public static function intRangeOpen( ?string $i_nstInt, int $i_nuMin, int $i_nuMax ) : bool {
        if ( ! self::int( $i_nstInt ) ) {
            return false;
        }
        $nu = intval( $i_nstInt );
        return $nu >= $i_nuMin && $nu <= $i_nuMax;
    }


    /**
     * Validates if a string is a valid IP address (IPv4 or IPv6).
     *
     * @param ?string $i_nstIP The string to validate
     * @return bool True if the string is a valid IP address, false otherwise
     */
    public static function ip( ?string $i_nstIP ) : bool {
        if ( ! is_string( $i_nstIP ) ) {
            return false;
        }
        if ( str_starts_with( $i_nstIP, '[' ) && str_ends_with( $i_nstIP, ']' ) ) {
            return self::ipv6( $i_nstIP );
        }
        return filter_var( $i_nstIP, FILTER_VALIDATE_IP ) !== false;
    }


    /**
     * Validates if a string is a valid IPv4 address.
     *
     * @param ?string $i_nstIP The string to validate
     * @return bool True if the string is a valid IPv4 address, false otherwise
     */
    public static function ipv4( ?string $i_nstIP ) : bool {
        if ( ! is_string( $i_nstIP ) ) {
            return false;
        }
        return filter_var( $i_nstIP, FILTER_VALIDATE_IP, FILTER_FLAG_IPV4 ) !== false;
    }


    /**
     * Validates if a string is a valid IPv6 address.
     *
     * @param ?string $i_nstIP The string to validate
     * @return bool True if the string is a valid IPv6 address, false otherwise
     */
    public static function ipv6( ?string $i_nstIP ) : bool {
        if ( ! is_string( $i_nstIP ) ) {
            return false;
        }
        if ( str_starts_with( $i_nstIP, '[' ) && str_ends_with( $i_nstIP, ']' ) ) {
            $i_nstIP = substr( $i_nstIP, 1, -1 );
        }
        return filter_var( $i_nstIP, FILTER_VALIDATE_IP, FILTER_FLAG_IPV6 ) !== false;
    }


    /**
     * Validates that a filename path does not exist.
     *
     * Also validates that the parent directory exists if specified.
     *
     * @param ?string $i_nstFile The file path to validate
     * @return bool True if the file does not exist and parent directory is valid, false otherwise
     */
    public static function nonexistentFilename( ?string $i_nstFile ) : bool {
        if ( '' === $i_nstFile || ! is_string( $i_nstFile ) ) {
            return false;
        }
        if ( file_exists( $i_nstFile ) ) {
            return false;
        }
        $stDir = dirname( $i_nstFile );
        if ( $stDir && ! is_dir( $stDir ) ) {
            return false;
        }
        return true;
    }


    /**
     * Validates if a string is a positive floating-point number (> 0).
     *
     * @param ?string $i_nstFloat The string to validate
     * @return bool True if the string is a positive float, false otherwise
     */
    public static function positiveFloat( ?string $i_nstFloat ) : bool {
        return self::floatRangeOpen( $i_nstFloat, PHP_FLOAT_EPSILON, PHP_FLOAT_MAX );
    }


    /**
     * Validates if a string is a positive integer (> 0).
     *
     * @param ?string $i_nstInt The string to validate
     * @return bool True if the string is a positive integer, false otherwise
     */
    public static function positiveInt( ?string $i_nstInt ) : bool {
        return self::intRangeOpen( $i_nstInt, 1, PHP_INT_MAX );
    }


    /**
     * Validates if a string is a non-negative floating-point number (>= 0).
     *
     * @param ?string $i_nstInt The string to validate
     * @return bool True if the string is a non-negative float, false otherwise
     */
    public static function unsignedFloat( ?string $i_nstInt ) : bool {
        return self::floatRangeOpen( $i_nstInt, 0.0, PHP_FLOAT_MAX );
    }


    /**
     * Validates if a string is a non-negative integer (>= 0).
     *
     * @param ?string $i_nstInt The string to validate
     * @return bool True if the string is a non-negative integer, false otherwise
     */
    public static function unsignedInt( ?string $i_nstInt ) : bool {
        return self::intRangeOpen( $i_nstInt, 0, PHP_INT_MAX );
    }


}
