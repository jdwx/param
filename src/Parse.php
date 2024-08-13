<?php


declare( strict_types = 1 );


namespace JDWX\Param;


class Parse {


    /**
     * @param array<string, string> $i_r
     * @return string The mapped value for the given key.
     */
    public static function arrayMap( string $i_stKey, array $i_r, ?string $i_nstError = null ) : string {
        if ( Validate::arrayKey( $i_stKey, $i_r ) ) {
            return $i_r[ $i_stKey ];
        }
        $stOptions = self::summarizeOptions( array_keys( $i_r ) );
        throw new ParseException( $i_nstError ?? "Expected one of {$stOptions}. Got: {$i_stKey}" );
    }


    /** @param list<string> $i_r */
    public static function arrayValue( string $i_stValue, array $i_r, ?string $i_nstError = null ) : string {
        if ( Validate::arrayValue( $i_stValue, $i_r ) ) {
            return $i_stValue;
        }
        $stOptions = self::summarizeOptions( array_values( $i_r ) );
        throw new ParseException( $i_nstError ?? "Expected one of {$stOptions}. Got: {$i_stValue}" );
    }


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


    public static function currency( string $i_stCurrency, ?string $i_nstError = null ) : int {
        if ( Validate::currency( $i_stCurrency ) ) {
            $f = self::roundedFloat( $i_stCurrency, 2 );
            return (int) round( $f * 100 );
        }
        throw new ParseException( $i_nstError ?? "Invalid currency: {$i_stCurrency}" );
    }


    public static function emailAddress( string $i_stEmail, ?string $i_nstError = null ) : string {
        if ( Validate::emailAddress( $i_stEmail ) ) {
            return $i_stEmail;
        }
        throw new ParseException( $i_nstError ?? "Invalid email address: {$i_stEmail}" );
    }


    public static function emailUsername( string $i_stUsername, ?string $i_nstError = null ) : string {
        if ( Validate::emailUsername( $i_stUsername ) ) {
            return $i_stUsername;
        }
        throw new ParseException( $i_nstError ?? "Invalid email username: {$i_stUsername}" );
    }


    public static function existingDirectory( string $i_stDir, ?string $i_nstError = null ) : string {
        if ( Validate::existingDirectory( $i_stDir ) ) {
            return $i_stDir;
        }
        throw new ParseException( $i_nstError ?? "Not a directory: {$i_stDir}" );
    }


    public static function existingFilename( string $i_stFile, ?string $i_nstError = null ) : string {
        if ( Validate::existingFilename( $i_stFile ) ) {
            return $i_stFile;
        }
        throw new ParseException( $i_nstError ?? "Filename does not exist: {$i_stFile}" );
    }


    public static function float( string $i_stFloat, ?string $i_nstError = null ) : float {
        if ( Validate::float( $i_stFloat ) ) {
            return floatval( $i_stFloat );
        }
        throw new ParseException( $i_nstError ?? "Invalid float: {$i_stFloat}" );
    }


    public static function floatRangeClosed( string  $i_stFloat, float $i_fMin, float $i_fMax,
                                             ?string $i_nstError = null ) : float {
        if ( Validate::floatRangeClosed( $i_stFloat, $i_fMin, $i_fMax ) ) {
            return floatval( $i_stFloat );
        }
        throw new ParseException( $i_nstError ?? "Float out of range ({$i_fMin}, {$i_fMax}): {$i_stFloat}" );
    }


    public static function floatRangeHalfClosed( string  $i_stFloat, float $i_fMin, float $i_fMax,
                                                 ?string $i_nstError = null ) : float {
        if ( Validate::floatRangeHalfClosed( $i_stFloat, $i_fMin, $i_fMax ) ) {
            return floatval( $i_stFloat );
        }
        throw new ParseException( $i_nstError ?? "Float out of range [{$i_fMin}, {$i_fMax}): {$i_stFloat}" );
    }


    public static function floatRangeOpen( string  $i_stFloat, float $i_fMin, float $i_fMax,
                                           ?string $i_nstError = null ) : float {
        if ( Validate::floatRangeOpen( $i_stFloat, $i_fMin, $i_fMax ) ) {
            return floatval( $i_stFloat );
        }
        throw new ParseException( $i_nstError ?? "Float out of range [{$i_fMin}, {$i_fMax}]: {$i_stFloat}" );
    }


    public static function glob( string $i_stGlob, int $i_iFlags = 0 ) : array {
        $r = glob( $i_stGlob, $i_iFlags );
        if ( is_array( $r ) && count( $r ) > 0 ) {
            return $r;
        }
        throw new ParseException( "Invalid glob pattern or no matches: {$i_stGlob}" );
    }


    public static function hostname( string $i_stHost, ?string $i_nstError = null ) : string {
        if ( Validate::hostname( $i_stHost ) ) {
            return $i_stHost;
        }
        throw new ParseException( $i_nstError ?? "Invalid hostname: {$i_stHost}" );
    }


    public static function int( string $i_stInt, ?string $i_nstError = null ) : int {
        if ( Validate::int( $i_stInt ) ) {
            return intval( $i_stInt );
        }
        throw new ParseException( $i_nstError ?? "Invalid integer: {$i_stInt}" );
    }


    public static function intRangeClosed( string  $i_stInt, int $i_iMin, int $i_iMax,
                                           ?string $i_nstError = null ) : int {
        if ( Validate::intRangeClosed( $i_stInt, $i_iMin, $i_iMax ) ) {
            return intval( $i_stInt );
        }
        throw new ParseException( $i_nstError ?? "Integer out of range ({$i_iMin}, {$i_iMax}): {$i_stInt}" );
    }


    public static function intRangeHalfClosed( string  $i_stInt, int $i_iMin, int $i_iMax,
                                               ?string $i_nstError = null ) : int {
        if ( Validate::intRangeHalfClosed( $i_stInt, $i_iMin, $i_iMax ) ) {
            return intval( $i_stInt );
        }
        throw new ParseException( $i_nstError ?? "Integer out of range [{$i_iMin}, {$i_iMax}): {$i_stInt}" );
    }


    public static function intRangeOpen( string  $i_stInt, int $i_iMin, int $i_iMax,
                                         ?string $i_nstError = null ) : int {
        if ( Validate::intRangeOpen( $i_stInt, $i_iMin, $i_iMax ) ) {
            return intval( $i_stInt );
        }
        throw new ParseException( $i_nstError ?? "Integer out of range [{$i_iMin}, {$i_iMax}]: {$i_stInt}" );
    }


    public static function ip( string $i_stIP, ?string $i_nstError = null ) : string {
        if ( Validate::ip( $i_stIP ) ) {
            return $i_stIP;
        }
        throw new ParseException( $i_nstError ?? "Invalid IP address: {$i_stIP}" );
    }


    public static function ipv4( string $i_stIP, ?string $i_nstError = null ) : string {
        if ( Validate::ipv4( $i_stIP ) ) {
            return $i_stIP;
        }
        throw new ParseException( $i_nstError ?? "Invalid IPv4 address: {$i_stIP}" );
    }


    public static function ipv6( string $i_stIP, ?string $i_nstError = null ) : string {
        if ( Validate::ipv6( $i_stIP ) ) {
            return $i_stIP;
        }
        throw new ParseException( $i_nstError ?? "Invalid IPv6 address: {$i_stIP}" );
    }


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


    public static function positiveFloat( string $i_stFloat, ?string $i_nstError = null ) : float {
        if ( Validate::positiveFloat( $i_stFloat ) ) {
            return floatval( $i_stFloat );
        }
        throw new ParseException( $i_nstError ?? "Invalid positive float: {$i_stFloat}" );
    }


    public static function positiveInt( string $i_stInt, ?string $i_nstError = null ) : int {
        if ( Validate::positiveInt( $i_stInt ) ) {
            return intval( $i_stInt );
        }
        throw new ParseException( $i_nstError ?? "Invalid positive integer: {$i_stInt}" );
    }


    public static function roundedFloat( string  $i_stFloat, int $i_iPrecision = 0,
                                         ?string $i_nstError = null ) : float {
        if ( Validate::float( $i_stFloat ) ) {
            return round( floatval( $i_stFloat ), $i_iPrecision );
        }
        throw new ParseException( $i_nstError ?? "Invalid rounded float: {$i_stFloat}" );
    }


    public static function roundedInt( string $i_stInt, int $i_iPrecision = 0, ?string $i_nstError = null ) : int {
        if ( Validate::float( $i_stInt ) ) {
            return intval( round( floatval( $i_stInt ), $i_iPrecision ) );
        }
        throw new ParseException( $i_nstError ?? "Invalid rounded integer: {$i_stInt}" );
    }


    /** @param list<string> $i_rOptions */
    public static function summarizeOptions( array $i_rOptions ) : string {
        if ( count( $i_rOptions ) > 5 ) {
            return join( ', ', array_slice( $i_rOptions, 0, 4 ) ) . ', ...';
        }
        if ( empty( $i_rOptions ) ) {
            return '(no options)';
        }
        return join( ', ', $i_rOptions );
    }


    public static function unsignedFloat( string $i_stFloat, ?string $i_nstError = null ) : float {
        if ( Validate::unsignedFloat( $i_stFloat ) ) {
            return floatval( $i_stFloat );
        }
        throw new ParseException( $i_nstError ?? "Invalid unsigned float: {$i_stFloat}" );
    }


    public static function unsignedInt( string $i_stInt, ?string $i_nstError = null ) : int {
        if ( Validate::unsignedInt( $i_stInt ) ) {
            return intval( $i_stInt );
        }
        throw new ParseException( $i_nstError ?? "Invalid unsigned integer: {$i_stInt}" );
    }


}
