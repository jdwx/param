<?php


declare( strict_types = 1 );


namespace JDWX\Param;


use JDWX\Strict\OK;


class Common {


    /**
     * Removes the specified left and right brackets from the input string if they exist at the start and end, respectively.
     *
     * @param string $i_stInput The input string to process.
     * @param string $i_stLeft  The left bracket character. Defaults to '['.
     * @param string $i_stRight The right bracket character. Defaults to ']'.
     *
     * @return string The processed string with brackets removed if present; otherwise, the input string unchanged.
     */
    public static function debracket( string $i_stInput, string $i_stLeft = '[', string $i_stRight = ']' ) : string {
        if ( str_starts_with( $i_stInput, $i_stLeft ) && str_ends_with( $i_stInput, $i_stRight ) ) {
            return substr( $i_stInput, strlen( $i_stLeft ), -strlen( $i_stRight ) );
        }
        return $i_stInput;
    }


    /**
     * Applies a subnet mask to an IPv4 address and returns the masked IP.
     *
     * The behavior is undefined if the inputs are not valid.
     *
     * @param string $i_stIP  The IPv4 address to mask.
     * @param int    $i_uMask The subnet mask length, ranging from 0 to 32.
     *
     * @return string The masked IPv4 address.
     */
    public static function ipv4Mask( string $i_stIP, int $i_uMask ) : string {
        $bitMask = ( ( 1 << $i_uMask ) - 1 ) << ( 32 - $i_uMask );
        $y = unpack( 'N', OK::inet_pton( $i_stIP ) );
        $y = array_shift( $y );
        return OK::inet_ntop( pack( 'N', $y & $bitMask ) );
    }


    /**
     * Applies an IPv6 network mask to an IP address, masking out bits based on the provided prefix length.
     *
     * The behavior is undefined if the inputs are not valid.
     *
     * @param string $i_stIP  The IPv6 address in string format to be masked.
     * @param int    $i_uMask The prefix length of the network mask (between 0 and 128).
     *
     * @return string The resulting IPv6 address after applying the specified network mask.
     */
    public static function ipv6Mask( string $i_stIP, int $i_uMask ) : string {
        $stAddressBytes = OK::inet_pton( $i_stIP );
        $uFullBytesOfMask = intdiv( $i_uMask, 8 );
        $uExtraBitsOfMask = $i_uMask % 8;
        $stOutputAddress = substr( $stAddressBytes, 0, $uFullBytesOfMask );
        if ( $uFullBytesOfMask < 16 ) {
            if ( $uExtraBitsOfMask > 0 ) {
                $uPartialByte = ord( $stAddressBytes[ $uFullBytesOfMask ] );
                $uPartialMask = ( 0xFF << ( 8 - $uExtraBitsOfMask ) ) & 0xFF;
                $stOutputAddress .= chr( $uPartialByte & $uPartialMask );
                $uFullBytesOfMask++;
            }
            $stOutputAddress .= str_repeat( "\0", 16 - $uFullBytesOfMask );
        }
        return OK::inet_ntop( $stOutputAddress );
    }


}
