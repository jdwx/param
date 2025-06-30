<?php


declare( strict_types = 1 );


namespace JDWX\Param;


/**
 * Static utility class for parsing and validating string values.
 * 
 * The Parse class provides methods to convert string inputs to specific types
 * with validation. Each method validates the input and throws ParseException
 * if the string cannot be converted to the expected type. This class is used
 * internally by Parameter classes to handle type-safe conversions.
 */
class Parse {


    /**
     * Maps a key to its corresponding value in an associative array.
     * 
     * @param string $i_stKey The key to look up
     * @param array<string, string> $i_r The associative array to search
     * @param string|null $i_nstError Optional custom error message
     * @return string The mapped value for the given key
     * @throws ParseException If the key is not found in the array
     */
    public static function arrayMap( string $i_stKey, array $i_r, ?string $i_nstError = null ) : string {
        if ( Validate::arrayKey( $i_stKey, $i_r ) ) {
            return $i_r[ $i_stKey ];
        }
        $stOptions = self::summarizeOptions( array_keys( $i_r ) );
        throw new ParseException( $i_nstError ?? "Expected one of {$stOptions}. Got: {$i_stKey}" );
    }


    /**
     * Validates that a value exists in an array of allowed values.
     * 
     * @param string $i_stValue The value to validate
     * @param list<string> $i_r The array of allowed values
     * @param string|null $i_nstError Optional custom error message
     * @return string The validated value
     * @throws ParseException If the value is not in the allowed array
     */
    public static function arrayValue( string $i_stValue, array $i_r, ?string $i_nstError = null ) : string {
        if ( Validate::arrayValue( $i_stValue, $i_r ) ) {
            return $i_stValue;
        }
        /** @phpstan-ignore arrayValues.list */
        $stOptions = self::summarizeOptions( array_values( $i_r ) );
        throw new ParseException( $i_nstError ?? "Expected one of {$stOptions}. Got: {$i_stValue}" );
    }


    /**
     * Parses a string to a boolean value.
     * 
     * Accepts numeric values (0 = false, non-zero = true) and text values
     * such as 'true', 'yes', 'on', 'false', 'no', 'off' (case-insensitive).
     * 
     * @param string $i_stBool The string to parse
     * @param string|null $i_nstError Optional custom error message
     * @return bool The parsed boolean value
     * @throws ParseException If the string cannot be parsed as a boolean
     */
    public static function bool( string $i_stBool, ?string $i_nstError = null ) : bool {
        if ( is_numeric( $i_stBool ) ) {
            return intval( $i_stBool ) !== 0;
        }
        return match ( strtolower( $i_stBool ) ) {
            'true', 'yes', 'yeah', 'y', 'on' => true,
            'false', 'no', 'nope', 'n', 'off' => false,
            default => throw new ParseException( $i_nstError ?? "Invalid boolean value: {$i_stBool}" ),
        };
    }


    /**
     * Validates that a string matches a specific constant value.
     * 
     * @param string $i_stString The string to validate
     * @param string $i_stConstant The expected constant value
     * @param string|null $i_nstError Optional custom error message
     * @return string The validated string (same as input if valid)
     * @throws ParseException If the string does not match the constant
     */
    public static function constant( string $i_stString, string $i_stConstant, ?string $i_nstError = null ) : string {
        if ( $i_stString === $i_stConstant ) {
            return $i_stString;
        }
        throw new ParseException( $i_nstError ?? "Expected constant: {$i_stConstant}. Got: {$i_stString}" );
    }


    /**
     * Parses a currency string to cents (integer).
     * 
     * Converts currency amounts like "$1.23" or "1.23" to cents (123).
     * 
     * @param string $i_stCurrency The currency string to parse
     * @param string|null $i_nstError Optional custom error message
     * @return int The currency amount in cents
     * @throws ParseException If the string cannot be parsed as currency
     */
    public static function currency( string $i_stCurrency, ?string $i_nstError = null ) : int {
        $st = Filter::currency( $i_stCurrency );
        if ( $st === null ) {
            throw new ParseException( $i_nstError ?? "Invalid currency: {$i_stCurrency}" );
        }
        $f = self::roundedFloat( $st, 2 );
        return intval( round( $f * 100 ) );
    }


    /**
     * Parses and validates a date string.
     * 
     * @param string $i_stDate The date string to parse
     * @param string|null $i_nstError Optional custom error message
     * @return string The validated date string in standard format
     * @throws ParseException If the string is not a valid date
     */
    public static function date( string $i_stDate, ?string $i_nstError = null ) : string {
        $nst = Filter::date( $i_stDate );
        if ( $nst === null ) {
            throw new ParseException( $i_nstError ?? "Invalid date: {$i_stDate}" );
        }
        return $nst;
    }


    /**
     * Parses and validates a date-time string.
     * 
     * @param string $i_stDateTime The date-time string to parse
     * @param string|null $i_nstError Optional custom error message
     * @return string The validated date-time string in standard format
     * @throws ParseException If the string is not a valid date-time
     */
    public static function dateTime( string $i_stDateTime, ?string $i_nstError = null ) : string {
        $nst = Filter::dateTime( $i_stDateTime );
        if ( $nst === null ) {
            throw new ParseException( $i_nstError ?? "Invalid date/time: {$i_stDateTime}" );
        }
        return $nst;
    }


    /**
     * Validates an email address.
     * 
     * @param string $i_stEmail The email address to validate
     * @param string|null $i_nstError Optional custom error message
     * @return string The validated email address
     * @throws ParseException If the string is not a valid email address
     */
    public static function emailAddress( string $i_stEmail, ?string $i_nstError = null ) : string {
        if ( Validate::emailAddress( $i_stEmail ) ) {
            return $i_stEmail;
        }
        throw new ParseException( $i_nstError ?? "Invalid email address: {$i_stEmail}" );
    }


    /**
     * Validates an email username (local part before @).
     * 
     * @param string $i_stUsername The email username to validate
     * @param string|null $i_nstError Optional custom error message
     * @return string The validated email username
     * @throws ParseException If the string is not a valid email username
     */
    public static function emailUsername( string $i_stUsername, ?string $i_nstError = null ) : string {
        if ( Validate::emailUsername( $i_stUsername ) ) {
            return $i_stUsername;
        }
        throw new ParseException( $i_nstError ?? "Invalid email username: {$i_stUsername}" );
    }


    /**
     * Validates that a path is an existing directory.
     * 
     * @param string $i_stDir The directory path to validate
     * @param string|null $i_nstError Optional custom error message
     * @return string The validated directory path
     * @throws ParseException If the path is not an existing directory
     */
    public static function existingDirectory( string $i_stDir, ?string $i_nstError = null ) : string {
        if ( Validate::existingDirectory( $i_stDir ) ) {
            return $i_stDir;
        }
        throw new ParseException( $i_nstError ?? "Not a directory: {$i_stDir}" );
    }


    /**
     * Validates that a path is an existing file.
     * 
     * @param string $i_stFile The file path to validate
     * @param string|null $i_nstError Optional custom error message
     * @return string The validated file path
     * @throws ParseException If the path is not an existing file
     */
    public static function existingFilename( string $i_stFile, ?string $i_nstError = null ) : string {
        if ( Validate::existingFilename( $i_stFile ) ) {
            return $i_stFile;
        }
        throw new ParseException( $i_nstError ?? "Filename does not exist: {$i_stFile}" );
    }


    /**
     * Parses a string to a floating-point number.
     * 
     * @param string $i_stFloat The string to parse
     * @param string|null $i_nstError Optional custom error message
     * @return float The parsed float value
     * @throws ParseException If the string cannot be parsed as a float
     */
    public static function float( string $i_stFloat, ?string $i_nstError = null ) : float {
        if ( Validate::float( $i_stFloat ) ) {
            return floatval( $i_stFloat );
        }
        throw new ParseException( $i_nstError ?? "Invalid float: {$i_stFloat}" );
    }


    /**
     * Parses a float and validates it's within a closed range (exclusive bounds).
     * 
     * @param string $i_stFloat The string to parse
     * @param float $i_fMin The minimum allowed value (exclusive)
     * @param float $i_fMax The maximum allowed value (exclusive)
     * @param string|null $i_nstError Optional custom error message
     * @return float The parsed and validated float
     * @throws ParseException If the string is not a valid float or is outside the range
     */
    public static function floatRangeClosed( string  $i_stFloat, float $i_fMin, float $i_fMax,
                                             ?string $i_nstError = null ) : float {
        if ( Validate::floatRangeClosed( $i_stFloat, $i_fMin, $i_fMax ) ) {
            return floatval( $i_stFloat );
        }
        throw new ParseException( $i_nstError ?? "Float out of range ({$i_fMin}, {$i_fMax}): {$i_stFloat}" );
    }


    /**
     * Parses a float and validates it's within a half-closed range [min, max).
     * 
     * @param string $i_stFloat The string to parse
     * @param float $i_fMin The minimum allowed value (inclusive)
     * @param float $i_fMax The maximum allowed value (exclusive)
     * @param string|null $i_nstError Optional custom error message
     * @return float The parsed and validated float
     * @throws ParseException If the string is not a valid float or is outside the range
     */
    public static function floatRangeHalfClosed( string  $i_stFloat, float $i_fMin, float $i_fMax,
                                                 ?string $i_nstError = null ) : float {
        if ( Validate::floatRangeHalfClosed( $i_stFloat, $i_fMin, $i_fMax ) ) {
            return floatval( $i_stFloat );
        }
        throw new ParseException( $i_nstError ?? "Float out of range [{$i_fMin}, {$i_fMax}): {$i_stFloat}" );
    }


    /**
     * Parses a float and validates it's within an open range [min, max].
     * 
     * @param string $i_stFloat The string to parse
     * @param float $i_fMin The minimum allowed value (inclusive)
     * @param float $i_fMax The maximum allowed value (inclusive)
     * @param string|null $i_nstError Optional custom error message
     * @return float The parsed and validated float
     * @throws ParseException If the string is not a valid float or is outside the range
     */
    public static function floatRangeOpen( string  $i_stFloat, float $i_fMin, float $i_fMax,
                                           ?string $i_nstError = null ) : float {
        if ( Validate::floatRangeOpen( $i_stFloat, $i_fMin, $i_fMax ) ) {
            return floatval( $i_stFloat );
        }
        throw new ParseException( $i_nstError ?? "Float out of range [{$i_fMin}, {$i_fMax}]: {$i_stFloat}" );
    }


    /**
     * Executes a glob pattern and returns matching filenames.
     * 
     * @param string $i_stGlob The glob pattern to execute
     * @param int $i_iFlags Optional flags for the glob function
     * @param bool $i_bAllowEmpty Whether to allow empty results
     * @return list<string> A list of filenames matching the glob pattern
     * @throws ParseException If the glob pattern is invalid or no matches found (unless allowed)
     */
    public static function glob( string $i_stGlob, int $i_iFlags = 0, bool $i_bAllowEmpty = false ) : array {
        $r = glob( $i_stGlob, $i_iFlags );
        if ( is_array( $r ) && ( count( $r ) > 0 || $i_bAllowEmpty ) ) {
            return $r;
        }
        throw new ParseException( "Invalid glob pattern or no matches: {$i_stGlob}" );
    }


    /**
     * Validates a hostname.
     * 
     * @param string $i_stHost The hostname to validate
     * @param string|null $i_nstError Optional custom error message
     * @return string The validated hostname
     * @throws ParseException If the string is not a valid hostname
     */
    public static function hostname( string $i_stHost, ?string $i_nstError = null ) : string {
        if ( Validate::hostname( $i_stHost ) ) {
            return $i_stHost;
        }
        throw new ParseException( $i_nstError ?? "Invalid hostname: {$i_stHost}" );
    }


    /**
     * Parses a string to an integer.
     * 
     * @param string $i_stInt The string to parse
     * @param string|null $i_nstError Optional custom error message
     * @return int The parsed integer value
     * @throws ParseException If the string cannot be parsed as an integer
     */
    public static function int( string $i_stInt, ?string $i_nstError = null ) : int {
        if ( Validate::int( $i_stInt ) ) {
            return intval( $i_stInt );
        }
        throw new ParseException( $i_nstError ?? "Invalid integer: {$i_stInt}" );
    }


    /**
     * Parses an integer and validates it's within a closed range (exclusive bounds).
     * 
     * @param string $i_stInt The string to parse
     * @param int $i_iMin The minimum allowed value (exclusive)
     * @param int $i_iMax The maximum allowed value (exclusive)
     * @param string|null $i_nstError Optional custom error message
     * @return int The parsed and validated integer
     * @throws ParseException If the string is not a valid integer or is outside the range
     */
    public static function intRangeClosed( string  $i_stInt, int $i_iMin, int $i_iMax,
                                           ?string $i_nstError = null ) : int {
        if ( Validate::intRangeClosed( $i_stInt, $i_iMin, $i_iMax ) ) {
            return intval( $i_stInt );
        }
        throw new ParseException( $i_nstError ?? "Integer out of range ({$i_iMin}, {$i_iMax}): {$i_stInt}" );
    }


    /**
     * Parses an integer and validates it's within a half-closed range [min, max).
     * 
     * @param string $i_stInt The string to parse
     * @param int $i_iMin The minimum allowed value (inclusive)
     * @param int $i_iMax The maximum allowed value (exclusive)
     * @param string|null $i_nstError Optional custom error message
     * @return int The parsed and validated integer
     * @throws ParseException If the string is not a valid integer or is outside the range
     */
    public static function intRangeHalfClosed( string  $i_stInt, int $i_iMin, int $i_iMax,
                                               ?string $i_nstError = null ) : int {
        if ( Validate::intRangeHalfClosed( $i_stInt, $i_iMin, $i_iMax ) ) {
            return intval( $i_stInt );
        }
        throw new ParseException( $i_nstError ?? "Integer out of range [{$i_iMin}, {$i_iMax}): {$i_stInt}" );
    }


    /**
     * Parses an integer and validates it's within an open range [min, max].
     * 
     * @param string $i_stInt The string to parse
     * @param int $i_iMin The minimum allowed value (inclusive)
     * @param int $i_iMax The maximum allowed value (inclusive)
     * @param string|null $i_nstError Optional custom error message
     * @return int The parsed and validated integer
     * @throws ParseException If the string is not a valid integer or is outside the range
     */
    public static function intRangeOpen( string  $i_stInt, int $i_iMin, int $i_iMax,
                                         ?string $i_nstError = null ) : int {
        if ( Validate::intRangeOpen( $i_stInt, $i_iMin, $i_iMax ) ) {
            return intval( $i_stInt );
        }
        throw new ParseException( $i_nstError ?? "Integer out of range [{$i_iMin}, {$i_iMax}]: {$i_stInt}" );
    }


    /**
     * Validates an IP address (IPv4 or IPv6).
     * 
     * @param string $i_stIP The IP address to validate
     * @param string|null $i_nstError Optional custom error message
     * @return string The validated IP address
     * @throws ParseException If the string is not a valid IP address
     */
    public static function ip( string $i_stIP, ?string $i_nstError = null ) : string {
        if ( Validate::ip( $i_stIP ) ) {
            return $i_stIP;
        }
        throw new ParseException( $i_nstError ?? "Invalid IP address: {$i_stIP}" );
    }


    /**
     * Validates an IPv4 address.
     * 
     * @param string $i_stIP The IPv4 address to validate
     * @param string|null $i_nstError Optional custom error message
     * @return string The validated IPv4 address
     * @throws ParseException If the string is not a valid IPv4 address
     */
    public static function ipv4( string $i_stIP, ?string $i_nstError = null ) : string {
        if ( Validate::ipv4( $i_stIP ) ) {
            return $i_stIP;
        }
        throw new ParseException( $i_nstError ?? "Invalid IPv4 address: {$i_stIP}" );
    }


    /**
     * Validates an IPv6 address.
     * 
     * @param string $i_stIP The IPv6 address to validate
     * @param string|null $i_nstError Optional custom error message
     * @return string The validated IPv6 address
     * @throws ParseException If the string is not a valid IPv6 address
     */
    public static function ipv6( string $i_stIP, ?string $i_nstError = null ) : string {
        if ( Validate::ipv6( $i_stIP ) ) {
            return $i_stIP;
        }
        throw new ParseException( $i_nstError ?? "Invalid IPv6 address: {$i_stIP}" );
    }


    /**
     * Validates that a filename path does not exist.
     * 
     * @param string $i_stFile The file path to validate
     * @param string|null $i_nstError Optional custom error message
     * @return string The validated file path
     * @throws ParseException If the file exists or the directory path is invalid
     */
    public static function nonexistentFilename( string $i_stFile, ?string $i_nstError = null ) : string {
        if ( Validate::nonexistentFilename( $i_stFile ) ) {
            return $i_stFile;
        }
        $stDir = dirname( $i_stFile );
        if ( $stDir && ! is_dir( $stDir ) ) {
            throw new ParseException( $i_nstError ?? "Directory does not exist for filename: {$i_stFile}" );
        }
        throw new ParseException( $i_nstError ?? "Invalid filename: {$i_stFile}" );
    }


    /**
     * Parses a string to a positive floating-point number (> 0).
     * 
     * @param string $i_stFloat The string to parse
     * @param string|null $i_nstError Optional custom error message
     * @return float The parsed positive float value
     * @throws ParseException If the string is not a positive float
     */
    public static function positiveFloat( string $i_stFloat, ?string $i_nstError = null ) : float {
        if ( Validate::positiveFloat( $i_stFloat ) ) {
            return floatval( $i_stFloat );
        }
        throw new ParseException( $i_nstError ?? "Invalid positive float: {$i_stFloat}" );
    }


    /**
     * Parses a string to a positive integer (> 0).
     * 
     * @param string $i_stInt The string to parse
     * @param string|null $i_nstError Optional custom error message
     * @return int The parsed positive integer value
     * @throws ParseException If the string is not a positive integer
     */
    public static function positiveInt( string $i_stInt, ?string $i_nstError = null ) : int {
        if ( Validate::positiveInt( $i_stInt ) ) {
            return intval( $i_stInt );
        }
        throw new ParseException( $i_nstError ?? "Invalid positive integer: {$i_stInt}" );
    }


    /**
     * Parses a string to a float and rounds it to the specified precision.
     * 
     * @param string $i_stFloat The string to parse
     * @param int $i_iPrecision Number of decimal places to round to
     * @param string|null $i_nstError Optional custom error message
     * @return float The parsed and rounded float value
     * @throws ParseException If the string cannot be parsed as a float
     */
    public static function roundedFloat( string  $i_stFloat, int $i_iPrecision = 0,
                                         ?string $i_nstError = null ) : float {
        if ( Validate::float( $i_stFloat ) ) {
            return round( floatval( $i_stFloat ), $i_iPrecision );
        }
        throw new ParseException( $i_nstError ?? "Invalid rounded float: {$i_stFloat}" );
    }


    /**
     * Parses a string to a float, rounds it, then converts to integer.
     * 
     * @param string $i_stInt The string to parse
     * @param int $i_iPrecision Number of decimal places before rounding
     * @param string|null $i_nstError Optional custom error message
     * @return int The parsed, rounded, and converted integer value
     * @throws ParseException If the string cannot be parsed as a float
     */
    public static function roundedInt( string $i_stInt, int $i_iPrecision = 0, ?string $i_nstError = null ) : int {
        if ( Validate::float( $i_stInt ) ) {
            return intval( round( floatval( $i_stInt ), $i_iPrecision ) );
        }
        throw new ParseException( $i_nstError ?? "Invalid rounded integer: {$i_stInt}" );
    }


    /**
     * Creates a summary string of options for error messages.
     * 
     * Limits the display to the first 4 options plus "..." if there are more than 5 options.
     * 
     * @param list<string> $i_rOptions The list of options to summarize
     * @return string A comma-separated summary of the options
     */
    public static function summarizeOptions( array $i_rOptions ) : string {
        if ( count( $i_rOptions ) > 5 ) {
            return join( ', ', array_slice( $i_rOptions, 0, 4 ) ) . ', ...';
        }
        if ( empty( $i_rOptions ) ) {
            return '(no options)';
        }
        return join( ', ', $i_rOptions );
    }


    /**
     * Parses and validates a time string.
     * 
     * @param string $i_stTime The time string to parse
     * @param string|null $i_nstError Optional custom error message
     * @return string The validated time string in standard format
     * @throws ParseException If the string is not a valid time
     */
    public static function time( string $i_stTime, ?string $i_nstError = null ) : string {
        $nst = Filter::time( $i_stTime );
        if ( $nst === null ) {
            throw new ParseException( $i_nstError ?? "Invalid time: {$i_stTime}" );
        }
        return $nst;
    }


    /**
     * Parses a date-time string and converts it to a Unix timestamp.
     * 
     * @param string $i_stTimeStamp The date-time string to parse
     * @param string|null $i_nstError Optional custom error message
     * @return int The Unix timestamp
     * @throws ParseException If the string is not a valid date-time
     */
    public static function timeStamp( string $i_stTimeStamp, ?string $i_nstError = null ) : int {
        $nst = Filter::dateTime( $i_stTimeStamp );
        if ( $nst === null ) {
            throw new ParseException( $i_nstError ?? "Invalid timestamp: {$i_stTimeStamp}" );
        }
        $tm = strtotime( $nst );
        # The date and time have already been validated.
        assert( is_int( $tm ) );
        return $tm;
    }


    /**
     * Parses a string to a non-negative floating-point number (>= 0).
     * 
     * @param string $i_stFloat The string to parse
     * @param string|null $i_nstError Optional custom error message
     * @return float The parsed non-negative float value
     * @throws ParseException If the string is not a non-negative float
     */
    public static function unsignedFloat( string $i_stFloat, ?string $i_nstError = null ) : float {
        if ( Validate::unsignedFloat( $i_stFloat ) ) {
            return floatval( $i_stFloat );
        }
        throw new ParseException( $i_nstError ?? "Invalid unsigned float: {$i_stFloat}" );
    }


    /**
     * Parses a string to a non-negative integer (>= 0).
     * 
     * @param string $i_stInt The string to parse
     * @param string|null $i_nstError Optional custom error message
     * @return int The parsed non-negative integer value
     * @throws ParseException If the string is not a non-negative integer
     */
    public static function unsignedInt( string $i_stInt, ?string $i_nstError = null ) : int {
        if ( Validate::unsignedInt( $i_stInt ) ) {
            return intval( $i_stInt );
        }
        throw new ParseException( $i_nstError ?? "Invalid unsigned integer: {$i_stInt}" );
    }


}
