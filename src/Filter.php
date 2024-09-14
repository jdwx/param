<?php


declare( strict_types = 1 );


namespace JDWX\Param;


class Filter {


    public static function currency( string $i_st ) : ?string {
        $st = str_replace( ',', '', $i_st );
        $stPrev = null;
        $bNegative = false;
        $bDollarSign = false;
        while ( $st != $stPrev ) {
            $stPrev = $st;
            if ( str_starts_with( $st, '$' ) ) {
                if ( $bDollarSign ) {
                    return null;
                }
                $st = substr( $st, 1 );
                $bDollarSign = true;
            }
            if ( str_starts_with( $st, '-' ) ) {
                if ( $bNegative ) {
                    return null;
                }
                $bNegative = true;
                $st = substr( $st, 1 );
            }
            if ( str_starts_with( $st, '(' ) && str_ends_with( $st, ')' ) ) {
                if ( $bNegative ) {
                    return null;
                }
                $st = substr( $st, 1, -1 );
                $bNegative = true;
            }
        }
        if ( str_starts_with( $st, '.' ) ) {
            $st = '0' . $st;
        }
        if ( ! preg_match( '/^\d+(\.\d+)?$/', $st ) ) {
            return null;
        }
        if ( $bNegative ) {
            $st = '-' . $st;
        }
        return $st;
    }


}
