<?php


declare( strict_types = 1 );


use JDWX\Param\Parameter;
use JDWX\Param\ParseException;
use PHPUnit\Framework\TestCase;


require_once __DIR__ . '/MyTestParameter.php';


class ParameterTest extends TestCase {


    public function testAsArrayOrNull() : void {

        $x = new Parameter( [ 'foo', 'bar' ] );
        static::assertSame( [ 'foo', 'bar' ], $x->asArrayOrNull() );

        $x = new Parameter( null );
        static::assertNull( $x->asArrayOrNull() );

        $x = new Parameter( 'foo' );
        static::expectException( TypeError::class );
        $x->asArrayOrNull();

    }


    public function testAsArrayOrString() : void {
        $x = new Parameter( [ 'foo', 'bar' ] );
        static::assertSame( [ 'foo', 'bar' ], $x->asArrayOrString() );

        $x = new Parameter( 'foo' );
        static::assertSame( 'foo', $x->asArrayOrString() );

        $x = new Parameter( null );
        static::expectException( TypeError::class );
        $x->asArrayOrString();
    }


    public function testAsArrayWithArray() : void {
        $x = new Parameter( [ 'foo', 'bar' ] );
        static::assertSame( [ 'foo', 'bar' ], $x->asArray() );
    }


    public function testAsArrayWithNull() : void {
        $x = new Parameter( null );
        static::expectException( TypeError::class );
        $x->asArray();
    }


    public function testAsArrayWithString() : void {
        $x = new Parameter( '5' );
        static::expectException( TypeError::class );
        $x->asArray();
    }


    public function testAsBool() : void {

        $x = new Parameter( 'true' );
        static::assertTrue( $x->asBool() );

        $x = new Parameter( 'false' );
        static::assertFalse( $x->asBool() );

        $x = new Parameter( 'yes' );
        static::assertTrue( $x->asBool() );

        $x = new Parameter( 'no' );
        static::assertFalse( $x->asBool() );

        $x = new Parameter( 'on' );
        static::assertTrue( $x->asBool() );

        $x = new Parameter( 'off' );
        static::assertFalse( $x->asBool() );

        $x = new Parameter( '1' );
        static::assertTrue( $x->asBool() );

        $x = new Parameter( '0' );
        static::assertFalse( $x->asBool() );

        $x = new Parameter( null );
        static::assertFalse( $x->asBool() );

        $x = new Parameter( 'foo' );
        static::expectException( ParseException::class );
        $x->asBool();

    }


    public function testAsBoolOrNull() : void {
        $x = new Parameter( 'false' );
        static::assertFalse( $x->asBoolOrNull() );

        $x = new Parameter( null );
        static::assertNull( $x->asBoolOrNull() );

    }


    public function testAsCurrency() : void {
        $x = new Parameter( '5.5' );
        static::assertSame( 550, $x->asCurrency() );

        $x = new Parameter( '5' );
        static::assertSame( 500, $x->asCurrency() );

        $x = new Parameter( '5.000000000001' );
        static::assertSame( 500, $x->asCurrency() );

        $x = new Parameter( '4.999999999999' );
        static::assertSame( 500, $x->asCurrency() );

        $x = new Parameter( 'foo' );
        static::expectException( ParseException::class );
        $x->asCurrency();
    }


    public function testAsCurrencyForEmpty() : void {
        $x = new Parameter( '' );
        static::expectException( ParseException::class );
        $x->asCurrency();
    }


    public function testAsCurrencyOrEmpty() : void {
        $x = new Parameter( '5.5' );
        static::assertSame( 550, $x->asCurrencyOrEmpty() );

        $x = new Parameter( '' );
        static::assertNull( $x->asCurrencyOrEmpty() );

        $x = new Parameter( 'foo' );
        static::expectException( ParseException::class );
        $x->asCurrencyOrEmpty();
    }


    public function testAsCurrencyOrNull() : void {
        $x = new Parameter( '5.5' );
        static::assertSame( 550, $x->asCurrencyOrNull() );

        $x = new Parameter( null );
        static::assertNull( $x->asCurrencyOrNull() );
    }


    public function testAsEmailAddress() : void {
        $x = new Parameter( 'test@example.tst' );
        static::assertSame( 'test@example.tst', $x->asEmailAddress() );

        $x = new Parameter( 'foo' );
        static::expectException( ParseException::class );
        $x->asEmailAddress();
    }


    public function testAsEmailAddressOrEmpty() : void {
        $x = new Parameter( 'test@example.tst' );
        static::assertSame( 'test@example.tst', $x->asEmailAddressOrEmpty() );

        $x = new Parameter( '' );
        static::assertNull( $x->asEmailAddressOrEmpty() );

        $x = new Parameter( 'foo' );
        static::expectException( ParseException::class );
        $x->asEmailAddressOrEmpty();
    }


    public function testAsEmailAddressOrNull() : void {
        $x = new Parameter( 'test@example.tst' );
        static::assertSame( 'test@example.tst', $x->asEmailAddressOrNull() );

        $x = new Parameter( null );
        static::assertNull( $x->asEmailAddressOrNull() );

        $x = new Parameter( 'foo' );
        static::expectException( ParseException::class );
        $x->asEmailAddressOrNull();
    }


    public function testAsEmailUsername() : void {
        $x = new Parameter( 'test' );
        static::assertSame( 'test', $x->asEmailUsername() );

        $x = new Parameter( 'fo@o' );
        static::expectException( ParseException::class );
        $x->asEmailUsername();
    }


    public function testAsEmailUsernameOrEmpty() : void {
        $x = new Parameter( 'test' );
        static::assertSame( 'test', $x->asEmailUsernameOrEmpty() );

        $x = new Parameter( '' );
        static::assertNull( $x->asEmailUsernameOrEmpty() );

        $x = new Parameter( 'fo o' );
        static::expectException( ParseException::class );
        $x->asEmailUsernameOrEmpty();
    }


    public function testAsEmailUsernameOrNull() : void {
        $x = new Parameter( 'test' );
        static::assertSame( 'test', $x->asEmailUsernameOrNull() );

        $x = new Parameter( null );
        static::assertNull( $x->asEmailUsernameOrNull() );

        $x = new Parameter( '<foo' );
        static::expectException( ParseException::class );
        $x->asEmailUsernameOrNull();
    }


    public function testAsExistingDirectory() : void {
        $x = new Parameter( sys_get_temp_dir() );
        static::assertSame( sys_get_temp_dir(), $x->asExistingDirectory() );

        $x = new Parameter( 'foo' );
        static::expectException( ParseException::class );
        $x->asExistingDirectory();
    }


    public function testAsExistingDirectoryOrEmpty() : void {
        $x = new Parameter( sys_get_temp_dir() );
        static::assertSame( sys_get_temp_dir(), $x->asExistingDirectoryOrEmpty() );

        $x = new Parameter( '' );
        static::assertNull( $x->asExistingDirectoryOrEmpty() );

        $x = new Parameter( 'foo' );
        static::expectException( ParseException::class );
        $x->asExistingDirectoryOrEmpty();
    }


    public function testAsExistingDirectoryOrNull() : void {
        $x = new Parameter( sys_get_temp_dir() );
        static::assertSame( sys_get_temp_dir(), $x->asExistingDirectoryOrNull() );

        $x = new Parameter( null );
        static::assertNull( $x->asExistingDirectoryOrNull() );

        $x = new Parameter( 'foo' );
        static::expectException( ParseException::class );
        $x->asExistingDirectoryOrNull();
    }


    public function testAsExistingFilename() : void {
        $x = new Parameter( __FILE__ );
        static::assertSame( __FILE__, $x->asExistingFilename() );

        $x = new Parameter( 'foo' );
        static::expectException( ParseException::class );
        $x->asExistingFilename();
    }


    public function testAsExistingFilenameOrEmpty() : void {
        $x = new Parameter( __FILE__ );
        static::assertSame( __FILE__, $x->asExistingFilenameOrEmpty() );

        $x = new Parameter( '' );
        static::assertNull( $x->asExistingFilenameOrEmpty() );

        $x = new Parameter( 'foo' );
        static::expectException( ParseException::class );
        $x->asExistingFilenameOrEmpty();
    }


    public function testAsExistingFilenameOrNull() : void {
        $x = new Parameter( __FILE__ );
        static::assertSame( __FILE__, $x->asExistingFilenameOrNull() );

        $x = new Parameter( null );
        static::assertNull( $x->asExistingFilenameOrNull() );

        $x = new Parameter( 'foo' );
        static::expectException( ParseException::class );
        $x->asExistingFilenameOrNull();
    }


    public function testAsFloatForArray() : void {
        $x = new Parameter( [ 'foo', 'bar' ] );
        static::expectException( TypeError::class );
        $x->asFloat();
    }


    public function testAsFloatForNull() : void {
        $x = new Parameter( null );
        static::expectException( TypeError::class );
        $x->asFloat();
    }


    public function testAsFloatForStringFloat() : void {

        $x = new Parameter( '5.5' );
        static::assertIsFloat( $x->asFloat() );
        static::assertEqualsWithDelta( 5.5, $x->asFloat(), 0.0001 );

    }


    public function testAsFloatForStringInt() : void {

        $x = new Parameter( '5' );
        static::assertIsFloat( $x->asFloat() );
        static::assertEqualsWithDelta( 5.0, $x->asFloat(), 0.0001 );

    }


    public function testAsFloatForStringText() : void {

        $x = new Parameter( 'foo' );
        static::expectException( ParseException::class );
        $x->asFloat();

    }


    public function testAsFloatOrEmpty() : void {
        $x = new Parameter( '5.5' );
        static::assertEqualsWithDelta( 5.5, $x->asFloatOrEmpty(), 0.0001 );

        $x = new Parameter( '' );
        static::assertNull( $x->asFloatOrEmpty() );

        $x = new Parameter( 'foo' );
        static::expectException( ParseException::class );
        $x->asFloatOrEmpty();
    }


    public function testAsFloatOrEmptyForArray() : void {
        $x = new Parameter( [ 'foo', 'bar' ] );
        static::expectException( TypeError::class );
        $x->asFloatOrEmpty();
    }


    public function testAsFloatOrEmptyForNull() : void {
        $x = new Parameter( null );
        static::expectException( TypeError::class );
        $x->asFloatOrEmpty();
    }


    public function testAsFloatOrNull() : void {

        $x = new Parameter( '5.5' );
        static::assertEqualsWithDelta( 5.5, $x->asFloatOrNull(), 0.0001 );

        $x = new Parameter( null );
        static::assertNull( $x->asFloatOrNull() );

    }


    public function testAsFloatRangeClosed() : void {
        $x = new Parameter( '5.5' );
        static::assertEqualsWithDelta( 5.5, $x->asFloatRangeClosed( 5.4, 5.6 ), 0.0001 );
        static::expectException( ParseException::class );
        $x->asFloatRangeClosed( 5.5, 5.7 );
    }


    public function testAsFloatRangeClosedOrEmpty() : void {
        $x = new Parameter( '5.5' );
        static::assertEqualsWithDelta( 5.5, $x->asFloatRangeClosedOrEmpty( 5.4, 5.6 ), 0.0001 );

        $x = new Parameter( '' );
        static::assertNull( $x->asFloatRangeClosedOrEmpty( 5.4, 5.6 ) );

        $x = new Parameter( 'foo' );
        static::expectException( ParseException::class );
        $x->asFloatRangeClosedOrEmpty( 5.4, 5.6 );

    }


    public function testAsFloatRangeClosedOrNull() : void {
        $x = new Parameter( '5.5' );
        static::assertEqualsWithDelta( 5.5, $x->asFloatRangeClosedOrNull( 5.4, 5.6 ), 0.0001 );

        $x = new Parameter( null );
        static::assertNull( $x->asFloatRangeClosedOrNull( 5.4, 5.6 ) );

        $x = new Parameter( '5.5.4' );
        static::expectException( ParseException::class );
        $x->asFloatRangeClosedOrNull( 5.4, 5.6 );
    }


    public function testAsFloatRangeHalfClosed() : void {
        $x = new Parameter( '5.5' );
        static::assertEqualsWithDelta( 5.5, $x->asFloatRangeHalfClosed( 5.5, 5.6 ), 0.0001 );

        static::expectException( ParseException::class );
        static::assertEqualsWithDelta( 5.5, $x->asFloatRangeHalfClosed( 5.4, 5.5 ), 0.0001 );
    }


    public function testAsFloatRangeHalfClosedOrEmpty() : void {
        $x = new Parameter( '5.5' );
        static::assertEqualsWithDelta( 5.5, $x->asFloatRangeHalfClosedOrEmpty( 5.5, 5.6 ), 0.0001 );

        $x = new Parameter( '' );
        static::assertNull( $x->asFloatRangeHalfClosedOrEmpty( 5.5, 5.6 ) );

        $x = new Parameter( 'foo' );
        static::expectException( ParseException::class );
        $x->asFloatRangeHalfClosedOrEmpty( 5.5, 5.6 );
    }


    public function testAsFloatRangeHalfClosedOrNull() : void {
        $x = new Parameter( '5.5' );
        static::assertEqualsWithDelta( 5.5, $x->asFloatRangeHalfClosedOrNull( 5.5, 5.6 ), 0.0001 );

        $x = new Parameter( null );
        static::assertNull( $x->asFloatRangeHalfClosedOrNull( 5.5, 5.6 ) );

        $x = new Parameter( '5.5.4' );
        static::expectException( ParseException::class );
        $x->asFloatRangeHalfClosedOrNull( 5.5, 5.6 );
    }


    public function testAsFloatRangeOpen() : void {
        $x = new Parameter( '5.5' );
        static::assertEqualsWithDelta( 5.5, $x->asFloatRangeOpen( 5.4, 5.5 ), 0.0001 );
        static::assertEqualsWithDelta( 5.5, $x->asFloatRangeOpen( 5.5, 5.6 ), 0.0001 );

        static::expectException( ParseException::class );
        $x->asFloatRangeOpen( 5.6, 5.7 );
    }


    public function testAsFloatRangeOpenOrEmpty() : void {
        $x = new Parameter( '5.5' );
        static::assertEqualsWithDelta( 5.5, $x->asFloatRangeOpenOrEmpty( 5.4, 5.5 ), 0.0001 );

        $x = new Parameter( '' );
        static::assertNull( $x->asFloatRangeOpenOrEmpty( 5.4, 5.5 ) );

        $x = new Parameter( 'foo' );
        static::expectException( ParseException::class );
        $x->asFloatRangeOpenOrEmpty( 5.4, 5.5 );
    }


    public function testAsFloatRangeOpenOrNull() : void {
        $x = new Parameter( '5.5' );
        static::assertEqualsWithDelta( 5.5, $x->asFloatRangeOpenOrNull( 5.4, 5.5 ), 0.0001 );

        $x = new Parameter( null );
        static::assertNull( $x->asFloatRangeOpenOrNull( 5.4, 5.5 ) );

        $x = new Parameter( '5.5.4' );
        static::expectException( ParseException::class );
        $x->asFloatRangeOpenOrNull( 5.4, 5.5 );
    }


    public function testAsHostname() : void {
        $x = new Parameter( 'www.example.com' );
        static::assertSame( 'www.example.com', $x->asHostname() );

        $x = new Parameter( 'foo' );
        static::expectException( ParseException::class );
        $x->asHostname();
    }


    public function testAsHostnameForTrailingDot() : void {
        $x = new Parameter( 'www.example.com.' );
        static::expectException( ParseException::class );
        $x->asHostname();
    }


    public function testAsHostnameOrEmpty() : void {
        $x = new Parameter( 'www.example.com' );
        static::assertSame( 'www.example.com', $x->asHostnameOrEmpty() );

        $x = new Parameter( '' );
        static::assertNull( $x->asHostnameOrEmpty() );

        $x = new Parameter( 'www|example.com' );
        static::expectException( ParseException::class );
        $x->asHostnameOrEmpty();
    }


    public function testAsHostnameOrNull() : void {
        $x = new Parameter( 'www.example.com' );
        static::assertSame( 'www.example.com', $x->asHostnameOrNull() );

        $x = new Parameter( null );
        static::assertNull( $x->asHostnameOrNull() );

        $x = new Parameter( 'www example.com' );
        static::expectException( ParseException::class );
        $x->asHostnameOrNull();
    }


    public function testAsIP() : void {
        $x = new Parameter( '192.0.2.1' );
        static::assertSame( '192.0.2.1', $x->asIP() );

        $x = new Parameter( '2001:db8::1' );
        static::assertSame( '2001:db8::1', $x->asIP() );

        $x = new Parameter( 'foo' );
        static::expectException( ParseException::class );
        $x->asIP();
    }


    public function testAsIPOrEmpty() : void {
        $x = new Parameter( '192.0.2.2' );
        static::assertSame( '192.0.2.2', $x->asIPOrEmpty() );

        $x = new Parameter( '' );
        static::assertNull( $x->asIPOrEmpty() );

        $x = new Parameter( 'foo' );
        static::expectException( ParseException::class );
        $x->asIPOrEmpty();
    }


    public function testAsIPOrNull() : void {
        $x = new Parameter( '192.0.2.3' );
        static::assertSame( '192.0.2.3', $x->asIPOrNull() );

        $x = new Parameter( null );
        static::assertNull( $x->asIPOrNull() );

        $x = new Parameter( 'foo' );
        static::expectException( ParseException::class );
        $x->asIPOrNull();
    }


    public function testAsIPv4() : void {
        $x = new Parameter( '192.0.2.1' );
        static::assertSame( '192.0.2.1', $x->asIPv4() );

        $x = new Parameter( '2001:db8::1' );
        static::expectException( ParseException::class );
        $x->asIPv4();
    }


    public function testAsIPv4OrEmpty() : void {
        $x = new Parameter( '192.0.2.2' );
        static::assertSame( '192.0.2.2', $x->asIPv4OrEmpty() );

        $x = new Parameter( '' );
        static::assertNull( $x->asIPv4OrEmpty() );

        $x = new Parameter( 'foo' );
        static::expectException( ParseException::class );
        $x->asIPv4OrEmpty();
    }


    public function testAsIPv4OrNull() : void {
        $x = new Parameter( '192.0.2.3' );
        static::assertSame( '192.0.2.3', $x->asIPv4OrNull() );

        $x = new Parameter( null );
        static::assertNull( $x->asIPv4OrNull() );

        $x = new Parameter( '192.0.2.3.4' );
        static::expectException( ParseException::class );
        $x->asIPv4OrNull();
    }


    public function testAsIPv6() : void {
        $x = new Parameter( '2001:db8::1' );
        static::assertSame( '2001:db8::1', $x->asIPv6() );

        $x = new Parameter( '192.0.2.1' );
        static::expectException( ParseException::class );
        $x->asIPv6();
    }


    public function testAsIPv6OrEmpty() : void {
        $x = new Parameter( '2001:db8::2' );
        static::assertSame( '2001:db8::2', $x->asIPv6OrEmpty() );

        $x = new Parameter( '' );
        static::assertNull( $x->asIPv6OrEmpty() );

        $x = new Parameter( 'foo' );
        static::expectException( ParseException::class );
        $x->asIPv6OrEmpty();
    }


    public function testAsIPv6OrNull() : void {
        $x = new Parameter( '::ffff:0:255.255.255.255' );
        static::assertSame( '::ffff:0:255.255.255.255', $x->asIPv6OrNull() );

        $x = new Parameter( null );
        static::assertNull( $x->asIPv6OrNull() );

        $x = new Parameter( '2001:db8::3.4' );
        static::expectException( ParseException::class );
        $x->asIPv6OrNull();
    }


    public function testAsInt() : void {

        $x = new Parameter( '5' );
        static::assertSame( 5, $x->asInt() );

        $x = new Parameter( '5.9' );
        static::assertSame( 5, $x->asInt() );

        static::expectException( ParseException::class );
        $x = new Parameter( 'foo' );
        $x->asInt();

    }


    public function testAsIntForEmpty() : void {
        $x = new Parameter( '' );
        static::expectException( ParseException::class );
        $x->asInt();
    }


    public function testAsIntOrEmpty() : void {
        $x = new Parameter( '' );
        static::assertNull( $x->asIntOrEmpty() );

        $x = new Parameter( '5' );
        static::assertSame( 5, $x->asIntOrEmpty() );

        $x = new Parameter( '5.9' );
        static::assertSame( 5, $x->asIntOrEmpty() );

        $x = new Parameter( 'foo' );
        static::expectException( ParseException::class );
        $x->asIntOrEmpty();
    }


    public function testAsIntOrNull() : void {

        $x = new Parameter( '5' );
        static::assertSame( 5, $x->asIntOrNull() );

        $x = new Parameter( null );
        static::assertNull( $x->asIntOrNull() );

    }


    public function testAsIntRangeClosed() : void {
        $x = new Parameter( '5' );
        static::assertSame( 5, $x->asIntRangeClosed( 4, 6 ) );
        static::expectException( ParseException::class );
        $x->asIntRangeClosed( 5, 7 );
    }


    public function testAsIntRangeClosedOrEmpty() : void {
        $x = new Parameter( '5' );
        static::assertSame( 5, $x->asIntRangeClosedOrEmpty( 4, 6 ) );

        $x = new Parameter( '' );
        static::assertNull( $x->asIntRangeClosedOrEmpty( 4, 6 ) );

        $x = new Parameter( 'foo' );
        static::expectException( ParseException::class );
        $x->asIntRangeClosedOrEmpty( 4, 6 );
    }


    public function testAsIntRangeClosedOrNull() : void {
        $x = new Parameter( '5' );
        static::assertSame( 5, $x->asIntRangeClosedOrNull( 4, 6 ) );

        $x = new Parameter( null );
        static::assertNull( $x->asIntRangeClosedOrNull( 4, 6 ) );

        $x = new Parameter( '' );
        static::expectException( ParseException::class );
        $x->asIntRangeClosedOrNull( 4, 6 );
    }


    public function testAsIntRangeHalfClosed() : void {
        $x = new Parameter( '5' );
        static::assertSame( 5, $x->asIntRangeHalfClosed( 5, 6 ) );
        static::expectException( ParseException::class );
        $x->asIntRangeHalfClosed( 4, 5 );
    }


    public function testAsIntRangeHalfClosedOrEmpty() : void {
        $x = new Parameter( '5' );
        static::assertSame( 5, $x->asIntRangeHalfClosedOrEmpty( 5, 6 ) );

        $x = new Parameter( '' );
        static::assertNull( $x->asIntRangeHalfClosedOrEmpty( 5, 6 ) );

        $x = new Parameter( 'foo' );
        static::expectException( ParseException::class );
        $x->asIntRangeHalfClosedOrEmpty( 5, 6 );
    }


    public function testAsIntRangeHalfClosedOrNull() : void {
        $x = new Parameter( '5' );
        static::assertSame( 5, $x->asIntRangeHalfClosedOrNull( 5, 6 ) );

        $x = new Parameter( null );
        static::assertNull( $x->asIntRangeHalfClosedOrNull( 5, 6 ) );

        $x = new Parameter( '' );
        static::expectException( ParseException::class );
        $x->asIntRangeHalfClosedOrNull( 5, 6 );
    }


    public function testAsIntRangeOpen() : void {
        $x = new Parameter( '5' );
        static::assertSame( 5, $x->asIntRangeOpen( 4, 6 ) );
        static::assertSame( 5, $x->asIntRangeOpen( 4, 5 ) );
        static::assertSame( 5, $x->asIntRangeOpen( 5, 6 ) );
        static::expectException( ParseException::class );
        $x->asIntRangeOpen( 6, 7 );
    }


    public function testAsIntRangeOpenOrEmpty() : void {
        $x = new Parameter( '5' );
        static::assertSame( 5, $x->asIntRangeOpenOrEmpty( 5, 6 ) );

        $x = new Parameter( '' );
        static::assertNull( $x->asIntRangeOpenOrEmpty( 4, 6 ) );

        $x = new Parameter( 'foo' );
        static::expectException( ParseException::class );
        $x->asIntRangeOpenOrEmpty( 4, 6 );
    }


    public function testAsIntRangeOpenOrNull() : void {
        $x = new Parameter( '5' );
        static::assertSame( 5, $x->asIntRangeOpenOrNull( 4, 6 ) );

        $x = new Parameter( null );
        static::assertNull( $x->asIntRangeOpenOrNull( 4, 6 ) );

        $x = new Parameter( '' );
        static::expectException( ParseException::class );
        $x->asIntRangeOpenOrNull( 4, 6 );
    }


    public function testAsJSON() : void {
        $x = new Parameter( [ 'foo', 'bar' ] );
        static::assertSame( '["foo","bar"]', $x->asJSON() );
    }


    public function testAsKeyword() : void {
        $x = new Parameter( 'foo' );
        static::assertSame( 'foo', $x->asKeyword( [ 'foo', 'bar' ] ) );

        $x = new Parameter( 'baz' );
        static::expectException( ParseException::class );
        $x->asKeyword( [ 'foo', 'bar' ] );
    }


    public function testAsKeywordOrEmpty() : void {
        $x = new Parameter( 'foo' );
        static::assertSame( 'foo', $x->asKeywordOrEmpty( [ 'foo', 'bar' ] ) );

        $x = new Parameter( '' );
        static::assertNull( $x->asKeywordOrEmpty( [ 'foo', 'bar' ] ) );

        $x = new Parameter( 'baz' );
        static::expectException( ParseException::class );
        $x->asKeywordOrEmpty( [ 'foo', 'bar' ] );
    }


    public function testAsKeywordOrNull() : void {
        $x = new Parameter( 'foo' );
        static::assertSame( 'foo', $x->asKeywordOrNull( [ 'foo', 'bar' ] ) );

        $x = new Parameter( null );
        static::assertNull( $x->asKeywordOrNull( [ 'foo', 'bar' ] ) );

        $x = new Parameter( 'baz' );
        static::expectException( ParseException::class );
        $x->asKeywordOrNull( [ 'foo', 'bar' ] );
    }


    public function testAsMap() : void {
        $rMap = [ 'foo' => 'bar', 'baz' => 'qux' ];
        $x = new Parameter( 'foo' );
        static::assertSame( 'bar', $x->asMap( $rMap ) );

        $x = new Parameter( 'quux' );
        static::expectException( ParseException::class );
        $x->asMap( $rMap );
    }


    public function testAsMapOrEmpty() : void {
        $rMap = [ 'foo' => 'bar', 'baz' => 'qux' ];
        $x = new Parameter( 'foo' );
        static::assertSame( 'bar', $x->asMapOrEmpty( $rMap ) );

        $x = new Parameter( '' );
        static::assertNull( $x->asMapOrEmpty( $rMap ) );

        $x = new Parameter( '5' );
        static::expectException( ParseException::class );
        $x->asMapOrEmpty( $rMap );
    }


    public function testAsMapOrNull() : void {
        $rMap = [ 'foo' => 'bar', 'baz' => 'qux' ];
        $x = new Parameter( 'foo' );
        static::assertSame( 'bar', $x->asMapOrNull( $rMap ) );

        $x = new Parameter( null );
        static::assertNull( $x->asMapOrNull( $rMap ) );

        $x = new Parameter( '' );
        static::expectException( ParseException::class );
        $x->asMapOrNull( $rMap );
    }


    public function testAsNonexistentFilename() : void {
        $x = new Parameter( __DIR__ . '/foo' );
        static::assertSame( __DIR__ . '/foo', $x->asNonexistentFilename() );

        $x = new Parameter( __FILE__ );
        static::expectException( ParseException::class );
        $x->asNonexistentFilename();
    }


    public function testAsNonexistentFilenameOrEmpty() : void {
        $x = new Parameter( __DIR__ . '/foo' );
        static::assertSame( __DIR__ . '/foo', $x->asNonexistentFilenameOrEmpty() );

        $x = new Parameter( '' );
        static::assertNull( $x->asNonexistentFilenameOrEmpty() );

        $x = new Parameter( '/no/such/dir/file.txt' );
        static::expectException( ParseException::class );
        $x->asNonexistentFilenameOrEmpty();
    }


    public function testAsNonexistentFilenameOrNull() : void {
        $x = new Parameter( __DIR__ . '/foo' );
        static::assertSame( __DIR__ . '/foo', $x->asNonexistentFilenameOrNull() );

        $x = new Parameter( null );
        static::assertNull( $x->asNonexistentFilenameOrNull() );

        $x = new Parameter( '' );
        static::expectException( ParseException::class );
        $x->asNonexistentFilenameOrNull();
    }


    public function testAsPositiveFloat() : void {
        $x = new Parameter( '3.14' );
        static::assertEqualsWithDelta( 3.14, $x->asPositiveFloat(), 0.0001 );

        $x = new Parameter( '-1.23' );
        static::expectException( ParseException::class );
        $x->asPositiveFloat();
    }


    public function testAsPositiveFloatOrEmpty() : void {
        $x = new Parameter( '0.1' );
        static::assertEqualsWithDelta( 0.1, $x->asPositiveFloatOrEmpty(), 0.0001 );

        $x = new Parameter( '' );
        static::assertNull( $x->asPositiveFloatOrEmpty() );

        $x = new Parameter( '0' );
        static::expectException( ParseException::class );
        $x->asPositiveFloatOrEmpty();
    }


    public function testAsPositiveFloatOrNull() : void {
        $x = new Parameter( '3.14' );
        static::assertEqualsWithDelta( 3.14, $x->asPositiveFloatOrNull(), 0.0001 );

        $x = new Parameter( null );
        static::assertNull( $x->asPositiveFloatOrNull() );

        $x = new Parameter( '' );
        static::expectException( ParseException::class );
        $x->asPositiveFloatOrNull();
    }


    public function testAsPositiveInt() : void {
        $x = new Parameter( '3' );
        static::assertSame( 3, $x->asPositiveInt() );

        $x = new Parameter( '-1' );
        static::expectException( ParseException::class );
        $x->asPositiveInt();
    }


    public function testAsPositiveIntOrEmpty() : void {
        $x = new Parameter( '3' );
        static::assertSame( 3, $x->asPositiveIntOrEmpty() );

        $x = new Parameter( '' );
        static::assertNull( $x->asPositiveIntOrEmpty() );

        $x = new Parameter( '0' );
        static::expectException( ParseException::class );
        $x->asPositiveIntOrEmpty();
    }


    public function testAsPositiveIntOrNull() : void {
        $x = new Parameter( '3' );
        static::assertSame( 3, $x->asPositiveIntOrNull() );

        $x = new Parameter( null );
        static::assertNull( $x->asPositiveIntOrNull() );

        $x = new Parameter( '' );
        static::expectException( ParseException::class );
        $x->asPositiveIntOrNull();
    }


    public function testAsRoundedInt() : void {
        $x = new Parameter( '5.5' );
        static::assertSame( 6, $x->asRoundedInt() );

        $x = new Parameter( '5.4' );
        static::assertSame( 5, $x->asRoundedInt() );
    }


    public function testAsString() : void {
        $x = new Parameter( '5.5' );
        static::assertIsString( $x->asString() );
        static::assertSame( '5.5', $x->asString() );
        $x = new Parameter( '5' );
        static::assertIsString( $x->asString() );
        static::assertSame( '5', $x->asString() );
        static::expectException( TypeError::class );
        $x = new Parameter( [ 'foo', 'bar' ] );
        $x->asString();
    }


    public function testAsStringOrNull() : void {

        $x = new Parameter( 'foo' );
        static::assertSame( 'foo', $x->asStringOrNull() );

        $x = new Parameter( null );
        static::assertNull( $x->asStringOrNull() );

    }


    public function testAsUnsignedFloat() : void {
        $x = new Parameter( '1.23' );
        static::assertEqualsWithDelta( 1.23, $x->asUnsignedFloat(), 0.0001 );

        $x = new Parameter( '-1.23' );
        static::expectException( ParseException::class );
        $x->asUnsignedFloat();
    }


    public function testAsUnsignedFloatOrEmpty() : void {
        $x = new Parameter( '0' );
        static::assertEqualsWithDelta( 0.0, $x->asUnsignedFloatOrEmpty(), 0.0001 );

        $x = new Parameter( '' );
        static::assertNull( $x->asUnsignedFloatOrEmpty() );

        $x = new Parameter( 'foo' );
        static::expectException( ParseException::class );
        $x->asUnsignedFloatOrEmpty();
    }


    public function testAsUnsignedFloatOrNull() : void {
        $x = new Parameter( '1.23' );
        static::assertEqualsWithDelta( 1.23, $x->asUnsignedFloatOrNull(), 0.0001 );

        $x = new Parameter( null );
        static::assertNull( $x->asUnsignedFloatOrNull() );

        $x = new Parameter( '' );
        static::expectException( ParseException::class );
        $x->asUnsignedFloatOrNull();
    }


    public function testAsUnsignedInt() : void {
        $x = new Parameter( '5' );
        static::assertSame( 5, $x->asUnsignedInt() );

        $x = new Parameter( '-5' );
        static::expectException( ParseException::class );
        $x->asUnsignedInt();
    }


    public function testAsUnsignedIntOrEmpty() : void {
        $x = new Parameter( '0' );
        static::assertSame( 0, $x->asUnsignedIntOrEmpty() );

        $x = new Parameter( '' );
        static::assertNull( $x->asUnsignedIntOrEmpty() );

        $x = new Parameter( 'foo' );
        static::expectException( ParseException::class );
        $x->asUnsignedIntOrEmpty();
    }


    public function testAsUnsignedIntOrNull() : void {
        $x = new Parameter( '5' );
        static::assertSame( 5, $x->asUnsignedIntOrNull() );

        $x = new Parameter( null );
        static::assertNull( $x->asUnsignedIntOrNull() );

        $x = new Parameter( '' );
        static::expectException( ParseException::class );
        $x->asUnsignedIntOrNull();
    }


    public function testConstructWithArray() : void {
        $x = new Parameter( [ 'foo', 'bar' ] );
        static::assertInstanceOf( Parameter::class, $x );

    }


    /** @suppress PhanTypeMismatchArgumentReal */
    public function testConstructWithInt() : void {
        static::expectException( TypeError::class );
        /**
         * @noinspection PhpStrictTypeCheckingInspection
         * @phpstan-ignore argument.type
         */
        $x = new Parameter( 5 );
        unset( $x );
    }


    public function testConstructWithNestedArray() : void {
        $x = new Parameter( [
            [ 'foo', 'bar' ],
            [ 'baz', 'qux' ],
        ] );
        static::assertInstanceOf( Parameter::class, $x );
        static::assertTrue( $x->isArray() );
        static::assertTrue( isset( $x[ 0 ] ) );
        static::assertTrue( $x[ 0 ]->isArray() );
        static::assertTrue( isset( $x[ 0 ][ 0 ] ) );
        static::assertSame( 'foo', $x[ 0 ][ 0 ]->asString() );

        $x = new Parameter( [
            [ 'foo', 'bar' ],
            [ 'baz', 'qux' ],
        ], nuAllowArrayDepth: 1 );
        static::expectException( InvalidArgumentException::class );
        $y = $x[ 0 ];
        unset( $y );
    }


    public function testConstructWithNull() : void {
        $x = new Parameter( null );
        static::assertInstanceOf( Parameter::class, $x );
        static::expectException( InvalidArgumentException::class );
        $x = new Parameter( null, bAllowNull: false );
        unset( $x );
    }


    public function testConstructWithParameter() : void {
        $x = new Parameter( 'foo' );
        static::assertSame( 'foo', $x->asString() );
        $y = new Parameter( $x );
        static::assertSame( 'foo', $y->asString() );
    }


    public function testConstructWithString() : void {
        $x = new Parameter( 'foo' );
        static::assertInstanceOf( Parameter::class, $x );
    }


    public function testForEach() : void {
        $x = new Parameter( [ 'foo', 'bar' ] );
        $r = [];
        foreach ( $x as $i => $p ) {
            $r[ $i ] = $p->asString();
        }
        static::assertSame( [ 'foo', 'bar' ], $r );
    }


    public function testFreeze() : void {
        $x = new MyTestParameter( 'foo' );
        static::assertFalse( $x->isFrozen() );
        $x->freeze();
        static::assertTrue( $x->isFrozen() );
    }


    public function testGetValue() : void {
        $x = new Parameter( '5' );
        static::assertSame( '5', $x->getValue() );

        $x = new Parameter( null );
        static::assertNull( $x->getValue() );

        $x = new Parameter( [ 'foo', 'bar' ] );
        static::assertSame( [ 'foo', 'bar' ], $x->getValue() );

    }


    public function testHas() : void {
        $x = new Parameter( [ 'foo', 'bar' ] );
        static::assertTrue( $x->offsetExists( 0 ) );
        static::assertTrue( $x->offsetExists( 1 ) );
        static::assertFalse( $x->offsetExists( 2 ) );
        static::assertFalse( $x->offsetExists( 'foo' ) );

        $x = new Parameter( [ 'foo' => 'bar', 'baz' => 'qux' ] );
        static::assertTrue( $x->offsetExists( 'foo' ) );
        static::assertTrue( $x->offsetExists( 'baz' ) );
        static::assertFalse( $x->offsetExists( 'qux' ) );
        static::assertFalse( $x->offsetExists( 0 ) );

        $x = new Parameter( 'foo' );
        static::expectException( TypeError::class );
        $x->has( 0 );
    }


    public function testIndexOrDefault() : void {

        $x = new Parameter( [ 'foo', 'bar' ] );
        static::assertSame( 'foo', $x->indexOrDefault( 0, 'baz' )->asString() );
        static::assertSame( 'baz', $x->indexOrDefault( 2, 'baz' )->asString() );

    }


    public function testIndexOrNull() : void {
        $x = new Parameter( [ 'foo', 'bar' ] );
        static::assertSame( 'foo', $x->indexOrNull( 0 )->asString() );
        static::assertNull( $x->indexOrNull( 2 ) );
    }


    public function testIsArray() : void {
        $x = new Parameter( '5.5' );
        static::assertFalse( $x->isArray() );
        $x = new Parameter( [ 'foo', 'bar' ] );
        static::assertTrue( $x->isArray() );
    }


    public function testIsEmpty() : void {
        $x = new Parameter( '5' );
        static::assertFalse( $x->isEmpty() );

        $x = new Parameter( [ 'foo', 'bar' ] );
        static::assertFalse( $x->isEmpty() );

        $x = new Parameter( null );
        static::assertTrue( $x->isEmpty() );

        $x = new Parameter( '' );
        static::assertTrue( $x->isEmpty() );

        $x = new Parameter( [] );
        static::assertTrue( $x->isEmpty() );

        $x = new Parameter( 'yup' );
        $y = new Parameter( $x );
        static::assertFalse( $y->isEmpty() );

        $x = new Parameter( null );
        $y = new Parameter( $x );
        static::assertTrue( $y->isEmpty() );
    }


    public function testIsNull() : void {

        $x = new Parameter( '5' );
        static::assertFalse( $x->isNull() );

        $x = new Parameter( [ 'foo', 'bar' ] );
        static::assertFalse( $x->isNull() );

        $x = new Parameter( null );
        static::assertTrue( $x->isNull() );

    }


    public function testIsSet() : void {
        $x = new Parameter( '5' );
        static::assertTrue( $x->isSet() );
    }


    public function testIsString() : void {
        $x = new Parameter( 'foo' );
        static::assertTrue( $x->isString() );

        $x = new Parameter( [ 'foo', 'bar' ] );
        static::assertFalse( $x->isString() );

        $x = new Parameter( null );
        static::assertFalse( $x->isString() );

    }


    public function testMutable() : void {
        $x = new MyTestParameter( 'foo' );
        static::assertFalse( $x->isMutable() );
        $x->mutate( true );
        static::assertTrue( $x->isMutable() );
        $x->mutate( false );
        static::expectException( LogicException::class );
        $x->set( 'bar' );
    }


    public function testMutableFrozen() : void {
        $x = new MyTestParameter( 'foo' );
        $x->freeze();
        static::expectException( LogicException::class );
        $x->mutate( true );
    }


    public function testNew() : void {
        $x = new Parameter( 'foo' );
        $y = $x->new( 'bar' );
        static::assertSame( 'bar', $y->asString() );
        static::assertSame( $x::class, $y::class );
    }


    public function testOffsetExists() : void {

        $x = new Parameter( [ 'foo' => 'bar', 'baz' => 'qux' ] );
        static::assertTrue( isset( $x[ 'foo' ] ) );
        static::assertFalse( isset( $x[ 'qux' ] ) );

        static::expectException( InvalidArgumentException::class );
        /** @phpstan-ignore isset.offset */
        $y = isset( $x[ null ] );
        unset( $y );

    }


    public function testOffsetGet() : void {
        $x = new Parameter( [ 'foo', 'bar' ] );
        static::assertTrue( isset( $x[ 0 ] ) );
        static::assertSame( 'foo', $x[ 0 ]->asString() );
        static::assertSame( 'bar', $x[ 1 ]->asString() );
    }


    public function testOffsetGetForInvalidArgument() : void {
        $x = new Parameter( [ 'foo', 'bar' ] );
        static::expectException( InvalidArgumentException::class );
        /** @phpstan-ignore offsetAccess.notFound */
        $y = $x[ null ];
        unset( $y );
    }


    public function testOffsetGetForOutOfBounds() : void {
        $x = new Parameter( [ 'foo', 'bar' ] );
        static::expectException( OutOfBoundsException::class );
        $y = $x[ 2 ];
        unset( $y );
    }


    public function testOffsetGetWithString() : void {
        $x = new Parameter( 'foo' );
        static::expectException( TypeError::class );
        $y = $x[ 0 ];
        unset( $y );
    }


    public function testOffsetSet() : void {
        $x = new Parameter( [ 'foo', 'bar' ] );
        static::expectException( LogicException::class );
        /** @phpstan-ignore offsetAssign.valueType */
        $x[ 0 ] = 'baz';
    }


    public function testOffsetUnset() : void {
        $x = new Parameter( [ 'foo', 'bar' ] );
        static::expectException( LogicException::class );
        unset( $x[ 0 ] );
    }


    public function testRoundedFloat() : void {
        $x = new Parameter( '5.5' );
        static::assertEqualsWithDelta( 5.5, $x->asRoundedFloat( 1 ), 0.0001 );

        $x = new Parameter( '5.4' );
        static::assertEqualsWithDelta( 5.4, $x->asRoundedFloat( 1 ), 0.0001 );

        $x = new Parameter( '1.2345' );
        static::assertEqualsWithDelta( 1.0, $x->asRoundedFloat(), 0.0001 );
        static::assertEqualsWithDelta( 1.2, $x->asRoundedFloat( 1 ), 0.0001 );
        static::assertEqualsWithDelta( 1.23, $x->asRoundedFloat( 2 ), 0.0001 );
        static::assertEqualsWithDelta( 1.235, $x->asRoundedFloat( 3 ), 0.0001 );
        static::assertEqualsWithDelta( 1.2345, $x->asRoundedFloat( 4 ), 0.0001 );
        static::assertEqualsWithDelta( 1.2345, $x->asRoundedFloat( 5 ), 0.0001 );

        $x = new Parameter( 'foo' );
        static::expectException( ParseException::class );
        $x->asRoundedFloat();
    }


    public function testRoundedFloatForEmpty() : void {
        $x = new Parameter( '' );
        static::expectException( ParseException::class );
        $x->asRoundedFloat();
    }


    public function testRoundedFloatOrEmpty() : void {
        $x = new Parameter( '5.5' );
        static::assertEqualsWithDelta( 5.5, $x->asRoundedFloatOrEmpty( 1 ), 0.0001 );

        $x = new Parameter( '' );
        static::assertNull( $x->asRoundedFloatOrEmpty() );

        $x = new Parameter( 'foo' );
        static::expectException( ParseException::class );
        $x->asRoundedFloatOrEmpty();
    }


    public function testRoundedFloatOrEmptyForNull() : void {
        $x = new Parameter( null );
        static::expectException( TypeError::class );
        $x->asRoundedFloatOrEmpty();
    }


    public function testRoundedFloatOrNull() : void {
        $x = new Parameter( '5.5' );
        static::assertEqualsWithDelta( 5.5, $x->asRoundedFloatOrNull( 1 ), 0.0001 );

        $x = new Parameter( '5.4' );
        static::assertEqualsWithDelta( 5.4, $x->asRoundedFloatOrNull( 1 ), 0.0001 );

        $x = new Parameter( null );
        static::assertNull( $x->asRoundedFloatOrNull() );
    }


    public function testRoundedInt() : void {
        $x = new Parameter( '5.5' );
        static::assertSame( 6, $x->asRoundedInt() );
        static::assertSame( 5, $x->asRoundedInt( 1 ) );


        $x = new Parameter( '5.4' );
        static::assertSame( 5, $x->asRoundedInt() );

        $x = new Parameter( '153.456' );
        static::assertSame( 153, $x->asRoundedInt() );
        static::assertSame( 150, $x->asRoundedInt( -1 ) );
        static::assertSame( 200, $x->asRoundedInt( -2 ) );

        $x = new Parameter( 'foo' );
        static::expectException( ParseException::class );
        $x->asRoundedInt();
    }


    public function testRoundedIntOrEmpty() : void {
        $x = new Parameter( '5.5' );
        static::assertSame( 6, $x->asRoundedIntOrEmpty() );

        $x = new Parameter( '' );
        static::assertNull( $x->asRoundedIntOrEmpty() );

        $x = new Parameter( 'foo' );
        static::expectException( ParseException::class );
        $x->asRoundedIntOrEmpty();
    }


    public function testRoundedIntOrNull() : void {
        $x = new Parameter( '5.5' );
        static::assertSame( 6, $x->asRoundedIntOrNull() );

        $x = new Parameter( '5.4' );
        static::assertSame( 5, $x->asRoundedIntOrNull() );

        $x = new Parameter( null );
        static::assertNull( $x->asRoundedIntOrNull() );
    }


    public function testToString() : void {
        $x = new Parameter( 'foo' );
        static::assertSame( 'foo', (string) $x );

        static::assertSame( 'xfoox', 'x' . $x . 'x' );

        $x = new Parameter( [ 'foo', 'bar' ] );
        static::expectException( TypeError::class );
        $y = (string) $x;
        unset( $y );
    }


}
