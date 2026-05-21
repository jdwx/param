<?php


declare( strict_types = 1 );


use JDWX\Param\Parse;
use JDWX\Param\ParseException;
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


    public function testIpForInvalid() : void {
        self::expectException( ParseException::class );
        Parse::ip( 'foo' );
    }


    public function testIpv4() : void {
        self::assertSame( '127.0.0.1', Parse::ipv4( '127.0.0.1' ) );
        self::assertSame( '10.20.40.80', Parse::ipv4( '10.20.40.80' ) );
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
