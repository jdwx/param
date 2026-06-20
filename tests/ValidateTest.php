<?php


declare( strict_types = 1 );


use JDWX\Param\Validate;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\TestCase;


#[CoversClass( Validate::class )]
final class ValidateTest extends TestCase {


    public function testArrayKey() : void {
        self::assertTrue( Validate::arrayKey( 'foo', [ 'foo' => 'bar' ] ) );
        self::assertFalse( Validate::arrayKey( 'bar', [ 'foo' => 'bar' ] ) );
        self::assertFalse( Validate::arrayKey( 'baz', [ 'foo' => 'bar' ] ) );
        self::assertFalse( Validate::arrayKey( null, [ 'foo' => 'bar' ] ) );
    }


    public function testArrayValue() : void {
        self::assertFalse( Validate::arrayValue( 'foo', [ 'foo' => 'bar' ] ) );
        self::assertTrue( Validate::arrayValue( 'bar', [ 'foo' => 'bar' ] ) );
        self::assertFalse( Validate::arrayValue( 'baz', [ 'foo' => 'bar' ] ) );
    }


    public function testBool() : void {
        self::assertTrue( Validate::bool( 'true' ) );
        self::assertTrue( Validate::bool( 'yes' ) );
        self::assertTrue( Validate::bool( 'yeah' ) );
        self::assertTrue( Validate::bool( 'y' ) );
        self::assertTrue( Validate::bool( 'on' ) );
        self::assertTrue( Validate::bool( '1' ) );
        self::assertTrue( Validate::bool( '2' ) );

        self::assertTrue( Validate::bool( 'false' ) );
        self::assertTrue( Validate::bool( 'no' ) );
        self::assertTrue( Validate::bool( 'nope' ) );
        self::assertTrue( Validate::bool( 'n' ) );
        self::assertTrue( Validate::bool( 'off' ) );
        self::assertTrue( Validate::bool( '0' ) );

        self::assertFalse( Validate::bool( '' ) );
        self::assertFalse( Validate::bool( 'foo' ) );
        self::assertFalse( Validate::bool( '!' ) );

        self::assertTrue( Validate::bool( 'TRUE' ) );

        self::assertFalse( Validate::bool( null ) );
    }


    public function testCurrency() : void {
        self::assertTrue( Validate::currency( '1234.56' ) );
        self::assertTrue( Validate::currency( '-1234.56' ) );
        self::assertTrue( Validate::currency( '1,234.56' ) );
        self::assertTrue( Validate::currency( '-1,234.56' ) );
        self::assertTrue( Validate::currency( '$1234.56' ) );
        self::assertTrue( Validate::currency( '-$1234.56' ) );
        self::assertTrue( Validate::currency( '$-1234.56' ) );
        self::assertTrue( Validate::currency( '$1,234.56' ) );
        self::assertTrue( Validate::currency( '-$1,234.56' ) );
        self::assertTrue( Validate::currency( '(1234.56)' ) );
        self::assertTrue( Validate::currency( '(1,234.56)' ) );
        self::assertTrue( Validate::currency( '($1234.56)' ) );
        self::assertTrue( Validate::currency( '($1,234.56)' ) );
        self::assertFalse( Validate::currency( '(-$1,234.56)' ) );
        self::assertTrue( Validate::currency( '1234' ) );
        self::assertFalse( Validate::currency( null ) );
    }


    public function testDateTime() : void {
        self::assertTrue( Validate::dateTime( '2024-01-25' ) );
        self::assertTrue( Validate::dateTime( '2024-01-26 + 2 days' ) );
        self::assertFalse( Validate::dateTime( 'foo' ) );
        self::assertFalse( Validate::dateTime( null ) );
    }


    public function testEmailAddress() : void {
        self::assertTrue( Validate::emailAddress( 'test@example.com' ) );
        self::assertFalse( Validate::emailAddress( 'invalid-email' ) );
        self::assertFalse( Validate::emailAddress( 'missing@domain' ) );
        self::assertFalse( Validate::emailAddress( 'no-at-symbol' ) );
        self::assertFalse( Validate::emailAddress( 'missing@domain.' ) );
        self::assertFalse( Validate::emailAddress( '@missing-domain' ) );
        self::assertFalse( Validate::emailAddress( null ) );
    }


    public function testEmailUsername() : void {
        self::assertTrue( Validate::emailUsername( 'test' ) );
        self::assertTrue( Validate::emailUsername( 'test+folder' ) );
        self::assertFalse( Validate::emailUsername( 'test@domain' ) );
        self::assertFalse( Validate::emailUsername( 'test@' ) );
        self::assertFalse( Validate::emailUsername( '@test' ) );
        self::assertFalse( Validate::emailUsername( '' ) );
        self::assertFalse( Validate::emailUsername( 'test with space' ) );
        self::assertFalse( Validate::emailUsername( null ) );
    }


    public function testExistingDirectory() : void {
        self::assertFalse( Validate::existingDirectory( __FILE__ ) );
        self::assertTrue( Validate::existingDirectory( __DIR__ ) );
        self::assertFalse( Validate::existingDirectory( '/dev/null' ) );
        self::assertFalse( Validate::existingDirectory( '/no/such/dir' ) );
        self::assertFalse( Validate::existingDirectory( null ) );
    }


    public function testExistingFilename() : void {
        self::assertTrue( Validate::existingFilename( __FILE__ ) );
        self::assertFalse( Validate::existingFilename( __DIR__ ) );
        self::assertFalse( Validate::existingFilename( '/dev/null' ) );
        self::assertFalse( Validate::existingFilename( '/no/such/file' ) );
        self::assertFalse( Validate::existingFilename( null ) );
    }


    public function testExistingPath() : void {
        self::assertTrue( Validate::existingPath( __FILE__ ) );
        self::assertTrue( Validate::existingPath( __DIR__ ) );
        self::assertTrue( Validate::existingPath( '/dev/null' ) );
        self::assertFalse( Validate::existingPath( '/no/such/file' ) );
        self::assertFalse( Validate::existingPath( null ) );
    }


    public function testFQDN() : void {
        self::assertTrue( Validate::fqdn( 'localhost' ) );
        self::assertTrue( Validate::fqdn( '_acme-challenge._well-known.example.org' ) );
        self::assertFalse( Validate::fqdn( 'not an fqdn' ) );
        self::assertFalse( Validate::fqdn( null ) );
    }


    public function testFloat() : void {
        self::assertTrue( Validate::float( '123.45' ) );
        self::assertTrue( Validate::float( '0.0' ) );
        self::assertTrue( Validate::float( '-123.45' ) );
        self::assertFalse( Validate::float( 'not a float' ) );
        self::assertFalse( Validate::float( null ) );
    }


    public function testFloatRangeClosed() : void {
        self::assertTrue( Validate::floatRangeClosed( '12.345', 0, 100 ) );
        self::assertFalse( Validate::floatRangeClosed( '0.0', 0, 100 ) );
        self::assertTrue( Validate::floatRangeClosed( '0.000001', 0, 100 ) );
        self::assertFalse( Validate::floatRangeClosed( '-0.0', 0, 100 ) );
        self::assertTrue( Validate::floatRangeClosed( '-0.000001', -100, 0 ) );
        self::assertTrue( Validate::floatRangeClosed( '-12.345', -100, 0 ) );
        self::assertFalse( Validate::floatRangeClosed( 'not a float', 0, 100 ) );
        self::assertFalse( Validate::floatRangeClosed( null, 0, 100 ) );
        self::assertFalse( Validate::floatRangeClosed( '123.45', 0, 100 ) );
        self::assertFalse( Validate::floatRangeClosed( '-123.45', -100, -0 ) );
    }


    public function testFloatRangeHalfClosed() : void {
        self::assertTrue( Validate::floatRangeHalfClosed( '12.345', 0, 100 ) );
        self::assertTrue( Validate::floatRangeHalfClosed( '0.0', 0, 100 ) );
        self::assertFalse( Validate::floatRangeHalfClosed( '0.0', -100, 0 ) );
        self::assertTrue( Validate::floatRangeHalfClosed( '0.000001', 0, 100 ) );
        self::assertTrue( Validate::floatRangeHalfClosed( '-0.0', 0, 100 ) );
        self::assertTrue( Validate::floatRangeHalfClosed( '-0.000001', -100, 0 ) );
        self::assertTrue( Validate::floatRangeHalfClosed( '-12.345', -100, 0 ) );
        self::assertFalse( Validate::floatRangeHalfClosed( 'not a float', 0, 100 ) );
        self::assertFalse( Validate::floatRangeHalfClosed( null, 0, 100 ) );
        self::assertFalse( Validate::floatRangeHalfClosed( '123.45', 0, 100 ) );
        self::assertFalse( Validate::floatRangeHalfClosed( '-123.45', -100, -0 ) );
    }


    public function testFloatRangeOpen() : void {
        self::assertTrue( Validate::floatRangeOpen( '12.345', 0, 100 ) );
        self::assertTrue( Validate::floatRangeOpen( '0.0', 0, 100 ) );
        self::assertTrue( Validate::floatRangeOpen( '0.000001', 0, 100 ) );
        self::assertTrue( Validate::floatRangeOpen( '-0.0', 0, 100 ) );
        self::assertTrue( Validate::floatRangeOpen( '-0.000001', -100, 0 ) );
        self::assertTrue( Validate::floatRangeOpen( '-12.345', -100, 0 ) );
        self::assertFalse( Validate::floatRangeOpen( 'not a float', 0, 100 ) );
        self::assertFalse( Validate::floatRangeOpen( null, 0, 100 ) );
        self::assertFalse( Validate::floatRangeOpen( '123.45', 0, 100 ) );
        self::assertFalse( Validate::floatRangeOpen( '-123.45', -100, -0 ) );
    }


    public function testHostname() : void {
        self::assertTrue( Validate::hostname( 'localhost' ) );
        self::assertTrue( Validate::hostname( 'example.com' ) );
        self::assertTrue( Validate::hostname( 'www.example.com' ) );
        self::assertTrue( Validate::hostname( 'a.very.long.and.complex.example.com' ) );
        self::assertFalse( Validate::hostname( 'example.com.' ) );
        self::assertFalse( Validate::hostname( 'example' ) );
        self::assertFalse( Validate::hostname( 'example.' ) );
        self::assertFalse( Validate::hostname( 'foo!bar.com' ) );
        self::assertFalse( Validate::hostname( null ) );
    }


    public function testInt() : void {
        self::assertTrue( Validate::int( '123' ) );
        self::assertTrue( Validate::int( '-123' ) );
        self::assertTrue( Validate::int( '-123.' ) );
        self::assertTrue( Validate::int( '-123.0' ) );
        self::assertFalse( Validate::int( '-123.45' ) );
        self::assertTrue( Validate::int( '0' ) );
        self::assertTrue( Validate::int( '-0' ) );
        self::assertFalse( Validate::int( 'not a number' ) );
        self::assertFalse( Validate::int( null ) );
    }


    public function testIntRangeClosed() : void {
        self::assertTrue( Validate::intRangeClosed( '12', 0, 100 ) );
        self::assertTrue( Validate::intRangeClosed( '-12', -100, 0 ) );
        self::assertFalse( Validate::intRangeClosed( '0', 0, 100 ) );
        self::assertFalse( Validate::intRangeClosed( '100', 0, 100 ) );
        self::assertFalse( Validate::intRangeClosed( '0', -100, 0 ) );
        self::assertFalse( Validate::intRangeClosed( '-100', -100, 0 ) );
        self::assertFalse( Validate::intRangeClosed( 'not a number', -100, 100 ) );
        self::assertFalse( Validate::intRangeClosed( '', -100, 100 ) );
        self::assertFalse( Validate::intRangeClosed( null, -100, 100 ) );
    }


    public function testIntRangeHalfClosed() : void {
        self::assertTrue( Validate::intRangeHalfClosed( '12', 0, 100 ) );
        self::assertTrue( Validate::intRangeHalfClosed( '-12', -100, 0 ) );
        self::assertTrue( Validate::intRangeHalfClosed( '0', 0, 100 ) );
        self::assertFalse( Validate::intRangeHalfClosed( '100', 0, 100 ) );
        self::assertFalse( Validate::intRangeHalfClosed( '0', -100, 0 ) );
        self::assertTrue( Validate::intRangeHalfClosed( '-100', -100, 0 ) );
        self::assertFalse( Validate::intRangeHalfClosed( 'not a number', -100, 100 ) );
        self::assertFalse( Validate::intRangeHalfClosed( '', -100, 100 ) );
        self::assertFalse( Validate::intRangeHalfClosed( null, -100, 100 ) );
    }


    public function testIntRangeOpen() : void {
        self::assertTrue( Validate::intRangeOpen( '12', 0, 100 ) );
        self::assertTrue( Validate::intRangeOpen( '-12', -100, 0 ) );
        self::assertTrue( Validate::intRangeOpen( '0', 0, 100 ) );
        self::assertTrue( Validate::intRangeOpen( '100', 0, 100 ) );
        self::assertTrue( Validate::intRangeOpen( '0', -100, 0 ) );
        self::assertTrue( Validate::intRangeOpen( '-100', -100, 0 ) );
        self::assertFalse( Validate::intRangeOpen( 'not a number', -100, 100 ) );
        self::assertFalse( Validate::intRangeOpen( '', -100, 100 ) );
        self::assertFalse( Validate::intRangeOpen( null, -100, 100 ) );
    }


    public function testIp() : void {
        self::assertTrue( Validate::ip( '127.0.0.1' ) );
        self::assertFalse( Validate::ip( '256.0.0.1' ) );
        self::assertFalse( Validate::ip( '192.168.1' ) );
        self::assertTrue( Validate::ip( '::1' ) );
        self::assertTrue( Validate::ip( '[::1]' ) );
        self::assertTrue( Validate::ip( '1234:5678:9abC:Def0:1234:5678:9abc:def0' ) );
        self::assertTrue( Validate::ip( '2001:db8::abc' ) );
        self::assertFalse( Validate::ip( '2001:db8::abg' ) );
        self::assertFalse( Validate::ip( 'not an IP' ) );
        self::assertFalse( Validate::ip( '' ) );
        self::assertFalse( Validate::ip( null ) );
    }


    public function testIpBlock() : void {

        self::assertTrue( Validate::ipBlock( '127.0.0.1', true ) );
        self::assertTrue( Validate::ipBlock( '::1', true ) );
        self::assertTrue( Validate::ipBlock( '10.0.0.0/8' ) );
        self::assertTrue( Validate::ipBlock( '[2601:db8::]/64' ) );

        self::assertFalse( Validate::ipBlock( '127.0.0.1' ) );
        self::assertFalse( Validate::ipBlock( '::1' ) );
        self::assertFalse( Validate::ipBlock( '10.0.256.0/24' ) );
        self::assertFalse( Validate::ipBlock( '1:2:3:4:5:6:7:8:9/64' ) );

        self::assertFalse( Validate::ipBlock( null ) );

    }


    public function testIpInCIDR() : void {
        // Dispatches to the IPv4 or IPv6 implementation based on the address family.
        self::assertTrue( Validate::ipInCIDR( '10.0.0.5', '10.0.0.0/8' ) );
        self::assertFalse( Validate::ipInCIDR( '11.0.0.5', '10.0.0.0/8' ) );
        self::assertTrue( Validate::ipInCIDR( '2001:db8::1', '2001:db8::/32' ) );
        self::assertFalse( Validate::ipInCIDR( '2001:dead::1', '2001:db8::/32' ) );

        // A bracketed IPv6 address still routes to the IPv6 implementation.
        self::assertTrue( Validate::ipInCIDR( '[2001:db8::1]', '2001:db8::/32' ) );

        // A single block may also be supplied as a one-element list.
        self::assertTrue( Validate::ipInCIDR( '10.0.0.5', [ '10.0.0.0/8' ] ) );

        // An IPv4 address is never inside an IPv6 block and vice versa.
        self::assertFalse( Validate::ipInCIDR( '10.0.0.5', '2001:db8::/32' ) );
        self::assertFalse( Validate::ipInCIDR( '2001:db8::1', '10.0.0.0/8' ) );

        // Invalid or missing inputs are rejected rather than throwing.
        self::assertFalse( Validate::ipInCIDR( null, '10.0.0.0/8' ) );
        self::assertFalse( Validate::ipInCIDR( 'not an IP', '10.0.0.0/8' ) );
        self::assertFalse( Validate::ipInCIDR( '10.0.0.5', 'garbage' ) );
        self::assertFalse( Validate::ipInCIDR( '10.0.0.5', '10.0.0.0/8/16' ) );
    }


    /**
     * The documented headline feature: a single list may mix IPv4 and IPv6
     * CIDR blocks, and an address matches if it falls in any one of them.
     */
    public function testIpInCIDRWithMixedList() : void {
        $r = [ '2001:db8::/32', '10.0.0.0/8' ];
        self::assertTrue( Validate::ipInCIDR( '10.0.0.5', $r ) );
        self::assertTrue( Validate::ipInCIDR( '2001:db8::1', $r ) );
        self::assertFalse( Validate::ipInCIDR( '11.0.0.5', $r ) );
        self::assertFalse( Validate::ipInCIDR( '2001:dead::1', $r ) );
    }


    public function testIpNotBogon() : void {
        // Globally routable addresses of either family are not bogons.
        self::assertTrue( Validate::ipNotBogon( '8.8.8.8' ) );
        self::assertTrue( Validate::ipNotBogon( '2606:4700::1111' ) );

        // Bogons of either family.
        self::assertFalse( Validate::ipNotBogon( '10.0.0.1' ) );
        self::assertFalse( Validate::ipNotBogon( '127.0.0.1' ) );
        self::assertFalse( Validate::ipNotBogon( 'fe80::1' ) );
        self::assertFalse( Validate::ipNotBogon( '::1' ) );

        // The localhost and private opt-ins pass through to the family handler.
        self::assertTrue( Validate::ipNotBogon( '127.0.0.1', i_bAllowLocalhost: true ) );
        self::assertTrue( Validate::ipNotBogon( '::1', i_bAllowLocalhost: true ) );
        self::assertTrue( Validate::ipNotBogon( '192.168.1.1', i_bAllowPrivate: true ) );
        self::assertTrue( Validate::ipNotBogon( 'fd00::1', i_bAllowPrivate: true ) );

        // Invalid or missing input is not "not a bogon".
        self::assertFalse( Validate::ipNotBogon( 'not an IP' ) );
        self::assertFalse( Validate::ipNotBogon( null ) );
    }


    public function testIpPrivate() : void {
        // Private addresses of either family.
        self::assertTrue( Validate::ipPrivate( '192.168.1.1' ) );
        self::assertTrue( Validate::ipPrivate( '10.0.0.1' ) );
        self::assertTrue( Validate::ipPrivate( 'fd00::1' ) );
        self::assertTrue( Validate::ipPrivate( 'fe80::1' ) );

        // Public addresses and loopback (without the opt-in) are not private.
        self::assertFalse( Validate::ipPrivate( '8.8.8.8' ) );
        self::assertFalse( Validate::ipPrivate( '2606:4700::1111' ) );
        self::assertFalse( Validate::ipPrivate( '127.0.0.1' ) );
        self::assertFalse( Validate::ipPrivate( '::1' ) );

        // Loopback counts as private only when explicitly included.
        self::assertTrue( Validate::ipPrivate( '127.0.0.1', i_bIncludeLocalhost: true ) );
        self::assertTrue( Validate::ipPrivate( '::1', i_bIncludeLocalhost: true ) );

        self::assertFalse( Validate::ipPrivate( 'not an IP' ) );
        self::assertFalse( Validate::ipPrivate( null ) );
    }


    /**
     * Guards the bogon/private range constants: every entry must be a valid CIDR
     * block of the expected family (and not the other). This catches typos like
     * a malformed address or a v4 range mistakenly placed in a v6 list.
     */
    public function testIpRangeConstantsAreValidBlocks() : void {
        $rV4 = [
            'IPV4_BOGON_RANGES' => Validate::IPV4_BOGON_RANGES,
            'IPV4_PRIVATE_RANGES' => Validate::IPV4_PRIVATE_RANGES,
        ];
        foreach ( $rV4 as $stName => $rRanges ) {
            foreach ( $rRanges as $stRange ) {
                self::assertTrue( Validate::ipv4Block( $stRange ), "{$stName}: not a valid IPv4 block: {$stRange}" );
                self::assertFalse( Validate::ipv6Block( $stRange ), "{$stName}: unexpectedly an IPv6 block: {$stRange}" );
            }
        }

        $rV6 = [
            'IPV6_BOGON_RANGES' => Validate::IPV6_BOGON_RANGES,
            'IPV6_PRIVATE_RANGES' => Validate::IPV6_PRIVATE_RANGES,
        ];
        foreach ( $rV6 as $stName => $rRanges ) {
            foreach ( $rRanges as $stRange ) {
                self::assertTrue( Validate::ipv6Block( $stRange ), "{$stName}: not a valid IPv6 block: {$stRange}" );
                self::assertFalse( Validate::ipv4Block( $stRange ), "{$stName}: unexpectedly an IPv4 block: {$stRange}" );
            }
        }
    }


    public function testIpv4() : void {
        self::assertTrue( Validate::ipv4( '127.0.0.1' ) );
        self::assertFalse( Validate::ipv4( '256.0.0.1' ) );
        self::assertFalse( Validate::ipv4( '192.168.1' ) );
        self::assertFalse( Validate::ipv4( '::1' ) );
        self::assertFalse( Validate::ipv4( '[::1]' ) );
        self::assertFalse( Validate::ipv4( '1234:5678:9abC:Def0:1234:5678:9abc:def0' ) );
        self::assertFalse( Validate::ipv4( '2001:db8::abc' ) );
        self::assertFalse( Validate::ipv4( '2001:db8::abg' ) );
        self::assertFalse( Validate::ipv4( 'not an IP' ) );
        self::assertFalse( Validate::ipv4( '' ) );
        self::assertFalse( Validate::ipv4( null ) );
    }


    public function testIpv4Block() : void {
        self::assertTrue( Validate::ipv4Block( '127.0.0.1', true ) );
        self::assertFalse( Validate::ipv4Block( '127.0.0.1' ) );
        self::assertTrue( Validate::ipv4Block( '127.0.0.1/0' ) );
        self::assertTrue( Validate::ipv4Block( '127.0.0.1/3' ) );
        self::assertTrue( Validate::ipv4Block( '127.0.0.1/9' ) );
        self::assertTrue( Validate::ipv4Block( '127.0.0.1/23' ) );
        self::assertTrue( Validate::ipv4Block( '127.0.0.1/32' ) );
        self::assertFalse( Validate::ipv4Block( '127.0.0.1/33' ) );
        self::assertFalse( Validate::ipv4Block( '10.0.0.0/8/16' ) );

        self::assertFalse( Validate::ipv4Block( '::1' ) );
        self::assertFalse( Validate::ipv4Block( '::1/64' ) );
        self::assertFalse( Validate::ipv4Block( 'Not even close.' ) );
        self::assertFalse( Validate::ipv4Block( null ) );
    }


    public function testIpv4InCIDR() : void {
        // Basic membership.
        self::assertTrue( Validate::ipv4InCIDR( '192.168.1.50', '192.168.1.0/24' ) );
        self::assertFalse( Validate::ipv4InCIDR( '192.168.2.50', '192.168.1.0/24' ) );

        // The whole space and a single host.
        self::assertTrue( Validate::ipv4InCIDR( '203.0.113.9', '0.0.0.0/0' ) );
        self::assertTrue( Validate::ipv4InCIDR( '192.168.1.5', '192.168.1.5/32' ) );
        self::assertFalse( Validate::ipv4InCIDR( '192.168.1.6', '192.168.1.5/32' ) );

        // A /31 (two-address) block.
        self::assertTrue( Validate::ipv4InCIDR( '192.168.1.1', '192.168.1.0/31' ) );
        self::assertFalse( Validate::ipv4InCIDR( '192.168.1.2', '192.168.1.0/31' ) );

        // Host bits set in the CIDR address are ignored (the block is masked too).
        self::assertTrue( Validate::ipv4InCIDR( '10.1.2.3', '10.255.255.255/8' ) );

        // A one-element list behaves like the bare block.
        self::assertTrue( Validate::ipv4InCIDR( '10.1.2.3', [ '10.0.0.0/8' ] ) );
        self::assertFalse( Validate::ipv4InCIDR( '11.1.2.3', [ '10.0.0.0/8' ] ) );

        // Non-IPv4 addresses are rejected.
        self::assertFalse( Validate::ipv4InCIDR( '2001:db8::1', '2001:db8::/32' ) );
        self::assertFalse( Validate::ipv4InCIDR( null, '10.0.0.0/8' ) );
        self::assertFalse( Validate::ipv4InCIDR( 'not an IP', '10.0.0.0/8' ) );

        // Invalid, IPv6, or bare-address blocks are rejected.
        self::assertFalse( Validate::ipv4InCIDR( '10.0.0.5', 'garbage' ) );
        self::assertFalse( Validate::ipv4InCIDR( '10.0.0.5', '2001:db8::/32' ) );
        self::assertFalse( Validate::ipv4InCIDR( '10.0.0.5', '10.0.0.0' ) );
        self::assertFalse( Validate::ipv4InCIDR( '10.0.0.5', '10.0.0.0/33' ) );
    }


    /**
     * A list of blocks matches if the address is in at least one of them. An
     * empty list matches nothing, and a null block (allowed by the type) is
     * simply not a match.
     */
    public function testIpv4InCIDRWithList() : void {
        // In the first block but not the second: should match (at least one).
        self::assertTrue( Validate::ipv4InCIDR( '10.0.0.5', [ '10.0.0.0/8', '192.168.0.0/16' ] ) );
        // In the second block but not the first: should match.
        self::assertTrue( Validate::ipv4InCIDR( '192.168.0.5', [ '10.0.0.0/8', '192.168.0.0/16' ] ) );
        // In neither: should not match.
        self::assertFalse( Validate::ipv4InCIDR( '172.16.0.5', [ '10.0.0.0/8', '192.168.0.0/16' ] ) );
        // An empty list matches nothing.
        self::assertFalse( Validate::ipv4InCIDR( '10.0.0.5', [] ) );
        // A null block (allowed by the parameter type) is simply not a match.
        self::assertFalse( Validate::ipv4InCIDR( '10.0.0.5', null ) );
    }


    public function testIpv4NotBogon() : void {
        // Globally routable addresses are not bogons.
        self::assertTrue( Validate::ipv4NotBogon( '8.8.8.8' ) );
        self::assertTrue( Validate::ipv4NotBogon( '1.1.1.1' ) );

        // A representative address from each bogon range is rejected.
        self::assertFalse( Validate::ipv4NotBogon( '0.0.0.0' ) );
        self::assertFalse( Validate::ipv4NotBogon( '10.1.2.3' ) );
        self::assertFalse( Validate::ipv4NotBogon( '100.64.0.1' ) );
        self::assertFalse( Validate::ipv4NotBogon( '127.0.0.1' ) );
        self::assertFalse( Validate::ipv4NotBogon( '169.254.1.1' ) );
        self::assertFalse( Validate::ipv4NotBogon( '172.16.5.5' ) );
        self::assertFalse( Validate::ipv4NotBogon( '192.168.1.1' ) );
        self::assertFalse( Validate::ipv4NotBogon( '198.51.100.7' ) );
        self::assertFalse( Validate::ipv4NotBogon( '203.0.113.7' ) );
        self::assertFalse( Validate::ipv4NotBogon( '224.0.0.1' ) );
        self::assertFalse( Validate::ipv4NotBogon( '255.255.255.255' ) );

        // Localhost: only the exact 127.0.0.1 is allowed by the opt-in.
        self::assertTrue( Validate::ipv4NotBogon( '127.0.0.1', i_bAllowLocalhost: true ) );
        self::assertFalse( Validate::ipv4NotBogon( '127.0.0.2', i_bAllowLocalhost: true ) );

        // Private addresses are allowed only with the opt-in...
        self::assertTrue( Validate::ipv4NotBogon( '10.1.2.3', i_bAllowPrivate: true ) );
        self::assertTrue( Validate::ipv4NotBogon( '192.168.1.1', i_bAllowPrivate: true ) );
        // ...and the opt-in does not rescue non-private bogons.
        self::assertFalse( Validate::ipv4NotBogon( '203.0.113.7', i_bAllowPrivate: true ) );

        // Non-IPv4 and invalid input.
        self::assertFalse( Validate::ipv4NotBogon( '2606:4700::1111' ) );
        self::assertFalse( Validate::ipv4NotBogon( 'not an IP' ) );
        self::assertFalse( Validate::ipv4NotBogon( null ) );
    }


    public function testIpv4Private() : void {
        // Each RFC 1918 range.
        self::assertTrue( Validate::ipv4Private( '10.1.2.3' ) );
        self::assertTrue( Validate::ipv4Private( '172.16.5.5' ) );
        self::assertTrue( Validate::ipv4Private( '192.168.1.1' ) );

        // Boundary addresses just outside 172.16.0.0/12.
        self::assertTrue( Validate::ipv4Private( '172.31.255.255' ) );
        self::assertFalse( Validate::ipv4Private( '172.32.0.0' ) );
        self::assertFalse( Validate::ipv4Private( '172.15.255.255' ) );

        // Public, loopback, and CGNAT are not private.
        self::assertFalse( Validate::ipv4Private( '8.8.8.8' ) );
        self::assertFalse( Validate::ipv4Private( '127.0.0.1' ) );
        self::assertFalse( Validate::ipv4Private( '100.64.0.1' ) );

        // Loopback only when explicitly included.
        self::assertTrue( Validate::ipv4Private( '127.0.0.1', i_bIncludeLocalhost: true ) );
        self::assertFalse( Validate::ipv4Private( '127.0.0.2', i_bIncludeLocalhost: true ) );

        self::assertFalse( Validate::ipv4Private( '2606:4700::1111' ) );
        self::assertFalse( Validate::ipv4Private( null ) );
    }


    public function testIpv6() : void {
        self::assertFalse( Validate::ipv6( '127.0.0.1' ) );
        self::assertFalse( Validate::ipv6( '256.0.0.1' ) );
        self::assertFalse( Validate::ipv6( '192.168.1' ) );
        self::assertTrue( Validate::ipv6( '::1' ) );
        self::assertTrue( Validate::ipv6( '[::1]' ) );
        self::assertTrue( Validate::ipv6( '1234:5678:9abC:Def0:1234:5678:9abc:def0' ) );
        self::assertTrue( Validate::ipv6( '2001:db8::abc' ) );
        self::assertFalse( Validate::ipv6( '2001:db8::abg' ) );
        self::assertFalse( Validate::ipv6( 'not an IP' ) );
        self::assertFalse( Validate::ipv6( '' ) );
        self::assertFalse( Validate::ipv6( null ) );
    }


    public function testIpv6Block() : void {
        self::assertTrue( Validate::ipv6Block( '::1', true ) );
        self::assertFalse( Validate::ipv6Block( '::1' ) );
        self::assertTrue( Validate::ipv6Block( '2001:db8::/64', true ) );
        self::assertTrue( Validate::ipv6Block( '2001:db8::/64' ) );
        self::assertTrue( Validate::ipv6Block( '[2001:db8::]/64' ) );
        self::assertFalse( Validate::ipv6Block( '[2001:db8::/64]' ) );
        self::assertFalse( Validate::ipv6Block( '[2001:]db8::/64' ) );
        self::assertFalse( Validate::ipv6Block( '2001:db8::/64/48' ) );
        self::assertTrue( Validate::ipv6Block( '2001:db8::/0' ) );
        self::assertTrue( Validate::ipv6Block( '2001:db8::/13' ) );
        self::assertTrue( Validate::ipv6Block( '2001:db8::/53' ) );
        self::assertTrue( Validate::ipv6Block( '2001:db8::/71' ) );
        self::assertTrue( Validate::ipv6Block( '2001:db8::/113' ) );
        self::assertTrue( Validate::ipv6Block( '2001:db8::/128' ) );
        self::assertFalse( Validate::ipv6Block( '2001:db8::/129' ) );

        self::assertFalse( Validate::ipv6Block( '10.0.0.0' ) );
        self::assertFalse( Validate::ipv6Block( '10.0.0.0/8' ) );
        self::assertFalse( Validate::ipv6Block( 'Not even close.' ) );
        self::assertFalse( Validate::ipv6Block( null ) );

    }


    public function testIpv6InCIDR() : void {
        // Basic membership.
        self::assertTrue( Validate::ipv6InCIDR( '2001:db8::1', '2001:db8::/32' ) );
        self::assertFalse( Validate::ipv6InCIDR( '2001:dead::1', '2001:db8::/32' ) );

        // The whole space and a single host.
        self::assertTrue( Validate::ipv6InCIDR( '::1', '::/0' ) );
        self::assertTrue( Validate::ipv6InCIDR( '2001:db8::1', '2001:db8::1/128' ) );
        self::assertFalse( Validate::ipv6InCIDR( '2001:db8::2', '2001:db8::1/128' ) );

        // A non-byte-aligned prefix length.
        self::assertTrue( Validate::ipv6InCIDR( '2001:db8:a000::1', '2001:db8:abcd::/36' ) );
        self::assertFalse( Validate::ipv6InCIDR( '2001:db8:b000::1', '2001:db8:abcd::/36' ) );

        // Host bits set in the CIDR address are ignored (the block is masked too).
        self::assertTrue( Validate::ipv6InCIDR( '2001:db8:abcd::1', '2001:db8:ffff::ffff/32' ) );

        // A one-element list behaves like the bare block.
        self::assertTrue( Validate::ipv6InCIDR( '2001:db8::1', [ '2001:db8::/32' ] ) );
        self::assertFalse( Validate::ipv6InCIDR( '2001:dead::1', [ '2001:db8::/32' ] ) );

        // Non-IPv6 addresses are rejected.
        self::assertFalse( Validate::ipv6InCIDR( '10.0.0.5', '10.0.0.0/8' ) );
        self::assertFalse( Validate::ipv6InCIDR( null, '2001:db8::/32' ) );
        self::assertFalse( Validate::ipv6InCIDR( 'not an IP', '2001:db8::/32' ) );

        // Invalid, IPv4, or bare-address blocks are rejected.
        self::assertFalse( Validate::ipv6InCIDR( '2001:db8::1', 'garbage' ) );
        self::assertFalse( Validate::ipv6InCIDR( '2001:db8::1', '10.0.0.0/8' ) );
        self::assertFalse( Validate::ipv6InCIDR( '2001:db8::1', '2001:db8::' ) );
        self::assertFalse( Validate::ipv6InCIDR( '2001:db8::1', '2001:db8::/129' ) );
    }


    /**
     * As with IPv4: matches if the address is in at least one listed block,
     * while an empty list and a null block are not matches.
     */
    public function testIpv6InCIDRWithList() : void {
        self::assertTrue( Validate::ipv6InCIDR( '2001:db8::1', [ '2001:db8::/32', 'fd00::/8' ] ) );
        self::assertTrue( Validate::ipv6InCIDR( 'fd12::1', [ '2001:db8::/32', 'fd00::/8' ] ) );
        self::assertFalse( Validate::ipv6InCIDR( '2001:dead::1', [ '2001:db8::/32', 'fd00::/8' ] ) );
        self::assertFalse( Validate::ipv6InCIDR( '2001:db8::1', [] ) );
        self::assertFalse( Validate::ipv6InCIDR( '2001:db8::1', null ) );
    }


    /**
     * Bracketed IPv6 addresses (e.g. "[2001:db8::1]") are accepted by
     * Validate::ipv6() and Validate::ipv6Block(), so for consistency they are
     * accepted here too -- on both the address and the CIDR block address.
     */
    public function testIpv6InCIDRWithBracketedInput() : void {
        self::assertTrue( Validate::ipv6InCIDR( '[2001:db8::1]', '2001:db8::/32' ) );
        self::assertTrue( Validate::ipv6InCIDR( '2001:db8::1', '[2001:db8::]/32' ) );
        self::assertFalse( Validate::ipv6InCIDR( '[2001:dead::1]', '2001:db8::/32' ) );
    }


    public function testIpv6NotBogon() : void {
        // Globally routable addresses are not bogons.
        self::assertTrue( Validate::ipv6NotBogon( '2606:4700::1111' ) );
        self::assertTrue( Validate::ipv6NotBogon( '2620:fe::fe' ) );

        // A representative address from each bogon range is rejected.
        self::assertFalse( Validate::ipv6NotBogon( '::' ) );
        self::assertFalse( Validate::ipv6NotBogon( '::1' ) );
        self::assertFalse( Validate::ipv6NotBogon( '::ffff:192.168.1.1' ) );
        self::assertFalse( Validate::ipv6NotBogon( '2001:2::1' ) );
        self::assertFalse( Validate::ipv6NotBogon( '2002::1' ) );
        self::assertFalse( Validate::ipv6NotBogon( 'fc00::1' ) );
        self::assertFalse( Validate::ipv6NotBogon( 'fe80::1' ) );

        // Localhost (::1) only with the opt-in. The check is CIDR-based, so a
        // non-normalized but equivalent form of ::1 is still recognized.
        self::assertTrue( Validate::ipv6NotBogon( '::1', i_bAllowLocalhost: true ) );
        self::assertTrue( Validate::ipv6NotBogon( '0:0:0:0:0:0:0:1', i_bAllowLocalhost: true ) );

        // Private (ULA / link-local) addresses only with the opt-in...
        self::assertTrue( Validate::ipv6NotBogon( 'fc00::1', i_bAllowPrivate: true ) );
        self::assertTrue( Validate::ipv6NotBogon( 'fe80::1', i_bAllowPrivate: true ) );
        // ...and the opt-in does not rescue non-private bogons.
        self::assertFalse( Validate::ipv6NotBogon( '2002::1', i_bAllowPrivate: true ) );

        // Non-IPv6 and invalid input.
        self::assertFalse( Validate::ipv6NotBogon( '8.8.8.8' ) );
        self::assertFalse( Validate::ipv6NotBogon( 'not an IP' ) );
        self::assertFalse( Validate::ipv6NotBogon( null ) );
    }


    /**
     * The RFC 3849 documentation prefix 2001:db8::/32 -- used as the example
     * address throughout these tests -- is classified as a bogon, both directly
     * and through the family-dispatching ipNotBogon().
     */
    public function testIpv6NotBogonDocumentationRange() : void {
        self::assertFalse( Validate::ipv6NotBogon( '2001:db8::1' ) );
        self::assertFalse( Validate::ipv6NotBogon( '2001:db8:abcd::1' ) );
        self::assertFalse( Validate::ipNotBogon( '2001:db8::1' ) );
    }


    public function testIpv6Private() : void {
        // Unique local addresses (fc00::/7) and link-local (fe80::/10).
        self::assertTrue( Validate::ipv6Private( 'fc00::1' ) );
        self::assertTrue( Validate::ipv6Private( 'fd12:3456::1' ) );
        self::assertTrue( Validate::ipv6Private( 'fe80::1' ) );

        // Public and loopback (without the opt-in) are not private.
        self::assertFalse( Validate::ipv6Private( '2606:4700::1111' ) );
        self::assertFalse( Validate::ipv6Private( '::1' ) );

        // Loopback only when explicitly included, in any equivalent form.
        self::assertTrue( Validate::ipv6Private( '::1', i_bIncludeLocalhost: true ) );
        self::assertTrue( Validate::ipv6Private( '0:0:0:0:0:0:0:1', i_bIncludeLocalhost: true ) );

        // Non-IPv6 and invalid input.
        self::assertFalse( Validate::ipv6Private( '192.168.1.1' ) );
        self::assertFalse( Validate::ipv6Private( 'not an IP' ) );
        self::assertFalse( Validate::ipv6Private( null ) );
    }


    public function testNonexistentFilename() : void {
        self::assertTrue( Validate::nonexistentFilename( __DIR__ . '/nonexistent.txt' ) );
        self::assertFalse( Validate::nonexistentFilename( '/path/to/nonexistent/file.txt' ) );
        self::assertFalse( Validate::nonexistentFilename( __FILE__ ) );
        self::assertFalse( Validate::nonexistentFilename( '' ) );
        self::assertFalse( Validate::nonexistentFilename( null ) );
    }


    public function testPositiveFloat() : void {
        self::assertTrue( Validate::positiveFloat( '123.45' ) );
        self::assertFalse( Validate::positiveFloat( '-123.45' ) );
        self::assertFalse( Validate::positiveFloat( '0' ) );
        self::assertFalse( Validate::positiveFloat( '' ) );
        self::assertFalse( Validate::positiveFloat( 'nope' ) );
        self::assertFalse( Validate::positiveFloat( null ) );
    }


    public function testPositiveInt() : void {
        self::assertTrue( Validate::positiveInt( '123' ) );
        self::assertFalse( Validate::positiveInt( '-123' ) );
        self::assertFalse( Validate::positiveInt( '0' ) );
        self::assertFalse( Validate::positiveInt( '' ) );
        self::assertFalse( Validate::positiveInt( 'nope' ) );
        self::assertFalse( Validate::positiveInt( null ) );
    }


    public function testUnsignedFloat() : void {
        self::assertTrue( Validate::unsignedFloat( '123.45' ) );
        self::assertFalse( Validate::unsignedFloat( '-123.45' ) );
        self::assertTrue( Validate::unsignedFloat( '0' ) );
        self::assertFalse( Validate::unsignedFloat( '' ) );
        self::assertFalse( Validate::unsignedFloat( 'nope' ) );
        self::assertFalse( Validate::unsignedFloat( null ) );
    }


    public function testUnsignedInt() : void {
        self::assertTrue( Validate::unsignedInt( '123' ) );
        self::assertFalse( Validate::unsignedInt( '-123' ) );
        self::assertTrue( Validate::unsignedInt( '0' ) );
        self::assertFalse( Validate::unsignedInt( '' ) );
        self::assertFalse( Validate::unsignedInt( 'nope' ) );
        self::assertFalse( Validate::unsignedInt( null ) );
    }


}
