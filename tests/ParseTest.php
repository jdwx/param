<?php


declare( strict_types = 1 );


use JDWX\Param\Parse;
use JDWX\Param\ParseException;
use JDWX\Param\Validate;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\TestCase;


#[CoversClass( Parse::class )]
final class ParseTest extends TestCase {


    public function testArrayMap() : void {
        $r = [ 'foo' => 'bar', 'baz' => 'qux' ];
        self::assertSame( 'bar', Parse::arrayMap( 'foo', $r ) );
        self::assertSame( 'qux', Parse::arrayMap( 'baz', $r ) );
    }


    public function testArrayMapForInvalid() : void {
        self::expectException( ParseException::class );
        Parse::arrayMap( 'missing', [ 'foo' => 'bar' ] );
    }


    public function testArrayValue() : void {
        $r = [ 'foo', 'bar', 'baz' ];
        self::assertSame( 'foo', Parse::arrayValue( 'foo', $r ) );
        self::assertSame( 'bar', Parse::arrayValue( 'bar', $r ) );
    }


    public function testArrayValueForInvalid() : void {
        self::expectException( ParseException::class );
        Parse::arrayValue( 'missing', [ 'foo', 'bar' ] );
    }


    public function testBool() : void {
        self::assertTrue( Parse::bool( 'true' ) );
        self::assertTrue( Parse::bool( 'yes' ) );
        self::assertTrue( Parse::bool( 'yeah' ) );
        self::assertTrue( Parse::bool( 'y' ) );
        self::assertTrue( Parse::bool( 'on' ) );
        self::assertTrue( Parse::bool( 'TRUE' ) );
        self::assertTrue( Parse::bool( '1' ) );
        self::assertTrue( Parse::bool( '42' ) );

        self::assertFalse( Parse::bool( 'false' ) );
        self::assertFalse( Parse::bool( 'no' ) );
        self::assertFalse( Parse::bool( 'nope' ) );
        self::assertFalse( Parse::bool( 'n' ) );
        self::assertFalse( Parse::bool( 'off' ) );
        self::assertFalse( Parse::bool( '0' ) );
    }


    public function testBoolAgreesWithValidateOnWhitespace() : void {
        self::assertTrue( Validate::bool( ' true ' ) );
        self::assertTrue( Parse::bool( ' true ' ) );
    }


    public function testBoolForInvalid() : void {
        self::expectException( ParseException::class );
        Parse::bool( 'foo' );
    }


    public function testConstant() : void {
        self::assertSame( 'foo', Parse::constant( 'foo', 'foo' ) );
        self::expectException( ParseException::class );
        Parse::constant( 'foo', 'bar' );
    }


    public function testCurrency() : void {
        self::assertSame( 123456, Parse::currency( '1234.56' ) );
        self::assertSame( -123456, Parse::currency( '-1234.56' ) );
        self::assertSame( 123456, Parse::currency( '1,234.56' ) );
        self::assertSame( -123456, Parse::currency( '-1,234.56' ) );
        self::assertSame( 123456, Parse::currency( '$1234.56' ) );
        self::assertSame( -123456, Parse::currency( '-$1234.56' ) );
        self::assertSame( -123456, Parse::currency( '$-1234.56' ) );
        self::assertSame( 123456, Parse::currency( '$1,234.56' ) );
        self::assertSame( -123456, Parse::currency( '-$1,234.56' ) );
        self::assertSame( -123456, Parse::currency( '(1234.56)' ) );
        self::assertSame( -123456, Parse::currency( '(1,234.56)' ) );
        self::assertSame( -123456, Parse::currency( '($1234.56)' ) );
        self::assertSame( -123456, Parse::currency( '($1,234.56)' ) );
        self::expectException( ParseException::class );
        Parse::currency( '(-$1,234.56)' );
    }


    public function testDate() : void {
        self::assertSame( '2024-01-25', Parse::date( '2024-01-25 12:34:56' ) );
        self::assertSame( '2025-03-02', Parse::date( 'March 2, 2025 3:53 PM' ) );
        self::assertSame( '2024-01-28', Parse::date( '2024-01-26 + 2 days' ) );
        self::expectException( ParseException::class );
        Parse::date( 'foo' );
    }


    public function testDateTime() : void {
        self::assertSame( '2024-01-25 12:34:56', Parse::dateTime( '2024-01-25 12:34:56' ) );
        self::assertSame( '2025-03-02 15:53:00', Parse::dateTime( 'March 2, 2025 3:53 PM' ) );
        self::assertSame( '2024-01-28 00:00:00', Parse::dateTime( '2024-01-26 + 2 days' ) );
        self::expectException( ParseException::class );
        Parse::dateTime( 'foo' );
    }


    public function testEmailAddress() : void {
        self::assertSame( 'test@example.com', Parse::emailAddress( 'test@example.com' ) );
    }


    public function testEmailAddressForInvalid() : void {
        self::expectException( ParseException::class );
        Parse::emailAddress( 'not-an-email' );
    }


    public function testEmailUsername() : void {
        self::assertSame( 'test', Parse::emailUsername( 'test' ) );
        self::assertSame( 'test+folder', Parse::emailUsername( 'test+folder' ) );
    }


    public function testEmailUsernameForInvalid() : void {
        self::expectException( ParseException::class );
        Parse::emailUsername( 'test@domain' );
    }


    public function testExistingDirectory() : void {
        self::assertSame( __DIR__, Parse::existingDirectory( __DIR__ ) );
    }


    public function testExistingDirectoryForInvalid() : void {
        self::expectException( ParseException::class );
        Parse::existingDirectory( '/no/such/dir' );
    }


    public function testExistingFilename() : void {
        self::assertSame( __FILE__, Parse::existingFilename( __FILE__ ) );
    }


    public function testExistingFilenameForInvalid() : void {
        self::expectException( ParseException::class );
        Parse::existingFilename( __DIR__ . '/no-such-file.txt' );
    }


    public function testExistingPath() : void {
        self::assertSame( __FILE__, Parse::existingPath( __FILE__ ) );
        self::assertSame( __DIR__, Parse::existingPath( __DIR__ ) );
    }


    public function testExistingPathForInvalid() : void {
        self::expectException( ParseException::class );
        Parse::existingPath( '/no/such/path' );
    }


    public function testFQDN() : void {
        self::assertSame( 'localhost', Parse::fqdn( 'localhost' ) );
        self::assertSame(
            '_acme-challenge._well-known.example.org',
            Parse::fqdn( '_acme-challenge._well-known.example.org' )
        );
        self::assertSame( 'example.com', Parse::fqdn( '  EXAMPLE.COM  ' ) );
    }


    public function testFQDNAllowSpaces() : void {
        self::assertSame(
            'foo bar.example.com',
            Parse::fqdn( 'foo bar.example.com', i_bAllowSpaces: true )
        );
    }


    public function testFQDNForInvalid() : void {
        self::expectException( ParseException::class );
        Parse::fqdn( 'not an fqdn' );
    }


    public function testFQDNForSpacesRejectedByDefault() : void {
        self::expectException( ParseException::class );
        Parse::fqdn( 'foo bar.example.com' );
    }


    public function testFloat() : void {
        self::assertSame( 123.45, Parse::float( '123.45' ) );
        self::assertSame( -123.45, Parse::float( '-123.45' ) );
        self::assertSame( 0.0, Parse::float( '0' ) );
    }


    public function testFloatForInvalid() : void {
        self::expectException( ParseException::class );
        Parse::float( 'not a float' );
    }


    public function testFloatRangeClosed() : void {
        self::assertSame( 12.345, Parse::floatRangeClosed( '12.345', 0, 100 ) );
    }


    public function testFloatRangeClosedForInvalid() : void {
        self::expectException( ParseException::class );
        Parse::floatRangeClosed( 'not a float', 0, 100 );
    }


    public function testFloatRangeClosedForOutOfRange() : void {
        self::expectException( ParseException::class );
        Parse::floatRangeClosed( '0', 0, 100 );
    }


    public function testFloatRangeHalfClosed() : void {
        self::assertSame( 12.345, Parse::floatRangeHalfClosed( '12.345', 0, 100 ) );
        self::assertSame( 0.0, Parse::floatRangeHalfClosed( '0', 0, 100 ) );
    }


    public function testFloatRangeHalfClosedForInvalid() : void {
        self::expectException( ParseException::class );
        Parse::floatRangeHalfClosed( 'not a float', 0, 100 );
    }


    public function testFloatRangeHalfClosedForOutOfRange() : void {
        self::expectException( ParseException::class );
        Parse::floatRangeHalfClosed( '100', 0, 100 );
    }


    public function testFloatRangeOpen() : void {
        self::assertSame( 12.345, Parse::floatRangeOpen( '12.345', 0, 100 ) );
        self::assertSame( 0.0, Parse::floatRangeOpen( '0', 0, 100 ) );
        self::assertSame( 100.0, Parse::floatRangeOpen( '100', 0, 100 ) );
    }


    public function testFloatRangeOpenForInvalid() : void {
        self::expectException( ParseException::class );
        Parse::floatRangeOpen( 'not a float', 0, 100 );
    }


    public function testFloatRangeOpenForOutOfRange() : void {
        self::expectException( ParseException::class );
        Parse::floatRangeOpen( '101', 0, 100 );
    }


    public function testGlob() : void {
        $r = Parse::glob( __DIR__ . '/*.php' );
        self::assertContains( __FILE__, $r );

        self::expectException( ParseException::class );
        Parse::glob( __DIR__ . '/*.foo', GLOB_ERR );
    }


    public function testGlobAllowsEmpty() : void {
        self::assertSame( [], Parse::glob( __DIR__ . '/*.no-such-extension', 0, true ) );
    }


    public function testHostname() : void {
        self::assertSame( 'example.com', Parse::hostname( 'example.com' ) );
        self::assertSame( 'www.example.com', Parse::hostname( 'www.example.com' ) );
    }


    public function testHostnameForInvalid() : void {
        self::expectException( ParseException::class );
        Parse::hostname( 'foo!bar.com' );
    }


    public function testInt() : void {
        self::assertSame( 123, Parse::int( '123' ) );
        self::assertSame( -123, Parse::int( '-123' ) );
        self::assertSame( 0, Parse::int( '0' ) );
    }


    public function testIntForInvalid() : void {
        self::expectException( ParseException::class );
        Parse::int( 'not a number' );
    }


    public function testIntRangeClosed() : void {
        self::assertSame( 12, Parse::intRangeClosed( '12', 0, 100 ) );
    }


    public function testIntRangeClosedForInvalid() : void {
        self::expectException( ParseException::class );
        Parse::intRangeClosed( 'not a number', 0, 100 );
    }


    public function testIntRangeClosedForOutOfRange() : void {
        self::expectException( ParseException::class );
        Parse::intRangeClosed( '0', 0, 100 );
    }


    public function testIntRangeHalfClosed() : void {
        self::assertSame( 12, Parse::intRangeHalfClosed( '12', 0, 100 ) );
        self::assertSame( 0, Parse::intRangeHalfClosed( '0', 0, 100 ) );
    }


    public function testIntRangeHalfClosedForInvalid() : void {
        self::expectException( ParseException::class );
        Parse::intRangeHalfClosed( 'not a number', 0, 100 );
    }


    public function testIntRangeHalfClosedForOutOfRange() : void {
        self::expectException( ParseException::class );
        Parse::intRangeHalfClosed( '100', 0, 100 );
    }


    public function testIntRangeOpen() : void {
        self::assertSame( 12, Parse::intRangeOpen( '12', 0, 100 ) );
        self::assertSame( 0, Parse::intRangeOpen( '0', 0, 100 ) );
        self::assertSame( 100, Parse::intRangeOpen( '100', 0, 100 ) );
    }


    public function testIntRangeOpenForInvalid() : void {
        self::expectException( ParseException::class );
        Parse::intRangeOpen( 'not a number', 0, 100 );
    }


    public function testIntRangeOpenForOutOfRange() : void {
        self::expectException( ParseException::class );
        Parse::intRangeOpen( '101', 0, 100 );
    }


    public function testIp() : void {
        self::assertSame( '127.0.0.1', Parse::ip( '127.0.0.1' ) );
        self::assertSame( '::1', Parse::ip( '::1' ) );
        self::assertSame( '::1', Parse::ip( '[0:0:0:0:0:0:0:1]' ) );
        self::assertSame( '[0:0:0:0:0:0:0:1]', Parse::ip( '[0:0:0:0:0:0:0:1]', i_bNormalize: false ) );
    }


    public function testIpBlock() : void {
        self::assertSame( '10.0.0.0/8', Parse::ipBlock( '10.0.0.0/8' ) );
        self::assertSame( '10.0.0.0/32', Parse::ipBlock( '10.0.0.0', true ) );
        self::assertSame( '2601:db8::/64', Parse::ipBlock( '[2601:db8::]/64' ) );
        self::assertSame( '2601:db8::/128', Parse::ipBlock( '2601:db8::', true ) );
    }


    public function testIpBlockForBadIPv4() : void {
        self::expectException( ParseException::class );
        Parse::ipBlock( '10.0.0.0/33' );
    }


    public function testIpBlockForBadIPv6() : void {
        self::expectException( ParseException::class );
        Parse::ipBlock( '[2601:db8]::/64' );
    }


    public function testIpForInvalid() : void {
        self::expectException( ParseException::class );
        Parse::ip( 'foo' );
    }


    public function testIpv4() : void {
        self::assertSame( '127.0.0.1', Parse::ipv4( '127.0.0.1' ) );
        self::assertSame( '10.20.40.80', Parse::ipv4( '10.20.40.80' ) );
    }


    public function testIpv4Block() : void {
        self::assertSame( '127.0.0.1/32', Parse::ipv4Block( '127.0.0.1', true ) );
        self::assertSame( '10.0.0.0/8', Parse::ipv4Block( '10.0.0.0/8', true ) );
        self::assertSame( '10.0.0.0/8', Parse::ipv4Block( '10.0.0.0/8' ) );
        self::assertSame( '10.20.30.40/16', Parse::ipv4Block( '10.20.30.40/16' ) );
        self::assertSame( '10.20.0.0/16', Parse::ipv4Block( '10.20.30.40/16', i_bMask: true ) );

        self::assertSame( '0.0.0.0/0', Parse::ipv4Block( '255.255.255.255/0', i_bMask: true ) );
        self::assertSame( '128.0.0.0/1', Parse::ipv4Block( '255.255.255.255/1', i_bMask: true ) );
        self::assertSame( '192.0.0.0/2', Parse::ipv4Block( '255.255.255.255/2', i_bMask: true ) );
        self::assertSame( '224.0.0.0/3', Parse::ipv4Block( '255.255.255.255/3', i_bMask: true ) );
        self::assertSame( '240.0.0.0/4', Parse::ipv4Block( '255.255.255.255/4', i_bMask: true ) );
        self::assertSame( '248.0.0.0/5', Parse::ipv4Block( '255.255.255.255/5', i_bMask: true ) );
        self::assertSame( '252.0.0.0/6', Parse::ipv4Block( '255.255.255.255/6', i_bMask: true ) );
        self::assertSame( '254.0.0.0/7', Parse::ipv4Block( '255.255.255.255/7', i_bMask: true ) );
        self::assertSame( '255.0.0.0/8', Parse::ipv4Block( '255.255.255.255/8', i_bMask: true ) );

        self::assertSame( '255.128.0.0/9', Parse::ipv4Block( '255.255.255.255/9', i_bMask: true ) );
        self::assertSame( '255.192.0.0/10', Parse::ipv4Block( '255.255.255.255/10', i_bMask: true ) );
        self::assertSame( '255.224.0.0/11', Parse::ipv4Block( '255.255.255.255/11', i_bMask: true ) );
        self::assertSame( '255.240.0.0/12', Parse::ipv4Block( '255.255.255.255/12', i_bMask: true ) );
        self::assertSame( '255.248.0.0/13', Parse::ipv4Block( '255.255.255.255/13', i_bMask: true ) );
        self::assertSame( '255.252.0.0/14', Parse::ipv4Block( '255.255.255.255/14', i_bMask: true ) );
        self::assertSame( '255.254.0.0/15', Parse::ipv4Block( '255.255.255.255/15', i_bMask: true ) );
        self::assertSame( '255.255.0.0/16', Parse::ipv4Block( '255.255.255.255/16', i_bMask: true ) );
        self::assertSame( '255.255.128.0/17', Parse::ipv4Block( '255.255.255.255/17', i_bMask: true ) );
        self::assertSame( '255.255.192.0/18', Parse::ipv4Block( '255.255.255.255/18', i_bMask: true ) );
        self::assertSame( '255.255.224.0/19', Parse::ipv4Block( '255.255.255.255/19', i_bMask: true ) );
        self::assertSame( '255.255.240.0/20', Parse::ipv4Block( '255.255.255.255/20', i_bMask: true ) );
        self::assertSame( '255.255.248.0/21', Parse::ipv4Block( '255.255.255.255/21', i_bMask: true ) );
        self::assertSame( '255.255.252.0/22', Parse::ipv4Block( '255.255.255.255/22', i_bMask: true ) );
        self::assertSame( '255.255.254.0/23', Parse::ipv4Block( '255.255.255.255/23', i_bMask: true ) );
        self::assertSame( '255.255.255.0/24', Parse::ipv4Block( '255.255.255.255/24', i_bMask: true ) );
        self::assertSame( '255.255.255.128/25', Parse::ipv4Block( '255.255.255.255/25', i_bMask: true ) );
        self::assertSame( '255.255.255.192/26', Parse::ipv4Block( '255.255.255.255/26', i_bMask: true ) );
        self::assertSame( '255.255.255.224/27', Parse::ipv4Block( '255.255.255.255/27', i_bMask: true ) );
        self::assertSame( '255.255.255.240/28', Parse::ipv4Block( '255.255.255.255/28', i_bMask: true ) );
        self::assertSame( '255.255.255.248/29', Parse::ipv4Block( '255.255.255.255/29', i_bMask: true ) );
        self::assertSame( '255.255.255.252/30', Parse::ipv4Block( '255.255.255.255/30', i_bMask: true ) );
        self::assertSame( '255.255.255.254/31', Parse::ipv4Block( '255.255.255.255/31', i_bMask: true ) );
        self::assertSame( '255.255.255.255/32', Parse::ipv4Block( '255.255.255.255/32', i_bMask: true ) );
    }


    public function testIpv4BlockForInvalid() : void {
        self::expectException( ParseException::class );
        Parse::ipv4Block( 'foo' );
    }


    public function testIpv4ForInvalid() : void {
        self::expectException( ParseException::class );
        Parse::ipv4( 'foo' );
    }


    public function testIpv4ForIpv6() : void {
        self::expectException( ParseException::class );
        Parse::ipv4( '2001:db8::1234' );
    }


    public function testIpv6() : void {
        self::assertSame( '2001:db8::1234', Parse::ipv6( '2001:db8::1234' ) );
        self::assertSame( '2001:db8::1234', Parse::ipv6( '[2001:db8:0000:0::1234]' ) );
        self::assertSame( '[2001:db8:0000:0::1234]', Parse::ipv6( '[2001:db8:0000:0::1234]', i_bNormalize: false ) );
    }


    public function testIpv6Block() : void {
        self::assertSame( '2001:db8::/128', Parse::ipv6Block( '2001:db8::', true ) );
        self::assertSame( '2001:db8::/128', Parse::ipv6Block( '2001:db8::/128', true ) );
        self::assertSame( '2001:db8::/128', Parse::ipv6Block( '2001:db8::/128' ) );
        self::assertSame( '2001:db8::1234/64', Parse::ipv6Block( '2001:db8::1234/64' ) );
        self::assertSame( '2001:db8::/64', Parse::ipv6Block( '2001:db8::1234/64', i_bMask: true ) );

        $all = 'ffff:ffff:ffff:ffff:ffff:ffff:ffff:ffff';
        self::assertSame( '::/0', Parse::ipv6Block( "{$all}/0", i_bMask: true ) );
        self::assertSame( '8000::/1', Parse::ipv6Block( "{$all}/1", i_bMask: true ) );
        self::assertSame( 'e000::/3', Parse::ipv6Block( "{$all}/3", i_bMask: true ) );
        self::assertSame( 'fe00::/7', Parse::ipv6Block( "{$all}/7", i_bMask: true ) );
        self::assertSame( 'ff00::/8', Parse::ipv6Block( "{$all}/8", i_bMask: true ) );
        self::assertSame( 'ff80::/9', Parse::ipv6Block( "{$all}/9", i_bMask: true ) );
        self::assertSame( 'ffff::/16', Parse::ipv6Block( "{$all}/16", i_bMask: true ) );
        self::assertSame( 'ffff:8000::/17', Parse::ipv6Block( "{$all}/17", i_bMask: true ) );
        self::assertSame( 'ffff:ffff::/32', Parse::ipv6Block( "{$all}/32", i_bMask: true ) );
        self::assertSame( 'ffff:ffff:ffff::/48', Parse::ipv6Block( "{$all}/48", i_bMask: true ) );
        self::assertSame( 'ffff:ffff:ffff:ffff::/64', Parse::ipv6Block( "{$all}/64", i_bMask: true ) );
        self::assertSame(
            'ffff:ffff:ffff:ffff:8000::/65',
            Parse::ipv6Block( "{$all}/65", i_bMask: true )
        );
        self::assertSame(
            'ffff:ffff:ffff:ffff:ffff:ffff::/96',
            Parse::ipv6Block( "{$all}/96", i_bMask: true )
        );
        self::assertSame(
            'ffff:ffff:ffff:ffff:ffff:ffff:ffff:0/112',
            Parse::ipv6Block( "{$all}/112", i_bMask: true )
        );
        self::assertSame(
            'ffff:ffff:ffff:ffff:ffff:ffff:ffff:ff00/120',
            Parse::ipv6Block( "{$all}/120", i_bMask: true )
        );
        self::assertSame(
            'ffff:ffff:ffff:ffff:ffff:ffff:ffff:fffe/127',
            Parse::ipv6Block( "{$all}/127", i_bMask: true )
        );
        self::assertSame( "{$all}/128", Parse::ipv6Block( "{$all}/128", i_bMask: true ) );
    }


    public function testIpv6ForInvalid() : void {
        self::expectException( ParseException::class );
        Parse::ipv6( 'foo' );
    }


    public function testIpv6ForIpv4() : void {
        self::expectException( ParseException::class );
        Parse::ipv6( '192.168.1.1' );
    }


    public function testNonexistentFilename() : void {
        self::assertSame(
            __DIR__ . '/no-such-file.txt',
            Parse::nonexistentFilename( __DIR__ . '/no-such-file.txt' )
        );
    }


    public function testNonexistentFilenameForExisting() : void {
        $this->expectException( ParseException::class );
        $this->expectExceptionMessage( 'Invalid filename' );
        Parse::nonexistentFilename( __FILE__ );
    }


    public function testNonexistentFilenameForMissingDirectory() : void {
        $this->expectException( ParseException::class );
        $this->expectExceptionMessage( 'Directory does not exist' );
        Parse::nonexistentFilename( '/no/such/dir/file.txt' );
    }


    public function testPositiveFloat() : void {
        self::assertSame( 123.45, Parse::positiveFloat( '123.45' ) );
    }


    public function testPositiveFloatForInvalid() : void {
        self::expectException( ParseException::class );
        Parse::positiveFloat( '0' );
    }


    public function testPositiveInt() : void {
        self::assertSame( 123, Parse::positiveInt( '123' ) );
    }


    public function testPositiveIntForInvalid() : void {
        self::expectException( ParseException::class );
        Parse::positiveInt( '0' );
    }


    public function testRoundedFloat() : void {
        self::assertSame( 123.0, Parse::roundedFloat( '123.45' ) );
        self::assertSame( 123.5, Parse::roundedFloat( '123.45', 1 ) );
        self::assertSame( 123.45, Parse::roundedFloat( '123.45', 2 ) );
        self::assertSame( -123.5, Parse::roundedFloat( '-123.45', 1 ) );
    }


    public function testRoundedFloatForInvalid() : void {
        self::expectException( ParseException::class );
        Parse::roundedFloat( 'not a float' );
    }


    public function testRoundedInt() : void {
        self::assertSame( 123, Parse::roundedInt( '123.45' ) );
        self::assertSame( 124, Parse::roundedInt( '123.55' ) );
        self::assertSame( -123, Parse::roundedInt( '-123.45' ) );
        self::assertSame( 123, Parse::roundedInt( '123.456', 1 ) );
    }


    public function testRoundedIntForInvalid() : void {
        self::expectException( ParseException::class );
        Parse::roundedInt( 'not a float' );
    }


    public function testSummarizeOptions() : void {
        $r = [ 'a', 'b', 'c', 'd', 'e' ];
        self::assertEquals( 'a, b, c, d, e', Parse::summarizeOptions( $r ) );

        $r = [ 'a', 'b', 'c' ];
        self::assertEquals( 'a, b, c', Parse::summarizeOptions( $r ) );

        $r = [];
        self::assertEquals( '(no options)', Parse::summarizeOptions( $r ) );

        $r = [ 'a', 'b', 'c', 'd', 'e', 'f', 'g', 'h', 'i', 'j' ];
        self::assertEquals( 'a, b, c, d, ...', Parse::summarizeOptions( $r ) );
    }


    public function testTime() : void {
        self::assertSame( '12:34:56', Parse::time( '2024-01-25 12:34:56' ) );
        self::assertSame( '15:53:00', Parse::time( 'March 2, 2025 3:53 PM' ) );
        self::assertSame( '00:00:00', Parse::time( '2024-01-26 + 2 days' ) );
        self::expectException( ParseException::class );
        Parse::time( 'foo' );
    }


    public function testTimeStamp() : void {
        self::assertSame( strtotime( '2024-01-25 12:34:56' ), Parse::timeStamp( '2024-01-25 12:34:56' ) );
        self::assertSame( strtotime( '2025-03-02 15:53:00' ), Parse::timeStamp( 'March 2, 2025 3:53 PM' ) );
    }


    public function testTimeStampForInvalid() : void {
        self::expectException( ParseException::class );
        Parse::timeStamp( 'foo' );
    }


    public function testTimeStampPreservesInstantAcrossTimezone() : void {
        $stOriginal = date_default_timezone_get();
        date_default_timezone_set( 'America/Los_Angeles' );
        try {
            $input = '2024-01-25 12:34:56';
            self::assertSame( strtotime( $input ), Parse::timeStamp( $input ) );
        } finally {
            date_default_timezone_set( $stOriginal );
        }
    }


    public function testUnsignedFloat() : void {
        self::assertSame( 123.45, Parse::unsignedFloat( '123.45' ) );
        self::assertSame( 0.0, Parse::unsignedFloat( '0' ) );
    }


    public function testUnsignedFloatForInvalid() : void {
        self::expectException( ParseException::class );
        Parse::unsignedFloat( '-1' );
    }


    public function testUnsignedInt() : void {
        self::assertSame( 123, Parse::unsignedInt( '123' ) );
        self::assertSame( 0, Parse::unsignedInt( '0' ) );
    }


    public function testUnsignedIntForInvalid() : void {
        self::expectException( ParseException::class );
        Parse::unsignedInt( '-1' );
    }


}
