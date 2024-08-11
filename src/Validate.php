<?php


declare( strict_types = 1 );


namespace JDWX\Param;


final class Validate {


    /** @param array<string, mixed> $i_r */
    public static function arrayKey( string $i_stKey, array $i_r ) : bool {
        return array_key_exists( $i_stKey, $i_r );
    }


    /** @param array<int|string, string> $i_r */
    public static function arrayValue( string $i_stValue, array $i_r ) : bool {
        return in_array( $i_stValue, $i_r );
    }


    public static function bool( string $i_stBool ) : bool {
        if ( is_numeric( $i_stBool ) ) {
            return true;
        }
        return match ( strtolower( trim( $i_stBool ) ) ) {
            'true', 'yes', 'yeah', 'y', 'on', 'false', 'no', 'nope', 'n', 'off' => true,
            default => false,
        };
    }


    public static function currency( string $i_stCurrency ) : bool {
        return is_numeric( $i_stCurrency );
    }


    public static function emailAddress( string $i_stEmail ) : bool {
        return filter_var( $i_stEmail, FILTER_VALIDATE_EMAIL ) !== false;
    }


    public static function emailUsername( string $i_stUsername ) : bool {
        return self::emailAddress( $i_stUsername . '@example.com' );
    }


    public static function existingDirectory( string $i_stDir ) : bool {
        if ( ! self::existingFilename( $i_stDir ) ) {
            return false;
        }
        return is_dir( $i_stDir );
    }


    public static function existingFilename( string $i_stFile ) : bool {
        return file_exists( $i_stFile );
    }


    public static function float( string $i_stFloat ) : bool {
        return is_numeric( $i_stFloat );
    }


    public static function floatRangeClosed( string $i_stFloat, float $i_fMin, float $i_fMax ) : bool {
        if ( ! self::float( $i_stFloat ) ) {
            return false;
        }
        $f = floatval( $i_stFloat );
        return $f > $i_fMin && $f < $i_fMax;
    }


    public static function floatRangeHalfClosed( string $i_stFloat, float $i_fMin, float $i_fMax ) : bool {
        if ( ! self::float( $i_stFloat ) ) {
            return false;
        }
        $f = floatval( $i_stFloat );
        return $f >= $i_fMin && $f < $i_fMax;
    }


    public static function floatRangeOpen( string $i_stFloat, float $i_fMin, float $i_fMax ) : bool {
        if ( ! self::float( $i_stFloat ) ) {
            return false;
        }
        $f = floatval( $i_stFloat );
        return $f >= $i_fMin && $f <= $i_fMax;
    }


    public static function hostname( string $i_stHost ) : bool {
        if ( str_ends_with( $i_stHost, '.' ) ) {
            return false;
        }
        if ( ! str_contains( $i_stHost, '.' ) ) {
            return false;
        }
        return filter_var( $i_stHost, FILTER_VALIDATE_DOMAIN, FILTER_FLAG_HOSTNAME ) !== false;
    }


    public static function int( string $i_stInt ) : bool {
        return is_numeric( $i_stInt );
    }


    public static function intRangeClosed( string $i_stInt, int $i_nuMin, int $i_nuMax ) : bool {
        if ( ! self::int( $i_stInt ) ) {
            return false;
        }
        $nu = intval( $i_stInt );
        return $nu > $i_nuMin && $nu < $i_nuMax;
    }


    public static function intRangeHalfClosed( string $i_stInt, int $i_nuMin, int $i_nuMax ) : bool {
        if ( ! self::int( $i_stInt ) ) {
            return false;
        }
        $nu = intval( $i_stInt );
        return $nu >= $i_nuMin && $nu < $i_nuMax;
    }


    public static function intRangeOpen( string $i_stInt, int $i_nuMin, int $i_nuMax ) : bool {
        if ( ! self::int( $i_stInt ) ) {
            return false;
        }
        $nu = intval( $i_stInt );
        return $nu >= $i_nuMin && $nu <= $i_nuMax;
    }


    public static function ip( string $i_stIP ) : bool {
        return filter_var( $i_stIP, FILTER_VALIDATE_IP ) !== false;
    }


    public static function ipv4( string $i_stIP ) : bool {
        return filter_var( $i_stIP, FILTER_VALIDATE_IP, FILTER_FLAG_IPV4 ) !== false;
    }


    public static function ipv6( string $i_stIP ) : bool {
        return filter_var( $i_stIP, FILTER_VALIDATE_IP, FILTER_FLAG_IPV6 ) !== false;
    }


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


    public static function positiveFloat( string $i_stFloat ) : bool {
        return self::floatRangeOpen( $i_stFloat, PHP_FLOAT_EPSILON, PHP_FLOAT_MAX );
    }


    public static function positiveInt( string $i_stInt ) : bool {
        return self::intRangeOpen( $i_stInt, 1, PHP_INT_MAX );
    }


    public static function unsignedFloat( string $i_stInt ) : bool {
        return self::floatRangeOpen( $i_stInt, 0.0, PHP_FLOAT_MAX );
    }


    public static function unsignedInt( string $i_stInt ) : bool {
        return self::intRangeOpen( $i_stInt, 0, PHP_INT_MAX );
    }


}
