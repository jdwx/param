<?php


declare( strict_types = 1 );


namespace JDWX\Param;


use JDWX\Strict\OK;


class Common {


    /**
     * These are IPv4 ranges that are not routable on the public Internet. It is *not*
     * a list of invalid ranges or ranges that you might not observe, especially
     * because the loopback range is on this list. Based on RFC 5735.
     *
     * @var list<string>
     */
    public const array IPV4_BOGON_RANGES = [
        '0.0.0.0/8', # RFC 1122 "This network"
        '10.0.0.0/8', # RFC 1918 Private address
        '100.64.0.0/10', # RFC 6598 Shared address space (Carrier-grade NAT)
        '127.0.0.0/8', # RFC 1122 Loopback
        '169.254.0.0/16', # RFC 3927 Link-local address
        '172.16.0.0/12', # RFC 1918 Private address
        '192.0.0.0/24', # RFC 5736 IETF protocol assignments
        '192.0.2.0/24', # RFC 5737 TEST-NET-1
        '192.168.0.0/16', # RFC 1918 Private address
        '198.18.0.0/15', # RFC 2544 Network interconnection benchmark testing
        '198.51.100.0/24', # RFC 5737 TEST-NET-2
        '203.0.113.0/24', # RFC 5737 TEST-NET-3
        '224.0.0.0/4', # RFC 3171 Multicast
        '240.0.0.0/4', # RFC 1112 Reserved ("Class E")
        '255.255.255.255/32', # RFC 919/922 Limited broadcast destination address
    ];


    /** @var list<string> */
    public const array IPV4_PRIVATE_RANGES = [
        '10.0.0.0/8',
        '172.16.0.0/12',
        '192.168.0.0/16',
    ];


    /**
     * These are IPv6 address ranges that are deemed "not globally
     * reachable" by IANA.
     *
     * @var list<string>
     */
    public const array IPV6_BOGON_RANGES = [
        '::/128', # RFC 4291 unicast "unspecified" address
        '::1/128', # RFC 4291 unicast "loopback" address
        '::ffff:0:0/96', # RFC 4291 IPv4-mapped IPv6 address
        '64:ff9b:1::/48', # RFC 8215 IPv4-IPv6 translation
        '100::/64', # RFC 6666 "discard-only" prefix
        '100:0:0:1::/64', # RFC 9780 dummy prefix
        '2001::/23', # RFC 2928 IETF Protocol assignments
        '2001:2::/48', # RFC 5180 Benchmarking
        '2001:10::/28', # Deprecated (previously "ORCHID")
        '2001:db8::/32', # RFC 3849 documentation
        '2002::/16', # RFC 3056 IPv6 transition (6to4)
        '3fff::/20', # RFC 9637 Documentation
        '5f00::/16', # RFC 9602 Segment Routing (SRv6) SIDs
        'fc00::/7', # RFC 4193/8190 Unique Local Addresses
        'fe80::/10', # RFC 4291 IPv6 link-local unicast
    ];


    /** @var list<string> */
    public const array IPV6_PRIVATE_RANGES = [
        'fc00::/7',
        'fe80::/10',
    ];


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
     * @param string                   $i_stIP The valid IP address to check.
     * @param list<string|null>|string $i_cidr The CIDR notation(s) to check against.
     * @return bool
     */
    public static function ipInCIDR( string $i_stIP, array|string $i_cidr ) : bool {
        return str_contains( $i_stIP, ':' )
            ? self::ipv6InCIDR( $i_stIP, $i_cidr )
            : self::ipv4InCIDR( $i_stIP, $i_cidr );
    }


    /**
     * @param string $i_stIP            The valid IP address to check.
     * @param bool   $i_bAllowLocalhost Whether to allow localhost IPs.
     * @param bool   $i_bAllowPrivate   Whether to allow private IPs.
     * @return bool True if the IP is not bogon, otherwise false.
     */
    public static function ipNotBogon( string $i_stIP, bool $i_bAllowLocalhost = false,
                                       bool   $i_bAllowPrivate = false ) : bool {
        return str_contains( $i_stIP, ':' )
            ? self::ipv6NotBogon( $i_stIP, $i_bAllowLocalhost, $i_bAllowPrivate )
            : self::ipv4NotBogon( $i_stIP, $i_bAllowLocalhost, $i_bAllowPrivate );
    }


    /**
     * @param string $i_stIP The valid IP address to check.
     * @return bool True if the IP is private, otherwise false.
     */
    public static function ipPrivate( string $i_stIP, bool $i_bAllowLocalhost = false ) : bool {
        return str_contains( $i_stIP, ':' )
            ? self::ipv6Private( $i_stIP, $i_bAllowLocalhost )
            : self::ipv4Private( $i_stIP, $i_bAllowLocalhost );
    }


    /**
     * This method assumes that the input IPv4 address has already been validated. It does *not*
     * require that the input CIDR block(s) all be validated. but only valid CIDR blocks are checked.
     *
     * @param string                   $i_stIP A valid IPv4 address to check.
     * @param list<string|null>|string $i_cidr The CIDR block or list of CIDR blocks to check against.
     * @return bool True if the IPv4 address is within the CIDR block(s), false otherwise.
     */
    public static function ipv4InCIDR( string $i_stIP, array|string $i_cidr ) : bool {
        if ( is_array( $i_cidr ) ) {
            foreach ( $i_cidr as $cidr ) {
                if ( is_string( $cidr ) && self::ipv4InCIDR( $i_stIP, $cidr ) ) {
                    return true;
                }
            }
            return false;
        }
        if ( ! Validate::ipv4Block( $i_cidr ) ) {
            return false;
        }
        $r = explode( '/', $i_cidr, 2 );
        $stCidrAddress = $r[ 0 ];
        $uBits = intval( $r[ 1 ] );
        return Common::ipv4Mask( $i_stIP, $uBits ) === Common::ipv4Mask( $stCidrAddress, $uBits );

    }


    /**
     * Applies a subnet mask to an IPv4 address and returns the masked IP.
     *
     * The behavior is undefined if the inputs are not valid.
     *
     * @param string $i_stIP  The valid IPv4 address to mask.
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
     * @param string $i_stIP A valid IPv4 address to check.
     * @param bool   $i_bAllowLocalhost
     * @param bool   $i_bAllowPrivate
     * @return bool
     */
    public static function ipv4NotBogon( string $i_stIP, bool $i_bAllowLocalhost = false,
                                         bool   $i_bAllowPrivate = false ) : bool {
        if ( $i_bAllowLocalhost && '127.0.0.1' === $i_stIP ) {
            return true;
        }
        if ( $i_bAllowPrivate && Common::ipv4InCIDR( $i_stIP, self::IPV4_PRIVATE_RANGES ) ) {
            return true;
        }
        return ! Common::ipv4InCIDR( $i_stIP, self::IPV4_BOGON_RANGES );
    }


    /**
     * @param string $i_stIP A valid IPv4 address to check.
     * @param bool   $i_bIncludeLocalhost
     * @return bool
     */
    public static function ipv4Private( string $i_stIP, bool $i_bIncludeLocalhost = false ) : bool {
        if ( $i_bIncludeLocalhost && '127.0.0.1' === $i_stIP ) {
            return true;
        }
        return self::ipv4InCIDR( $i_stIP, self::IPV4_PRIVATE_RANGES );
    }


    /**
     * This method assumes that the input IPv6 address has already been validated. It does *not*
     * require that the input CIDR block(s) all be validated. but only valid CIDR blocks are checked.
     *
     * @param string                   $i_stIP The valid IPv6 address to check.
     * @param list<string|null>|string $i_cidr The CIDR block or list of CIDR blocks to check against.
     * @return bool True if the IPv6 address is within the CIDR block(s), false otherwise.
     */
    public static function ipv6InCIDR( string $i_stIP, array|string $i_cidr ) : bool {
        if ( is_array( $i_cidr ) ) {
            foreach ( $i_cidr as $cidr ) {
                if ( is_string( $cidr ) && self::ipv6InCIDR( $i_stIP, $cidr ) ) {
                    return true;
                }
            }
            return false;
        }
        if ( ! Validate::ipv6Block( $i_cidr ) ) {
            return false;
        }
        $r = explode( '/', $i_cidr, 2 );
        $stCidrAddress = Common::debracket( $r[ 0 ] );
        $uBits = intval( $r[ 1 ] );
        return Common::ipv6Mask( $i_stIP, $uBits ) === Common::ipv6Mask( $stCidrAddress, $uBits );
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


    /**
     * @param string $i_stIP The valid IPv6 address to check.
     * @param bool   $i_bAllowLocalhost
     * @param bool   $i_bAllowPrivate
     * @return bool
     */
    public static function ipv6NotBogon( string $i_stIP, bool $i_bAllowLocalhost = false,
                                         bool   $i_bAllowPrivate = false ) : bool {
        if ( $i_bAllowLocalhost && self::ipv6InCIDR( $i_stIP, '::1/128' ) ) {
            return true;
        }
        if ( $i_bAllowPrivate && self::ipv6InCIDR( $i_stIP, self::IPV6_PRIVATE_RANGES ) ) {
            return true;
        }
        return ! self::ipv6InCIDR( $i_stIP, self::IPV6_BOGON_RANGES );
    }


    /**
     * @param string $i_stIP The valid IPv6 address to check.
     * @param bool   $i_bIncludeLocalhost
     * @return bool
     */
    public static function ipv6Private( string $i_stIP, bool $i_bIncludeLocalhost = false ) : bool {
        if ( $i_bIncludeLocalhost && self::ipv6InCIDR( $i_stIP, '::1/128' ) ) {
            return true;
        }
        return self::ipv6InCIDR( $i_stIP, self::IPV6_PRIVATE_RANGES );
    }


}
