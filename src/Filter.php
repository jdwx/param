<?php


declare( strict_types = 1 );


namespace JDWX\Param;


/**
 * Static utility class for filtering and normalizing string inputs.
 * 
 * The Filter class provides methods to clean and normalize string inputs
 * into standard formats. Unlike Parse and Validate classes, these methods
 * return null if the input cannot be processed rather than throwing exceptions
 * or returning false. This class is used by Parse methods to clean inputs
 * before validation.
 */
class Filter {


    /**
     * Filters and normalizes a currency string.
     * 
     * Removes commas, handles dollar signs, negative signs, and parentheses
     * for negative amounts. Returns a normalized numeric string or null if invalid.
     * 
     * Examples:
     * - "$1,234.56" → "1234.56"
     * - "($1.23)" → "-1.23"
     * - "-$5.00" → "-5.00"
     * 
     * @param string $i_st The currency string to filter
     * @return string|null The normalized currency string, or null if invalid
     */
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


    /**
     * Filters and normalizes a date string to YYYY-MM-DD format.
     * 
     * Accepts various date formats and converts them to the standard
     * ISO 8601 date format (YYYY-MM-DD).
     * 
     * @param string $i_st The date string to filter
     * @return string|null The normalized date string in YYYY-MM-DD format, or null if invalid
     */
    public static function date( string $i_st ) : ?string {
        $bst = strtotime( $i_st );
        if ( $bst === false ) {
            return null;
        }
        return gmdate( 'Y-m-d', $bst );
    }


    /**
     * Filters and normalizes a date-time string to YYYY-MM-DD HH:MM:SS format.
     * 
     * Accepts various date-time formats and converts them to the standard
     * format (YYYY-MM-DD HH:MM:SS) in GMT timezone.
     * 
     * @param string $i_st The date-time string to filter
     * @return string|null The normalized date-time string, or null if invalid
     */
    public static function dateTime( string $i_st ) : ?string {
        $bst = strtotime( $i_st );
        if ( $bst === false ) {
            return null;
        }
        return gmdate( 'Y-m-d H:i:s', $bst );
    }


    /**
     * Filters and normalizes a time string to HH:MM:SS format.
     * 
     * Accepts various time formats and converts them to the standard
     * 24-hour format (HH:MM:SS).
     * 
     * @param string $i_st The time string to filter
     * @return string|null The normalized time string in HH:MM:SS format, or null if invalid
     */
    public static function time( string $i_st ) : ?string {
        $bst = strtotime( $i_st );
        if ( $bst === false ) {
            return null;
        }
        return gmdate( 'H:i:s', $bst );
    }


}
