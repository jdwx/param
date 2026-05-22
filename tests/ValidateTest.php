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
