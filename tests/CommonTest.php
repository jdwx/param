<?php


declare( strict_types = 1 );


use JDWX\Param\Common;
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


}
