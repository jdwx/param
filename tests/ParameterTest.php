<?php


declare( strict_types = 1 );


use JDWX\Param\IParameter;
use JDWX\Param\Parameter;
use JDWX\Param\ParseException;
use PHPUnit\Framework\TestCase;


require_once __DIR__ . '/MyTestParameter.php';


class ParameterTest extends TestCase {


    public static function p( mixed $x, bool $bAllowNull = true,
                              ?int  $nuAllowArrayDepth = null ) : IParameter {
        return new Parameter( $x, $bAllowNull, $nuAllowArrayDepth );
    }


    public function testAsArrayOrNull() : void {

        $x = self::p( [ 'foo', 'bar' ] );
        static::assertSame( [ 'foo', 'bar' ], $x->asArrayOrNull() );

        $x = self::p( null );
        static::assertNull( $x->asArrayOrNull() );

        $x = self::p( 'foo' );
        static::expectException( TypeError::class );
        $x->asArrayOrNull();

    }


    public function testAsArrayOrString() : void {
        $x = self::p( [ 'foo', 'bar' ] );
        static::assertSame( [ 'foo', 'bar' ], $x->asArrayOrString() );

        $x = self::p( 'foo' );
        static::assertSame( 'foo', $x->asArrayOrString() );

        $x = self::p( null );
        static::expectException( TypeError::class );
        $x->asArrayOrString();
    }


    public function testAsArrayWithArray() : void {
        $x = self::p( [ 'foo', 'bar' ] );
        static::assertSame( [ 'foo', 'bar' ], $x->asArray() );
    }


    public function testAsArrayWithNull() : void {
        $x = self::p( null );
        static::expectException( TypeError::class );
        $x->asArray();
    }


    public function testAsArrayWithString() : void {
        $x = self::p( '5' );
        static::expectException( TypeError::class );
        $x->asArray();
    }


    public function testAsBool() : void {

        $x = self::p( 'true' );
        static::assertTrue( $x->asBool() );

        $x = self::p( 'false' );
        static::assertFalse( $x->asBool() );

        $x = self::p( 'yes' );
        static::assertTrue( $x->asBool() );

        $x = self::p( 'no' );
        static::assertFalse( $x->asBool() );

        $x = self::p( 'on' );
        static::assertTrue( $x->asBool() );

        $x = self::p( 'off' );
        static::assertFalse( $x->asBool() );

        $x = self::p( '1' );
        static::assertTrue( $x->asBool() );

        $x = self::p( '0' );
        static::assertFalse( $x->asBool() );

        $x = self::p( null );
        static::assertFalse( $x->asBool() );

        $x = self::p( 'foo' );
        static::expectException( ParseException::class );
        $x->asBool();

    }


    public function testAsBoolOrNull() : void {
        $x = self::p( 'false' );
        static::assertFalse( $x->asBoolOrNull() );

        $x = self::p( null );
        static::assertNull( $x->asBoolOrNull() );

    }


    public function testAsCurrency() : void {
        $x = self::p( '5.5' );
        static::assertSame( 550, $x->asCurrency() );

        $x = self::p( '5' );
        static::assertSame( 500, $x->asCurrency() );

        $x = self::p( '5.000000000001' );
        static::assertSame( 500, $x->asCurrency() );

        $x = self::p( '4.999999999999' );
        static::assertSame( 500, $x->asCurrency() );

        $x = self::p( 'foo' );
        static::expectException( ParseException::class );
        $x->asCurrency();
    }


    public function testAsCurrencyForEmpty() : void {
        $x = self::p( '' );
        static::expectException( ParseException::class );
        $x->asCurrency();
    }


    public function testAsCurrencyOrEmpty() : void {
        $x = self::p( '5.5' );
        static::assertSame( 550, $x->asCurrencyOrEmpty() );

        $x = self::p( '' );
        static::assertNull( $x->asCurrencyOrEmpty() );

        $x = self::p( 'foo' );
        static::expectException( ParseException::class );
        $x->asCurrencyOrEmpty();
    }


    public function testAsCurrencyOrNull() : void {
        $x = self::p( '5.5' );
        static::assertSame( 550, $x->asCurrencyOrNull() );

        $x = self::p( null );
        static::assertNull( $x->asCurrencyOrNull() );
    }


    public function testAsEmailAddress() : void {
        $x = self::p( 'test@example.tst' );
        static::assertSame( 'test@example.tst', $x->asEmailAddress() );

        $x = self::p( 'foo' );
        static::expectException( ParseException::class );
        $x->asEmailAddress();
    }


    public function testAsEmailAddressOrEmpty() : void {
        $x = self::p( 'test@example.tst' );
        static::assertSame( 'test@example.tst', $x->asEmailAddressOrEmpty() );

        $x = self::p( '' );
        static::assertNull( $x->asEmailAddressOrEmpty() );

        $x = self::p( 'foo' );
        static::expectException( ParseException::class );
        $x->asEmailAddressOrEmpty();
    }


    public function testAsEmailAddressOrNull() : void {
        $x = self::p( 'test@example.tst' );
        static::assertSame( 'test@example.tst', $x->asEmailAddressOrNull() );

        $x = self::p( null );
        static::assertNull( $x->asEmailAddressOrNull() );

        $x = self::p( 'foo' );
        static::expectException( ParseException::class );
        $x->asEmailAddressOrNull();
    }


    public function testAsEmailUsername() : void {
        $x = self::p( 'test' );
        static::assertSame( 'test', $x->asEmailUsername() );

        $x = self::p( 'fo@o' );
        static::expectException( ParseException::class );
        $x->asEmailUsername();
    }


    public function testAsEmailUsernameOrEmpty() : void {
        $x = self::p( 'test' );
        static::assertSame( 'test', $x->asEmailUsernameOrEmpty() );

        $x = self::p( '' );
        static::assertNull( $x->asEmailUsernameOrEmpty() );

        $x = self::p( 'fo o' );
        static::expectException( ParseException::class );
        $x->asEmailUsernameOrEmpty();
    }


    public function testAsEmailUsernameOrNull() : void {
        $x = self::p( 'test' );
        static::assertSame( 'test', $x->asEmailUsernameOrNull() );

        $x = self::p( null );
        static::assertNull( $x->asEmailUsernameOrNull() );

        $x = self::p( '<foo' );
        static::expectException( ParseException::class );
        $x->asEmailUsernameOrNull();
    }


    public function testAsExistingDirectory() : void {
        $x = self::p( sys_get_temp_dir() );
        static::assertSame( sys_get_temp_dir(), $x->asExistingDirectory() );

        $x = self::p( 'foo' );
        static::expectException( ParseException::class );
        $x->asExistingDirectory();
    }


    public function testAsExistingDirectoryOrEmpty() : void {
        $x = self::p( sys_get_temp_dir() );
        static::assertSame( sys_get_temp_dir(), $x->asExistingDirectoryOrEmpty() );

        $x = self::p( '' );
        static::assertNull( $x->asExistingDirectoryOrEmpty() );

        $x = self::p( 'foo' );
        static::expectException( ParseException::class );
        $x->asExistingDirectoryOrEmpty();
    }


    public function testAsExistingDirectoryOrNull() : void {
        $x = self::p( sys_get_temp_dir() );
        static::assertSame( sys_get_temp_dir(), $x->asExistingDirectoryOrNull() );

        $x = self::p( null );
        static::assertNull( $x->asExistingDirectoryOrNull() );

        $x = self::p( 'foo' );
        static::expectException( ParseException::class );
        $x->asExistingDirectoryOrNull();
    }


    public function testAsExistingFilename() : void {
        $x = self::p( __FILE__ );
        static::assertSame( __FILE__, $x->asExistingFilename() );

        $x = self::p( 'foo' );
        static::expectException( ParseException::class );
        $x->asExistingFilename();
    }


    public function testAsExistingFilenameOrEmpty() : void {
        $x = self::p( __FILE__ );
        static::assertSame( __FILE__, $x->asExistingFilenameOrEmpty() );

        $x = self::p( '' );
        static::assertNull( $x->asExistingFilenameOrEmpty() );

        $x = self::p( 'foo' );
        static::expectException( ParseException::class );
        $x->asExistingFilenameOrEmpty();
    }


    public function testAsExistingFilenameOrNull() : void {
        $x = self::p( __FILE__ );
        static::assertSame( __FILE__, $x->asExistingFilenameOrNull() );

        $x = self::p( null );
        static::assertNull( $x->asExistingFilenameOrNull() );

        $x = self::p( 'foo' );
        static::expectException( ParseException::class );
        $x->asExistingFilenameOrNull();
    }


    public function testAsFloatForArray() : void {
        $x = self::p( [ 'foo', 'bar' ] );
        static::expectException( TypeError::class );
        $x->asFloat();
    }


    public function testAsFloatForNull() : void {
        $x = self::p( null );
        static::expectException( TypeError::class );
        $x->asFloat();
    }


    public function testAsFloatForStringFloat() : void {

        $x = self::p( '5.5' );
        static::assertIsFloat( $x->asFloat() );
        static::assertEqualsWithDelta( 5.5, $x->asFloat(), 0.0001 );

        $x = self::p( '.01' );
        static::assertIsFloat( $x->asFloat() );
        static::assertEqualsWithDelta( 0.01, $x->asFloat(), 0.0001 );

        $x = self::p( '-.01' );
        static::assertIsFloat( $x->asFloat() );
        static::assertEqualsWithDelta( -0.01, $x->asFloat(), 0.0001 );

        $x = self::p( '5.5.4' );
        static::expectException( ParseException::class );
        $x->asFloat();

    }


    public function testAsFloatForStringInt() : void {

        $x = self::p( '5' );
        static::assertIsFloat( $x->asFloat() );
        static::assertEqualsWithDelta( 5.0, $x->asFloat(), 0.0001 );

    }


    public function testAsFloatForStringText() : void {

        $x = self::p( 'foo' );
        static::expectException( ParseException::class );
        $x->asFloat();

    }


    public function testAsFloatOrEmpty() : void {
        $x = self::p( '5.5' );
        static::assertEqualsWithDelta( 5.5, $x->asFloatOrEmpty(), 0.0001 );

        $x = self::p( '' );
        static::assertNull( $x->asFloatOrEmpty() );

        $x = self::p( 'foo' );
        static::expectException( ParseException::class );
        $x->asFloatOrEmpty();
    }


    public function testAsFloatOrEmptyForArray() : void {
        $x = self::p( [ 'foo', 'bar' ] );
        static::expectException( TypeError::class );
        $x->asFloatOrEmpty();
    }


    public function testAsFloatOrEmptyForNull() : void {
        $x = self::p( null );
        static::expectException( TypeError::class );
        $x->asFloatOrEmpty();
    }


    public function testAsFloatOrNull() : void {

        $x = self::p( '5.5' );
        static::assertEqualsWithDelta( 5.5, $x->asFloatOrNull(), 0.0001 );

        $x = self::p( null );
        static::assertNull( $x->asFloatOrNull() );

    }


    public function testAsFloatRangeClosed() : void {
        $x = self::p( '5.5' );
        static::assertEqualsWithDelta( 5.5, $x->asFloatRangeClosed( 5.4, 5.6 ), 0.0001 );
        static::expectException( ParseException::class );
        $x->asFloatRangeClosed( 5.5, 5.7 );
    }


    public function testAsFloatRangeClosedOrEmpty() : void {
        $x = self::p( '5.5' );
        static::assertEqualsWithDelta( 5.5, $x->asFloatRangeClosedOrEmpty( 5.4, 5.6 ), 0.0001 );

        $x = self::p( '' );
        static::assertNull( $x->asFloatRangeClosedOrEmpty( 5.4, 5.6 ) );

        $x = self::p( 'foo' );
        static::expectException( ParseException::class );
        $x->asFloatRangeClosedOrEmpty( 5.4, 5.6 );

    }


    public function testAsFloatRangeClosedOrNull() : void {
        $x = self::p( '5.5' );
        static::assertEqualsWithDelta( 5.5, $x->asFloatRangeClosedOrNull( 5.4, 5.6 ), 0.0001 );

        $x = self::p( null );
        static::assertNull( $x->asFloatRangeClosedOrNull( 5.4, 5.6 ) );

        $x = self::p( '5.5.4' );
        static::expectException( ParseException::class );
        $x->asFloatRangeClosedOrNull( 5.4, 5.6 );
    }


    public function testAsFloatRangeHalfClosed() : void {
        $x = self::p( '5.5' );
        static::assertEqualsWithDelta( 5.5, $x->asFloatRangeHalfClosed( 5.5, 5.6 ), 0.0001 );

        static::expectException( ParseException::class );
        static::assertEqualsWithDelta( 5.5, $x->asFloatRangeHalfClosed( 5.4, 5.5 ), 0.0001 );
    }


    public function testAsFloatRangeHalfClosedOrEmpty() : void {
        $x = self::p( '5.5' );
        static::assertEqualsWithDelta( 5.5, $x->asFloatRangeHalfClosedOrEmpty( 5.5, 5.6 ), 0.0001 );

        $x = self::p( '' );
        static::assertNull( $x->asFloatRangeHalfClosedOrEmpty( 5.5, 5.6 ) );

        $x = self::p( 'foo' );
        static::expectException( ParseException::class );
        $x->asFloatRangeHalfClosedOrEmpty( 5.5, 5.6 );
    }


    public function testAsFloatRangeHalfClosedOrNull() : void {
        $x = self::p( '5.5' );
        static::assertEqualsWithDelta( 5.5, $x->asFloatRangeHalfClosedOrNull( 5.5, 5.6 ), 0.0001 );

        $x = self::p( null );
        static::assertNull( $x->asFloatRangeHalfClosedOrNull( 5.5, 5.6 ) );

        $x = self::p( '5.5.4' );
        static::expectException( ParseException::class );
        $x->asFloatRangeHalfClosedOrNull( 5.5, 5.6 );
    }


    public function testAsFloatRangeOpen() : void {
        $x = self::p( '5.5' );
        static::assertEqualsWithDelta( 5.5, $x->asFloatRangeOpen( 5.4, 5.5 ), 0.0001 );
        static::assertEqualsWithDelta( 5.5, $x->asFloatRangeOpen( 5.5, 5.6 ), 0.0001 );

        static::expectException( ParseException::class );
        $x->asFloatRangeOpen( 5.6, 5.7 );
    }


    public function testAsFloatRangeOpenOrEmpty() : void {
        $x = self::p( '5.5' );
        static::assertEqualsWithDelta( 5.5, $x->asFloatRangeOpenOrEmpty( 5.4, 5.5 ), 0.0001 );

        $x = self::p( '' );
        static::assertNull( $x->asFloatRangeOpenOrEmpty( 5.4, 5.5 ) );

        $x = self::p( 'foo' );
        static::expectException( ParseException::class );
        $x->asFloatRangeOpenOrEmpty( 5.4, 5.5 );
    }


    public function testAsFloatRangeOpenOrNull() : void {
        $x = self::p( '5.5' );
        static::assertEqualsWithDelta( 5.5, $x->asFloatRangeOpenOrNull( 5.4, 5.5 ), 0.0001 );

        $x = self::p( null );
        static::assertNull( $x->asFloatRangeOpenOrNull( 5.4, 5.5 ) );

        $x = self::p( '5.5.4' );
        static::expectException( ParseException::class );
        $x->asFloatRangeOpenOrNull( 5.4, 5.5 );
    }


    public function testAsHostname() : void {
        $x = self::p( 'www.example.com' );
        static::assertSame( 'www.example.com', $x->asHostname() );

        $x = self::p( 'foo' );
        static::expectException( ParseException::class );
        $x->asHostname();
    }


    public function testAsHostnameForTrailingDot() : void {
        $x = self::p( 'www.example.com.' );
        static::expectException( ParseException::class );
        $x->asHostname();
    }


    public function testAsHostnameOrEmpty() : void {
        $x = self::p( 'www.example.com' );
        static::assertSame( 'www.example.com', $x->asHostnameOrEmpty() );

        $x = self::p( '' );
        static::assertNull( $x->asHostnameOrEmpty() );

        $x = self::p( 'www|example.com' );
        static::expectException( ParseException::class );
        $x->asHostnameOrEmpty();
    }


    public function testAsHostnameOrNull() : void {
        $x = self::p( 'www.example.com' );
        static::assertSame( 'www.example.com', $x->asHostnameOrNull() );

        $x = self::p( null );
        static::assertNull( $x->asHostnameOrNull() );

        $x = self::p( 'www example.com' );
        static::expectException( ParseException::class );
        $x->asHostnameOrNull();
    }


    public function testAsIP() : void {
        $x = self::p( '192.0.2.1' );
        static::assertSame( '192.0.2.1', $x->asIP() );

        $x = self::p( '2001:db8::1' );
        static::assertSame( '2001:db8::1', $x->asIP() );

        $x = self::p( 'foo' );
        static::expectException( ParseException::class );
        $x->asIP();
    }


    public function testAsIPOrEmpty() : void {
        $x = self::p( '192.0.2.2' );
        static::assertSame( '192.0.2.2', $x->asIPOrEmpty() );

        $x = self::p( '' );
        static::assertNull( $x->asIPOrEmpty() );

        $x = self::p( 'foo' );
        static::expectException( ParseException::class );
        $x->asIPOrEmpty();
    }


    public function testAsIPOrNull() : void {
        $x = self::p( '192.0.2.3' );
        static::assertSame( '192.0.2.3', $x->asIPOrNull() );

        $x = self::p( null );
        static::assertNull( $x->asIPOrNull() );

        $x = self::p( 'foo' );
        static::expectException( ParseException::class );
        $x->asIPOrNull();
    }


    public function testAsIPv4() : void {
        $x = self::p( '192.0.2.1' );
        static::assertSame( '192.0.2.1', $x->asIPv4() );

        $x = self::p( '2001:db8::1' );
        static::expectException( ParseException::class );
        $x->asIPv4();
    }


    public function testAsIPv4OrEmpty() : void {
        $x = self::p( '192.0.2.2' );
        static::assertSame( '192.0.2.2', $x->asIPv4OrEmpty() );

        $x = self::p( '' );
        static::assertNull( $x->asIPv4OrEmpty() );

        $x = self::p( 'foo' );
        static::expectException( ParseException::class );
        $x->asIPv4OrEmpty();
    }


    public function testAsIPv4OrNull() : void {
        $x = self::p( '192.0.2.3' );
        static::assertSame( '192.0.2.3', $x->asIPv4OrNull() );

        $x = self::p( null );
        static::assertNull( $x->asIPv4OrNull() );

        $x = self::p( '192.0.2.3.4' );
        static::expectException( ParseException::class );
        $x->asIPv4OrNull();
    }


    public function testAsIPv6() : void {
        $x = self::p( '2001:db8::1' );
        static::assertSame( '2001:db8::1', $x->asIPv6() );

        $x = self::p( '192.0.2.1' );
        static::expectException( ParseException::class );
        $x->asIPv6();
    }


    public function testAsIPv6OrEmpty() : void {
        $x = self::p( '2001:db8::2' );
        static::assertSame( '2001:db8::2', $x->asIPv6OrEmpty() );

        $x = self::p( '' );
        static::assertNull( $x->asIPv6OrEmpty() );

        $x = self::p( 'foo' );
        static::expectException( ParseException::class );
        $x->asIPv6OrEmpty();
    }


    public function testAsIPv6OrNull() : void {
        $x = self::p( '::ffff:0:255.255.255.255' );
        static::assertSame( '::ffff:0:255.255.255.255', $x->asIPv6OrNull() );

        $x = self::p( null );
        static::assertNull( $x->asIPv6OrNull() );

        $x = self::p( '2001:db8::3.4' );
        static::expectException( ParseException::class );
        $x->asIPv6OrNull();
    }


    public function testAsInt() : void {

        $x = self::p( '5' );
        static::assertSame( 5, $x->asInt() );

        $x = self::p( '5.9' );
        static::assertSame( 5, $x->asInt() );

        static::expectException( ParseException::class );
        $x = self::p( 'foo' );
        $x->asInt();

    }


    public function testAsIntForEmpty() : void {
        $x = self::p( '' );
        static::expectException( ParseException::class );
        $x->asInt();
    }


    public function testAsIntOrEmpty() : void {
        $x = self::p( '' );
        static::assertNull( $x->asIntOrEmpty() );

        $x = self::p( '5' );
        static::assertSame( 5, $x->asIntOrEmpty() );

        $x = self::p( '5.9' );
        static::assertSame( 5, $x->asIntOrEmpty() );

        $x = self::p( 'foo' );
        static::expectException( ParseException::class );
        $x->asIntOrEmpty();
    }


    public function testAsIntOrNull() : void {

        $x = self::p( '5' );
        static::assertSame( 5, $x->asIntOrNull() );

        $x = self::p( null );
        static::assertNull( $x->asIntOrNull() );

    }


    public function testAsIntRangeClosed() : void {
        $x = self::p( '5' );
        static::assertSame( 5, $x->asIntRangeClosed( 4, 6 ) );
        static::expectException( ParseException::class );
        $x->asIntRangeClosed( 5, 7 );
    }


    public function testAsIntRangeClosedOrEmpty() : void {
        $x = self::p( '5' );
        static::assertSame( 5, $x->asIntRangeClosedOrEmpty( 4, 6 ) );

        $x = self::p( '' );
        static::assertNull( $x->asIntRangeClosedOrEmpty( 4, 6 ) );

        $x = self::p( 'foo' );
        static::expectException( ParseException::class );
        $x->asIntRangeClosedOrEmpty( 4, 6 );
    }


    public function testAsIntRangeClosedOrNull() : void {
        $x = self::p( '5' );
        static::assertSame( 5, $x->asIntRangeClosedOrNull( 4, 6 ) );

        $x = self::p( null );
        static::assertNull( $x->asIntRangeClosedOrNull( 4, 6 ) );

        $x = self::p( '' );
        static::expectException( ParseException::class );
        $x->asIntRangeClosedOrNull( 4, 6 );
    }


    public function testAsIntRangeHalfClosed() : void {
        $x = self::p( '5' );
        static::assertSame( 5, $x->asIntRangeHalfClosed( 5, 6 ) );
        static::expectException( ParseException::class );
        $x->asIntRangeHalfClosed( 4, 5 );
    }


    public function testAsIntRangeHalfClosedOrEmpty() : void {
        $x = self::p( '5' );
        static::assertSame( 5, $x->asIntRangeHalfClosedOrEmpty( 5, 6 ) );

        $x = self::p( '' );
        static::assertNull( $x->asIntRangeHalfClosedOrEmpty( 5, 6 ) );

        $x = self::p( 'foo' );
        static::expectException( ParseException::class );
        $x->asIntRangeHalfClosedOrEmpty( 5, 6 );
    }


    public function testAsIntRangeHalfClosedOrNull() : void {
        $x = self::p( '5' );
        static::assertSame( 5, $x->asIntRangeHalfClosedOrNull( 5, 6 ) );

        $x = self::p( null );
        static::assertNull( $x->asIntRangeHalfClosedOrNull( 5, 6 ) );

        $x = self::p( '' );
        static::expectException( ParseException::class );
        $x->asIntRangeHalfClosedOrNull( 5, 6 );
    }


    public function testAsIntRangeOpen() : void {
        $x = self::p( '5' );
        static::assertSame( 5, $x->asIntRangeOpen( 4, 6 ) );
        static::assertSame( 5, $x->asIntRangeOpen( 4, 5 ) );
        static::assertSame( 5, $x->asIntRangeOpen( 5, 6 ) );
        static::expectException( ParseException::class );
        $x->asIntRangeOpen( 6, 7 );
    }


    public function testAsIntRangeOpenOrEmpty() : void {
        $x = self::p( '5' );
        static::assertSame( 5, $x->asIntRangeOpenOrEmpty( 5, 6 ) );

        $x = self::p( '' );
        static::assertNull( $x->asIntRangeOpenOrEmpty( 4, 6 ) );

        $x = self::p( 'foo' );
        static::expectException( ParseException::class );
        $x->asIntRangeOpenOrEmpty( 4, 6 );
    }


    public function testAsIntRangeOpenOrNull() : void {
        $x = self::p( '5' );
        static::assertSame( 5, $x->asIntRangeOpenOrNull( 4, 6 ) );

        $x = self::p( null );
        static::assertNull( $x->asIntRangeOpenOrNull( 4, 6 ) );

        $x = self::p( '' );
        static::expectException( ParseException::class );
        $x->asIntRangeOpenOrNull( 4, 6 );
    }


    public function testAsJSON() : void {
        $x = self::p( [ 'foo', 'bar' ] );
        static::assertSame( '["foo","bar"]', $x->asJSON() );
    }


    public function testAsKeyword() : void {
        $x = self::p( 'foo' );
        static::assertSame( 'foo', $x->asKeyword( [ 'foo', 'bar' ] ) );

        $x = self::p( 'baz' );
        static::expectException( ParseException::class );
        $x->asKeyword( [ 'foo', 'bar' ] );
    }


    public function testAsKeywordOrEmpty() : void {
        $x = self::p( 'foo' );
        static::assertSame( 'foo', $x->asKeywordOrEmpty( [ 'foo', 'bar' ] ) );

        $x = self::p( '' );
        static::assertNull( $x->asKeywordOrEmpty( [ 'foo', 'bar' ] ) );

        $x = self::p( 'baz' );
        static::expectException( ParseException::class );
        $x->asKeywordOrEmpty( [ 'foo', 'bar' ] );
    }


    public function testAsKeywordOrNull() : void {
        $x = self::p( 'foo' );
        static::assertSame( 'foo', $x->asKeywordOrNull( [ 'foo', 'bar' ] ) );

        $x = self::p( null );
        static::assertNull( $x->asKeywordOrNull( [ 'foo', 'bar' ] ) );

        $x = self::p( 'baz' );
        static::expectException( ParseException::class );
        $x->asKeywordOrNull( [ 'foo', 'bar' ] );
    }


    public function testAsMap() : void {
        $rMap = [ 'foo' => 'bar', 'baz' => 'qux' ];
        $x = self::p( 'foo' );
        static::assertSame( 'bar', $x->asMap( $rMap ) );

        $x = self::p( 'quux' );
        static::expectException( ParseException::class );
        $x->asMap( $rMap );
    }


    public function testAsMapOrEmpty() : void {
        $rMap = [ 'foo' => 'bar', 'baz' => 'qux' ];
        $x = self::p( 'foo' );
        static::assertSame( 'bar', $x->asMapOrEmpty( $rMap ) );

        $x = self::p( '' );
        static::assertNull( $x->asMapOrEmpty( $rMap ) );

        $x = self::p( '5' );
        static::expectException( ParseException::class );
        $x->asMapOrEmpty( $rMap );
    }


    public function testAsMapOrNull() : void {
        $rMap = [ 'foo' => 'bar', 'baz' => 'qux' ];
        $x = self::p( 'foo' );
        static::assertSame( 'bar', $x->asMapOrNull( $rMap ) );

        $x = self::p( null );
        static::assertNull( $x->asMapOrNull( $rMap ) );

        $x = self::p( '' );
        static::expectException( ParseException::class );
        $x->asMapOrNull( $rMap );
    }


    public function testAsNonexistentFilename() : void {
        $x = self::p( __DIR__ . '/foo' );
        static::assertSame( __DIR__ . '/foo', $x->asNonexistentFilename() );

        $x = self::p( __FILE__ );
        static::expectException( ParseException::class );
        $x->asNonexistentFilename();
    }


    public function testAsNonexistentFilenameOrEmpty() : void {
        $x = self::p( __DIR__ . '/foo' );
        static::assertSame( __DIR__ . '/foo', $x->asNonexistentFilenameOrEmpty() );

        $x = self::p( '' );
        static::assertNull( $x->asNonexistentFilenameOrEmpty() );

        $x = self::p( '/no/such/dir/file.txt' );
        static::expectException( ParseException::class );
        $x->asNonexistentFilenameOrEmpty();
    }


    public function testAsNonexistentFilenameOrNull() : void {
        $x = self::p( __DIR__ . '/foo' );
        static::assertSame( __DIR__ . '/foo', $x->asNonexistentFilenameOrNull() );

        $x = self::p( null );
        static::assertNull( $x->asNonexistentFilenameOrNull() );

        $x = self::p( '' );
        static::expectException( ParseException::class );
        $x->asNonexistentFilenameOrNull();
    }


    public function testAsPositiveFloat() : void {
        $x = self::p( '3.14' );
        static::assertEqualsWithDelta( 3.14, $x->asPositiveFloat(), 0.0001 );

        $x = self::p( '-1.23' );
        static::expectException( ParseException::class );
        $x->asPositiveFloat();
    }


    public function testAsPositiveFloatOrEmpty() : void {
        $x = self::p( '0.1' );
        static::assertEqualsWithDelta( 0.1, $x->asPositiveFloatOrEmpty(), 0.0001 );

        $x = self::p( '' );
        static::assertNull( $x->asPositiveFloatOrEmpty() );

        $x = self::p( '0' );
        static::expectException( ParseException::class );
        $x->asPositiveFloatOrEmpty();
    }


    public function testAsPositiveFloatOrNull() : void {
        $x = self::p( '3.14' );
        static::assertEqualsWithDelta( 3.14, $x->asPositiveFloatOrNull(), 0.0001 );

        $x = self::p( null );
        static::assertNull( $x->asPositiveFloatOrNull() );

        $x = self::p( '' );
        static::expectException( ParseException::class );
        $x->asPositiveFloatOrNull();
    }


    public function testAsPositiveInt() : void {
        $x = self::p( '3' );
        static::assertSame( 3, $x->asPositiveInt() );

        $x = self::p( '-1' );
        static::expectException( ParseException::class );
        $x->asPositiveInt();
    }


    public function testAsPositiveIntOrEmpty() : void {
        $x = self::p( '3' );
        static::assertSame( 3, $x->asPositiveIntOrEmpty() );

        $x = self::p( '' );
        static::assertNull( $x->asPositiveIntOrEmpty() );

        $x = self::p( '0' );
        static::expectException( ParseException::class );
        $x->asPositiveIntOrEmpty();
    }


    public function testAsPositiveIntOrNull() : void {
        $x = self::p( '3' );
        static::assertSame( 3, $x->asPositiveIntOrNull() );

        $x = self::p( null );
        static::assertNull( $x->asPositiveIntOrNull() );

        $x = self::p( '' );
        static::expectException( ParseException::class );
        $x->asPositiveIntOrNull();
    }


    public function testAsRoundedInt() : void {
        $x = self::p( '5.5' );
        static::assertSame( 6, $x->asRoundedInt() );

        $x = self::p( '5.4' );
        static::assertSame( 5, $x->asRoundedInt() );
    }


    public function testAsString() : void {
        $x = self::p( '5.5' );
        static::assertIsString( $x->asString() );
        static::assertSame( '5.5', $x->asString() );
        $x = self::p( '5' );
        static::assertIsString( $x->asString() );
        static::assertSame( '5', $x->asString() );
        static::expectException( TypeError::class );
        $x = self::p( [ 'foo', 'bar' ] );
        $x->asString();
    }


    public function testAsStringOrNull() : void {

        $x = self::p( 'foo' );
        static::assertSame( 'foo', $x->asStringOrNull() );

        $x = self::p( null );
        static::assertNull( $x->asStringOrNull() );

    }


    public function testAsUnsignedFloat() : void {
        $x = self::p( '1.23' );
        static::assertEqualsWithDelta( 1.23, $x->asUnsignedFloat(), 0.0001 );

        $x = self::p( '-1.23' );
        static::expectException( ParseException::class );
        $x->asUnsignedFloat();
    }


    public function testAsUnsignedFloatOrEmpty() : void {
        $x = self::p( '0' );
        static::assertEqualsWithDelta( 0.0, $x->asUnsignedFloatOrEmpty(), 0.0001 );

        $x = self::p( '' );
        static::assertNull( $x->asUnsignedFloatOrEmpty() );

        $x = self::p( 'foo' );
        static::expectException( ParseException::class );
        $x->asUnsignedFloatOrEmpty();
    }


    public function testAsUnsignedFloatOrNull() : void {
        $x = self::p( '1.23' );
        static::assertEqualsWithDelta( 1.23, $x->asUnsignedFloatOrNull(), 0.0001 );

        $x = self::p( null );
        static::assertNull( $x->asUnsignedFloatOrNull() );

        $x = self::p( '' );
        static::expectException( ParseException::class );
        $x->asUnsignedFloatOrNull();
    }


    public function testAsUnsignedInt() : void {
        $x = self::p( '5' );
        static::assertSame( 5, $x->asUnsignedInt() );

        $x = self::p( '-5' );
        static::expectException( ParseException::class );
        $x->asUnsignedInt();
    }


    public function testAsUnsignedIntOrEmpty() : void {
        $x = self::p( '0' );
        static::assertSame( 0, $x->asUnsignedIntOrEmpty() );

        $x = self::p( '' );
        static::assertNull( $x->asUnsignedIntOrEmpty() );

        $x = self::p( 'foo' );
        static::expectException( ParseException::class );
        $x->asUnsignedIntOrEmpty();
    }


    public function testAsUnsignedIntOrNull() : void {
        $x = self::p( '5' );
        static::assertSame( 5, $x->asUnsignedIntOrNull() );

        $x = self::p( null );
        static::assertNull( $x->asUnsignedIntOrNull() );

        $x = self::p( '' );
        static::expectException( ParseException::class );
        $x->asUnsignedIntOrNull();
    }


    public function testConstructWithArray() : void {
        $x = self::p( [ 'foo', 'bar' ] );
        static::assertInstanceOf( Parameter::class, $x );

    }


    public function testConstructWithBool() : void {
        $x = self::p( true );
        static::assertInstanceOf( Parameter::class, $x );
        static::assertSame( 'true', $x->asString() );
        static::assertTrue( $x->asBool() );

        $x = self::p( false );
        static::assertInstanceOf( Parameter::class, $x );
        static::assertSame( 'false', $x->asString() );
        static::assertFalse( $x->asBool() );
    }


    public function testConstructWithFloat() : void {
        $x = self::p( 5.5 );
        static::assertInstanceOf( Parameter::class, $x );
        static::assertSame( '5.5', $x->asString() );
        static::assertEqualsWithDelta( 5.5, $x->asFloat(), 0.0001 );
    }


    public function testConstructWithInt() : void {
        $x = self::p( 5 );
        static::assertInstanceOf( Parameter::class, $x );
        self::assertEquals( '5', $x->asString() );
        self::assertSame( 5, $x->asInt() );
    }


    public function testConstructWithNestedArray() : void {
        $x = self::p( [
            [ 'foo', 'bar' ],
            [ 'baz', 'qux' ],
        ] );
        static::assertInstanceOf( Parameter::class, $x );
        static::assertTrue( $x->isArray() );
        static::assertTrue( isset( $x[ 0 ] ) );
        static::assertTrue( $x[ 0 ]->isArray() );
        static::assertTrue( isset( $x[ 0 ][ 0 ] ) );
        static::assertSame( 'foo', $x[ 0 ][ 0 ]->asString() );

        $x = self::p( [
            [ 'foo', 'bar' ],
            [ 'baz', 'qux' ],
        ], nuAllowArrayDepth: 1 );
        static::expectException( InvalidArgumentException::class );
        $y = $x[ 0 ];
        unset( $y );
    }


    public function testConstructWithNull() : void {
        $x = self::p( null );
        static::assertInstanceOf( Parameter::class, $x );
        static::expectException( InvalidArgumentException::class );
        $x = self::p( null, bAllowNull: false );
        unset( $x );
    }


    public function testConstructWithParameter() : void {
        $x = self::p( 'foo' );
        static::assertSame( 'foo', $x->asString() );
        $y = self::p( $x );
        static::assertSame( 'foo', $y->asString() );
    }


    public function testConstructWithString() : void {
        $x = self::p( 'foo' );
        static::assertInstanceOf( Parameter::class, $x );
    }


    public function testForEach() : void {
        $x = self::p( [ 'foo', 'bar' ] );
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
        $x = self::p( '5' );
        static::assertSame( '5', $x->getValue() );

        $x = self::p( null );
        static::assertNull( $x->getValue() );

        $x = self::p( [ 'foo', 'bar' ] );
        static::assertSame( [ 'foo', 'bar' ], $x->getValue() );

    }


    public function testHas() : void {
        $x = self::p( [ 'foo', 'bar' ] );
        static::assertTrue( $x->offsetExists( 0 ) );
        static::assertTrue( $x->offsetExists( 1 ) );
        static::assertFalse( $x->offsetExists( 2 ) );
        static::assertFalse( $x->offsetExists( 'foo' ) );

        $x = self::p( [ 'foo' => 'bar', 'baz' => 'qux' ] );
        static::assertTrue( $x->offsetExists( 'foo' ) );
        static::assertTrue( $x->offsetExists( 'baz' ) );
        static::assertFalse( $x->offsetExists( 'qux' ) );
        static::assertFalse( $x->offsetExists( 0 ) );

        $x = self::p( 'foo' );
        static::expectException( TypeError::class );
        $x->has( 0 );
    }


    public function testIndexOrDefault() : void {

        $x = self::p( [ 'foo', 'bar' ] );
        static::assertSame( 'foo', $x->indexOrDefault( 0, 'baz' )->asString() );
        static::assertSame( 'baz', $x->indexOrDefault( 2, 'baz' )->asString() );

    }


    public function testIndexOrNull() : void {
        $x = self::p( [ 'foo', 'bar' ] );
        static::assertSame( 'foo', $x->indexOrNull( 0 )->asString() );
        static::assertNull( $x->indexOrNull( 2 ) );
    }


    public function testIsArray() : void {
        $x = self::p( '5.5' );
        static::assertFalse( $x->isArray() );
        $x = self::p( [ 'foo', 'bar' ] );
        static::assertTrue( $x->isArray() );
    }


    public function testIsEmpty() : void {
        $x = self::p( '5' );
        static::assertFalse( $x->isEmpty() );

        $x = self::p( [ 'foo', 'bar' ] );
        static::assertFalse( $x->isEmpty() );

        $x = self::p( null );
        static::assertTrue( $x->isEmpty() );

        $x = self::p( '' );
        static::assertTrue( $x->isEmpty() );

        $x = self::p( [] );
        static::assertTrue( $x->isEmpty() );

        $x = self::p( 'yup' );
        $y = self::p( $x );
        static::assertFalse( $y->isEmpty() );

        $x = self::p( null );
        $y = self::p( $x );
        static::assertTrue( $y->isEmpty() );
    }


    public function testIsNull() : void {

        $x = self::p( '5' );
        static::assertFalse( $x->isNull() );

        $x = self::p( [ 'foo', 'bar' ] );
        static::assertFalse( $x->isNull() );

        $x = self::p( null );
        static::assertTrue( $x->isNull() );

    }


    public function testIsSet() : void {
        $x = self::p( '5' );
        static::assertTrue( $x->isSet() );
    }


    public function testIsString() : void {
        $x = self::p( 'foo' );
        static::assertTrue( $x->isString() );

        $x = self::p( [ 'foo', 'bar' ] );
        static::assertFalse( $x->isString() );

        $x = self::p( null );
        static::assertFalse( $x->isString() );

    }


    public function testJsonSerialize() : void {
        $x = self::p( 'foo' );
        self::assertSame( '"foo"', json_encode( $x ) );

        $x = self::p( [ 'foo', 'bar' ] );
        self::assertSame( '["foo","bar"]', json_encode( $x ) );

        $x = self::p( null );
        self::assertSame( 'null', json_encode( $x ) );
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
        $x = self::p( 'foo' );
        $y = $x->new( 'bar' );
        static::assertSame( 'bar', $y->asString() );
        static::assertSame( $x::class, $y::class );
    }


    public function testOffsetExists() : void {

        $x = self::p( [ 'foo' => 'bar', 'baz' => 'qux' ] );
        static::assertTrue( isset( $x[ 'foo' ] ) );
        static::assertFalse( isset( $x[ 'qux' ] ) );

        static::expectException( InvalidArgumentException::class );
        $y = isset( $x[ null ] );
        unset( $y );

    }


    public function testOffsetGet() : void {
        $x = self::p( [ 'foo', 'bar' ] );
        static::assertTrue( isset( $x[ 0 ] ) );
        static::assertSame( 'foo', $x[ 0 ]->asString() );
        static::assertSame( 'bar', $x[ 1 ]->asString() );
    }


    public function testOffsetGetForInvalidArgument() : void {
        $x = self::p( [ 'foo', 'bar' ] );
        static::expectException( InvalidArgumentException::class );
        $y = $x[ null ];
        unset( $y );
    }


    public function testOffsetGetForOutOfBounds() : void {
        $x = self::p( [ 'foo', 'bar' ] );
        static::expectException( OutOfBoundsException::class );
        $y = $x[ 2 ];
        unset( $y );
    }


    public function testOffsetGetWithString() : void {
        $x = self::p( 'foo' );
        static::expectException( TypeError::class );
        $y = $x[ 0 ];
        unset( $y );
    }


    public function testOffsetSet() : void {
        $x = self::p( [ 'foo', 'bar' ] );
        static::expectException( LogicException::class );
        $x[ 0 ] = 'baz';
    }


    public function testOffsetUnset() : void {
        $x = self::p( [ 'foo', 'bar' ] );
        static::expectException( LogicException::class );
        unset( $x[ 0 ] );
    }


    public function testRoundedFloat() : void {
        $x = self::p( '5.5' );
        static::assertEqualsWithDelta( 5.5, $x->asRoundedFloat( 1 ), 0.0001 );

        $x = self::p( '5.4' );
        static::assertEqualsWithDelta( 5.4, $x->asRoundedFloat( 1 ), 0.0001 );

        $x = self::p( '1.2345' );
        static::assertEqualsWithDelta( 1.0, $x->asRoundedFloat(), 0.0001 );
        static::assertEqualsWithDelta( 1.2, $x->asRoundedFloat( 1 ), 0.0001 );
        static::assertEqualsWithDelta( 1.23, $x->asRoundedFloat( 2 ), 0.0001 );
        static::assertEqualsWithDelta( 1.235, $x->asRoundedFloat( 3 ), 0.0001 );
        static::assertEqualsWithDelta( 1.2345, $x->asRoundedFloat( 4 ), 0.0001 );
        static::assertEqualsWithDelta( 1.2345, $x->asRoundedFloat( 5 ), 0.0001 );

        $x = self::p( 'foo' );
        static::expectException( ParseException::class );
        $x->asRoundedFloat();
    }


    public function testRoundedFloatForEmpty() : void {
        $x = self::p( '' );
        static::expectException( ParseException::class );
        $x->asRoundedFloat();
    }


    public function testRoundedFloatOrEmpty() : void {
        $x = self::p( '5.5' );
        static::assertEqualsWithDelta( 5.5, $x->asRoundedFloatOrEmpty( 1 ), 0.0001 );

        $x = self::p( '' );
        static::assertNull( $x->asRoundedFloatOrEmpty() );

        $x = self::p( 'foo' );
        static::expectException( ParseException::class );
        $x->asRoundedFloatOrEmpty();
    }


    public function testRoundedFloatOrEmptyForNull() : void {
        $x = self::p( null );
        static::expectException( TypeError::class );
        $x->asRoundedFloatOrEmpty();
    }


    public function testRoundedFloatOrNull() : void {
        $x = self::p( '5.5' );
        static::assertEqualsWithDelta( 5.5, $x->asRoundedFloatOrNull( 1 ), 0.0001 );

        $x = self::p( '5.4' );
        static::assertEqualsWithDelta( 5.4, $x->asRoundedFloatOrNull( 1 ), 0.0001 );

        $x = self::p( null );
        static::assertNull( $x->asRoundedFloatOrNull() );
    }


    public function testRoundedInt() : void {
        $x = self::p( '5.5' );
        static::assertSame( 6, $x->asRoundedInt() );
        static::assertSame( 5, $x->asRoundedInt( 1 ) );


        $x = self::p( '5.4' );
        static::assertSame( 5, $x->asRoundedInt() );

        $x = self::p( '153.456' );
        static::assertSame( 153, $x->asRoundedInt() );
        static::assertSame( 150, $x->asRoundedInt( -1 ) );
        static::assertSame( 200, $x->asRoundedInt( -2 ) );

        $x = self::p( 'foo' );
        static::expectException( ParseException::class );
        $x->asRoundedInt();
    }


    public function testRoundedIntOrEmpty() : void {
        $x = self::p( '5.5' );
        static::assertSame( 6, $x->asRoundedIntOrEmpty() );

        $x = self::p( '' );
        static::assertNull( $x->asRoundedIntOrEmpty() );

        $x = self::p( 'foo' );
        static::expectException( ParseException::class );
        $x->asRoundedIntOrEmpty();
    }


    public function testRoundedIntOrNull() : void {
        $x = self::p( '5.5' );
        static::assertSame( 6, $x->asRoundedIntOrNull() );

        $x = self::p( '5.4' );
        static::assertSame( 5, $x->asRoundedIntOrNull() );

        $x = self::p( null );
        static::assertNull( $x->asRoundedIntOrNull() );
    }


    public function testToString() : void {
        $x = self::p( 'foo' );
        static::assertSame( 'foo', (string) $x );

        static::assertSame( 'xfoox', 'x' . $x . 'x' );

        $x = self::p( [ 'foo', 'bar' ] );
        static::expectException( TypeError::class );
        $y = (string) $x;
        unset( $y );
    }


}
