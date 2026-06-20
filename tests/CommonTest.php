<?php


declare( strict_types = 1 );


use JDWX\Param\Common;
use JDWX\Param\Validate;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\TestCase;


#[CoversClass( Common::class )]
final class CommonTest extends TestCase {


    public function testDebracket() : void {
        // Matching brackets are stripped; a bare value is returned unchanged.
        self::assertSame( '2001:db8::1', Common::debracket( '[2001:db8::1]' ) );
        self::assertSame( '2001:db8::1', Common::debracket( '2001:db8::1' ) );

        // Both ends must match, otherwise the input is left untouched.
        self::assertSame( '[2001:db8::1', Common::debracket( '[2001:db8::1' ) );
        self::assertSame( '2001:db8::1]', Common::debracket( '2001:db8::1]' ) );

        // Empty input and empty brackets.
        self::assertSame( '', Common::debracket( '' ) );
        self::assertSame( '', Common::debracket( '[]' ) );

        // Custom single- and multi-character delimiters.
        self::assertSame( 'abc', Common::debracket( '(abc)', '(', ')' ) );
        self::assertSame( 'x', Common::debracket( '<<x>>', '<<', '>>' ) );
    }


    /**
     * Guards the bogon/private range constants: every entry must be a valid CIDR
     * block of the expected family (and not the other). This catches typos like
     * a malformed address or a v4 range mistakenly placed in a v6 list.
     */
    public function testIpRangeConstantsAreValidBlocks() : void {
        $rV4 = [
            'IPV4_BOGON_RANGES' => Common::IPV4_BOGON_RANGES,
            'IPV4_PRIVATE_RANGES' => Common::IPV4_PRIVATE_RANGES,
        ];
        foreach ( $rV4 as $stName => $rRanges ) {
            foreach ( $rRanges as $stRange ) {
                self::assertTrue( Validate::ipv4Block( $stRange ), "{$stName}: not a valid IPv4 block: {$stRange}" );
                self::assertFalse( Validate::ipv6Block( $stRange ), "{$stName}: unexpectedly an IPv6 block: {$stRange}" );
            }
        }

        $rV6 = [
            'IPV6_BOGON_RANGES' => Common::IPV6_BOGON_RANGES,
            'IPV6_PRIVATE_RANGES' => Common::IPV6_PRIVATE_RANGES,
        ];
        foreach ( $rV6 as $stName => $rRanges ) {
            foreach ( $rRanges as $stRange ) {
                self::assertTrue( Validate::ipv6Block( $stRange ), "{$stName}: not a valid IPv6 block: {$stRange}" );
                self::assertFalse( Validate::ipv4Block( $stRange ), "{$stName}: unexpectedly an IPv4 block: {$stRange}" );
            }
        }
    }


    public function testIpv4InCIDR() : void {
        // Membership, the whole space, and a single host. The address is assumed
        // already valid -- Common does not re-validate it.
        self::assertTrue( Common::ipv4InCIDR( '192.168.1.50', '192.168.1.0/24' ) );
        self::assertFalse( Common::ipv4InCIDR( '192.168.2.50', '192.168.1.0/24' ) );
        self::assertTrue( Common::ipv4InCIDR( '203.0.113.9', '0.0.0.0/0' ) );
        self::assertTrue( Common::ipv4InCIDR( '192.168.1.5', '192.168.1.5/32' ) );
        self::assertFalse( Common::ipv4InCIDR( '192.168.1.6', '192.168.1.5/32' ) );

        // A /31 block; host bits in the CIDR address are ignored.
        self::assertTrue( Common::ipv4InCIDR( '192.168.1.1', '192.168.1.0/31' ) );
        self::assertFalse( Common::ipv4InCIDR( '192.168.1.2', '192.168.1.0/31' ) );
        self::assertTrue( Common::ipv4InCIDR( '10.1.2.3', '10.255.255.255/8' ) );

        // A list matches any one block; empty, invalid, and null entries are skipped.
        self::assertTrue( Common::ipv4InCIDR( '10.0.0.5', [ '192.168.0.0/16', '10.0.0.0/8' ] ) );
        self::assertFalse( Common::ipv4InCIDR( '172.16.0.5', [ '192.168.0.0/16', '10.0.0.0/8' ] ) );
        self::assertFalse( Common::ipv4InCIDR( '10.0.0.5', [] ) );
        self::assertTrue( Common::ipv4InCIDR( '10.0.0.5', [ 'garbage', null, '10.0.0.0/8' ] ) );

        // Invalid, IPv6, or bare-address blocks do not match.
        self::assertFalse( Common::ipv4InCIDR( '10.0.0.5', 'garbage' ) );
        self::assertFalse( Common::ipv4InCIDR( '10.0.0.5', '2001:db8::/32' ) );
        self::assertFalse( Common::ipv4InCIDR( '10.0.0.5', '10.0.0.0' ) );
    }


    public function testIpv4Mask() : void {
        // A /0 masks everything away; a /32 keeps the whole address.
        self::assertSame( '0.0.0.0', Common::ipv4Mask( '203.0.113.77', 0 ) );
        self::assertSame( '255.255.255.255', Common::ipv4Mask( '255.255.255.255', 32 ) );
        self::assertSame( '203.0.113.77', Common::ipv4Mask( '203.0.113.77', 32 ) );

        // Byte-aligned masks.
        self::assertSame( '10.0.0.0', Common::ipv4Mask( '10.20.30.40', 8 ) );
        self::assertSame( '10.20.0.0', Common::ipv4Mask( '10.20.30.40', 16 ) );
        self::assertSame( '10.20.30.0', Common::ipv4Mask( '10.20.30.40', 24 ) );

        // Masks that fall within a byte.
        self::assertSame( '192.168.1.0', Common::ipv4Mask( '192.168.1.130', 24 ) );
        self::assertSame( '192.168.1.128', Common::ipv4Mask( '192.168.1.130', 25 ) );
        self::assertSame( '192.168.1.128', Common::ipv4Mask( '192.168.1.130', 26 ) );
        self::assertSame( '10.16.0.0', Common::ipv4Mask( '10.20.30.40', 12 ) );
        self::assertSame( '0.0.0.0', Common::ipv4Mask( '10.20.30.40', 1 ) );
        self::assertSame( '10.20.30.40', Common::ipv4Mask( '10.20.30.40', 31 ) );

        // Every prefix length against the all-ones address reproduces the netmask.
        self::assertSame( '128.0.0.0', Common::ipv4Mask( '255.255.255.255', 1 ) );
        self::assertSame( '255.255.255.254', Common::ipv4Mask( '255.255.255.255', 31 ) );
    }


    public function testIpv4NotBogon() : void {
        // Globally routable addresses are not bogons.
        self::assertTrue( Common::ipv4NotBogon( '8.8.8.8' ) );
        self::assertTrue( Common::ipv4NotBogon( '1.1.1.1' ) );

        // A representative address from each bogon range is rejected.
        self::assertFalse( Common::ipv4NotBogon( '0.0.0.0' ) );
        self::assertFalse( Common::ipv4NotBogon( '10.1.2.3' ) );
        self::assertFalse( Common::ipv4NotBogon( '100.64.0.1' ) );
        self::assertFalse( Common::ipv4NotBogon( '127.0.0.1' ) );
        self::assertFalse( Common::ipv4NotBogon( '169.254.1.1' ) );
        self::assertFalse( Common::ipv4NotBogon( '172.16.5.5' ) );
        self::assertFalse( Common::ipv4NotBogon( '192.168.1.1' ) );
        self::assertFalse( Common::ipv4NotBogon( '198.51.100.7' ) );
        self::assertFalse( Common::ipv4NotBogon( '203.0.113.7' ) );
        self::assertFalse( Common::ipv4NotBogon( '224.0.0.1' ) );
        self::assertFalse( Common::ipv4NotBogon( '255.255.255.255' ) );

        // Localhost: only the exact 127.0.0.1, and only with the opt-in.
        self::assertTrue( Common::ipv4NotBogon( '127.0.0.1', i_bAllowLocalhost: true ) );
        self::assertFalse( Common::ipv4NotBogon( '127.0.0.2', i_bAllowLocalhost: true ) );

        // Private only with the opt-in, which does not rescue non-private bogons.
        self::assertTrue( Common::ipv4NotBogon( '10.1.2.3', i_bAllowPrivate: true ) );
        self::assertTrue( Common::ipv4NotBogon( '192.168.1.1', i_bAllowPrivate: true ) );
        self::assertFalse( Common::ipv4NotBogon( '203.0.113.7', i_bAllowPrivate: true ) );
    }


    public function testIpv4Private() : void {
        // Each RFC 1918 range, and the boundaries around 172.16.0.0/12.
        self::assertTrue( Common::ipv4Private( '10.1.2.3' ) );
        self::assertTrue( Common::ipv4Private( '172.16.5.5' ) );
        self::assertTrue( Common::ipv4Private( '192.168.1.1' ) );
        self::assertTrue( Common::ipv4Private( '172.31.255.255' ) );
        self::assertFalse( Common::ipv4Private( '172.32.0.0' ) );
        self::assertFalse( Common::ipv4Private( '172.15.255.255' ) );

        // Public, loopback, and CGNAT are not private.
        self::assertFalse( Common::ipv4Private( '8.8.8.8' ) );
        self::assertFalse( Common::ipv4Private( '127.0.0.1' ) );
        self::assertFalse( Common::ipv4Private( '100.64.0.1' ) );

        // Loopback only when explicitly included.
        self::assertTrue( Common::ipv4Private( '127.0.0.1', i_bIncludeLocalhost: true ) );
        self::assertFalse( Common::ipv4Private( '127.0.0.2', i_bIncludeLocalhost: true ) );
    }


    public function testIpv6InCIDR() : void {
        // Membership, the whole space, and a single host.
        self::assertTrue( Common::ipv6InCIDR( '2001:db8::1', '2001:db8::/32' ) );
        self::assertFalse( Common::ipv6InCIDR( '2001:dead::1', '2001:db8::/32' ) );
        self::assertTrue( Common::ipv6InCIDR( '::1', '::/0' ) );
        self::assertTrue( Common::ipv6InCIDR( '2001:db8::1', '2001:db8::1/128' ) );

        // A non-byte-aligned prefix; host bits in the CIDR address are ignored.
        self::assertTrue( Common::ipv6InCIDR( '2001:db8:a000::1', '2001:db8:abcd::/36' ) );
        self::assertFalse( Common::ipv6InCIDR( '2001:db8:b000::1', '2001:db8:abcd::/36' ) );
        self::assertTrue( Common::ipv6InCIDR( '2001:db8:abcd::1', '2001:db8:ffff::ffff/32' ) );

        // The CIDR block address may be bracketed (the IP is assumed already clean).
        self::assertTrue( Common::ipv6InCIDR( '2001:db8::1', '[2001:db8::]/32' ) );

        // A list matches any one block; empty, invalid, and null entries are skipped.
        self::assertTrue( Common::ipv6InCIDR( '2001:db8::1', [ 'fd00::/8', '2001:db8::/32' ] ) );
        self::assertFalse( Common::ipv6InCIDR( '2001:dead::1', [ 'fd00::/8', '2001:db8::/32' ] ) );
        self::assertFalse( Common::ipv6InCIDR( '2001:db8::1', [] ) );
        self::assertTrue( Common::ipv6InCIDR( '2001:db8::1', [ 'garbage', null, '2001:db8::/32' ] ) );

        // Invalid, IPv4, or bare-address blocks do not match.
        self::assertFalse( Common::ipv6InCIDR( '2001:db8::1', 'garbage' ) );
        self::assertFalse( Common::ipv6InCIDR( '2001:db8::1', '10.0.0.0/8' ) );
        self::assertFalse( Common::ipv6InCIDR( '2001:db8::1', '2001:db8::' ) );
    }


    public function testIpv6Mask() : void {
        // A /0 masks everything away; a /128 keeps the whole address.
        self::assertSame( '::', Common::ipv6Mask( '2001:db8::1', 0 ) );
        self::assertSame( '::1', Common::ipv6Mask( '::1', 128 ) );
        self::assertSame( '2001:db8::1', Common::ipv6Mask( '2001:db8::1', 128 ) );

        // Byte-aligned masks.
        self::assertSame( '2001:db8::', Common::ipv6Mask( '2001:db8::1', 32 ) );
        self::assertSame(
            '2001:db8:abcd:1230::',
            Common::ipv6Mask( '2001:db8:abcd:1234:5678:9abc:def0:1234', 60 )
        );

        // Masks that fall within a 16-bit group (non-byte-aligned).
        self::assertSame( '2001:db8:8000::', Common::ipv6Mask( '2001:db8:abcd:1234::1', 33 ) );
        self::assertSame( '2001:db8:a000::', Common::ipv6Mask( '2001:db8:abcd:1234::1', 36 ) );
        self::assertSame( 'fe80::', Common::ipv6Mask( 'fe80::1', 10 ) );

        // The all-1s address reproduces the netmask at representative lengths.
        $all = 'ffff:ffff:ffff:ffff:ffff:ffff:ffff:ffff';
        self::assertSame( '8000::', Common::ipv6Mask( $all, 1 ) );
        self::assertSame( 'ffff:ffff:ffff:ffff::', Common::ipv6Mask( $all, 64 ) );
        self::assertSame( 'ffff:ffff:ffff:ffff:ffff:ffff:ffff:fffe', Common::ipv6Mask( $all, 127 ) );
    }


    public function testIpv6NotBogon() : void {
        // Globally routable addresses are not bogons.
        self::assertTrue( Common::ipv6NotBogon( '2606:4700::1111' ) );
        self::assertTrue( Common::ipv6NotBogon( '2620:fe::fe' ) );

        // A representative address from each bogon range is rejected, including
        // the RFC 3849 documentation prefix.
        self::assertFalse( Common::ipv6NotBogon( '::' ) );
        self::assertFalse( Common::ipv6NotBogon( '::1' ) );
        self::assertFalse( Common::ipv6NotBogon( '::ffff:192.168.1.1' ) );
        self::assertFalse( Common::ipv6NotBogon( '2001:db8::1' ) );
        self::assertFalse( Common::ipv6NotBogon( '2002::1' ) );
        self::assertFalse( Common::ipv6NotBogon( 'fc00::1' ) );
        self::assertFalse( Common::ipv6NotBogon( 'fe80::1' ) );

        // Localhost only with the opt-in, in any equivalent form.
        self::assertTrue( Common::ipv6NotBogon( '::1', i_bAllowLocalhost: true ) );
        self::assertTrue( Common::ipv6NotBogon( '0:0:0:0:0:0:0:1', i_bAllowLocalhost: true ) );

        // Private only with the opt-in, which does not rescue non-private bogons.
        self::assertTrue( Common::ipv6NotBogon( 'fc00::1', i_bAllowPrivate: true ) );
        self::assertTrue( Common::ipv6NotBogon( 'fe80::1', i_bAllowPrivate: true ) );
        self::assertFalse( Common::ipv6NotBogon( '2002::1', i_bAllowPrivate: true ) );
    }


    public function testIpv6Private() : void {
        // Unique local addresses (fc00::/7) and link-local (fe80::/10).
        self::assertTrue( Common::ipv6Private( 'fc00::1' ) );
        self::assertTrue( Common::ipv6Private( 'fd12:3456::1' ) );
        self::assertTrue( Common::ipv6Private( 'fe80::1' ) );

        // Public and loopback (without the opt-in) are not private.
        self::assertFalse( Common::ipv6Private( '2606:4700::1111' ) );
        self::assertFalse( Common::ipv6Private( '::1' ) );

        // Loopback only when explicitly included, in any equivalent form.
        self::assertTrue( Common::ipv6Private( '::1', i_bIncludeLocalhost: true ) );
        self::assertTrue( Common::ipv6Private( '0:0:0:0:0:0:0:1', i_bIncludeLocalhost: true ) );
    }


}
