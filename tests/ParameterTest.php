<?php


declare( strict_types = 1 );


use JDWX\Param\IParameter;
use JDWX\Param\Parameter;
use JDWX\Param\ParseException;
use PHPUnit\Framework\TestCase;


require_once __DIR__ . '/MyTestParameter.php';


final class ParameterTest extends TestCase {


    public static function p( mixed $x, bool $bAllowNull = true,
                              ?int  $nuAllowArrayDepth = null ) : IParameter {
        return new Parameter( $x, $bAllowNull, $nuAllowArrayDepth );
    }


    public function testAsArrayOrNull() : void {

        $x = self::p( [ 'foo', 'bar' ] );
        self::assertSame( [ 'foo', 'bar' ], $x->asArrayOrNull() );

        $x = self::p( null );
        self::assertNull( $x->asArrayOrNull() );

        $x = self::p( 'foo' );
        self::expectException( TypeError::class );
        $x->asArrayOrNull();

    }


    public function testAsArrayOrString() : void {
        $x = self::p( [ 'foo', 'bar' ] );
        self::assertSame( [ 'foo', 'bar' ], $x->asArrayOrString() );

        $x = self::p( 'foo' );
        self::assertSame( 'foo', $x->asArrayOrString() );

        $x = self::p( null );
        self::expectException( TypeError::class );
        $x->asArrayOrString();
    }


    public function testAsArrayRecursive() : void {
        $x = self::p( [ 'foo', [ 'bar', self::p( 'baz' ), self::p( [ 'qux', self::p( 'quux' ) ] ) ] ] );
        self::assertSame( [ 'foo', [ 'bar', 'baz', [ 'qux', 'quux' ] ] ], $x->asArrayRecursive() );

        self::expectException( TypeError::class );
        self::p( 'foo' )->asArrayRecursive();
    }


    public function testAsArrayRecursiveOrNull() : void {
        $x = self::p( [ 'foo', [ 'bar', self::p( 'baz' ), self::p( [ 'qux', self::p( 'quux' ) ] ) ] ] );
        self::assertSame( [ 'foo', [ 'bar', 'baz', [ 'qux', 'quux' ] ] ], $x->asArrayRecursiveOrNull() );

        $x = self::p( null );
        self::assertNull( $x->asArrayRecursiveOrNull() );

        $x = self::p( 'foo' );
        self::expectException( TypeError::class );
        $x->asArrayRecursiveOrNull();
    }


    public function testAsArrayRecursiveOrString() : void {
        $x = self::p( [ 'foo', [ 'bar', self::p( 'baz' ), self::p( [ 'qux', self::p( 'quux' ) ] ) ] ] );
        self::assertSame( [ 'foo', [ 'bar', 'baz', [ 'qux', 'quux' ] ] ], $x->asArrayRecursiveOrString() );

        $x = self::p( 'foo' );
        self::assertSame( 'foo', $x->asArrayRecursiveOrString() );

        $x = self::p( null );
        self::expectException( TypeError::class );
        $x->asArrayRecursiveOrString();
    }


    public function testAsArrayWithArray() : void {
        $x = self::p( [ 'foo', 'bar' ] );
        self::assertSame( [ 'foo', 'bar' ], $x->asArray() );
    }


    public function testAsArrayWithNull() : void {
        $x = self::p( null );
        self::expectException( TypeError::class );
        $x->asArray();
    }


    public function testAsArrayWithString() : void {
        $x = self::p( '5' );
        self::expectException( TypeError::class );
        $x->asArray();
    }


    public function testAsBool() : void {

        $x = self::p( 'true' );
        self::assertTrue( $x->asBool() );

        $x = self::p( 'false' );
        self::assertFalse( $x->asBool() );

        $x = self::p( 'yes' );
        self::assertTrue( $x->asBool() );

        $x = self::p( 'no' );
        self::assertFalse( $x->asBool() );

        $x = self::p( 'on' );
        self::assertTrue( $x->asBool() );

        $x = self::p( 'off' );
        self::assertFalse( $x->asBool() );

        $x = self::p( '1' );
        self::assertTrue( $x->asBool() );

        $x = self::p( '0' );
        self::assertFalse( $x->asBool() );

        $x = self::p( null );
        self::assertFalse( $x->asBool() );

        $x = self::p( 'foo' );
        self::expectException( ParseException::class );
        $x->asBool();

    }


    public function testAsBoolOrNull() : void {
        $x = self::p( 'false' );
        self::assertFalse( $x->asBoolOrNull() );

        $x = self::p( null );
        self::assertNull( $x->asBoolOrNull() );
    }


    public function testAsConstant() : void {
        $x = self::p( 'foo' );
        self::assertSame( 'foo', $x->asConstant( 'foo' ) );

        $x = self::p( 'foo' );
        self::expectException( ParseException::class );
        $x->asConstant( 'bar' );
    }


    public function testAsConstantForArray() : void {
        $x = self::p( [ 'foo', 'bar' ] );
        self::expectException( TypeError::class );
        $x->asConstant( 'foo' );
    }


    public function testAsConstantForNull() : void {
        $x = self::p( null );
        self::expectException( TypeError::class );
        $x->asConstant( 'foo' );
    }


    public function testAsCurrency() : void {
        $x = self::p( '5.5' );
        self::assertSame( 550, $x->asCurrency() );

        $x = self::p( '5' );
        self::assertSame( 500, $x->asCurrency() );

        $x = self::p( '5.000000000001' );
        self::assertSame( 500, $x->asCurrency() );

        $x = self::p( '4.999999999999' );
        self::assertSame( 500, $x->asCurrency() );

        $x = self::p( 'foo' );
        self::expectException( ParseException::class );
        $x->asCurrency();
    }


    public function testAsCurrencyForEmpty() : void {
        $x = self::p( '' );
        self::expectException( ParseException::class );
        $x->asCurrency();
    }


    public function testAsCurrencyOrEmpty() : void {
        $x = self::p( '5.5' );
        self::assertSame( 550, $x->asCurrencyOrEmpty() );

        $x = self::p( '' );
        self::assertNull( $x->asCurrencyOrEmpty() );

        $x = self::p( 'foo' );
        self::expectException( ParseException::class );
        $x->asCurrencyOrEmpty();
    }


    public function testAsCurrencyOrNull() : void {
        $x = self::p( '5.5' );
        self::assertSame( 550, $x->asCurrencyOrNull() );

        $x = self::p( null );
        self::assertNull( $x->asCurrencyOrNull() );
    }


    public function testAsDate() : void {
        $x = self::p( '2024-01-25 12:34:56' );
        self::assertSame( '2024-01-25', $x->asDate() );

        $x = self::p( 'March 2, 2025 3:53 PM' );
        self::assertSame( '2025-03-02', $x->asDate() );

        $x = self::p( '2024-01-26 + 2 days' );
        self::assertSame( '2024-01-28', $x->asDate() );

        $x = self::p( 'foo' );
        self::expectException( ParseException::class );
        $x->asDate();
    }


    public function testAsDateOrNull() : void {
        $x = self::p( '2024-01-25 12:34:56' );
        self::assertSame( '2024-01-25', $x->asDateOrNull() );

        $x = self::p( null );
        self::assertNull( $x->asDateOrNull() );
    }


    public function testAsDateTime() : void {
        $x = self::p( '2024-01-25 12:34:56' );
        self::assertSame( '2024-01-25 12:34:56', $x->asDateTime() );

        $x = self::p( 'March 2, 2025 3:53 PM' );
        self::assertSame( '2025-03-02 15:53:00', $x->asDateTime() );

        $x = self::p( '2024-01-26 + 2 days' );
        self::assertSame( '2024-01-28 00:00:00', $x->asDateTime() );

        $x = self::p( 'foo' );
        self::expectException( ParseException::class );
        $x->asDateTime();
    }


    public function testAsDateTimeOrNull() : void {
        $x = self::p( '2024-01-25 12:34:56' );
        self::assertSame( '2024-01-25 12:34:56', $x->asDateTimeOrNull() );

        $x = self::p( null );
        self::assertNull( $x->asDateTimeOrNull() );
    }


    public function testAsEmailAddress() : void {
        $x = self::p( 'test@example.tst' );
        self::assertSame( 'test@example.tst', $x->asEmailAddress() );

        $x = self::p( 'foo' );
        self::expectException( ParseException::class );
        $x->asEmailAddress();
    }


    public function testAsEmailAddressOrEmpty() : void {
        $x = self::p( 'test@example.tst' );
        self::assertSame( 'test@example.tst', $x->asEmailAddressOrEmpty() );

        $x = self::p( '' );
        self::assertNull( $x->asEmailAddressOrEmpty() );

        $x = self::p( 'foo' );
        self::expectException( ParseException::class );
        $x->asEmailAddressOrEmpty();
    }


    public function testAsEmailAddressOrNull() : void {
        $x = self::p( 'test@example.tst' );
        self::assertSame( 'test@example.tst', $x->asEmailAddressOrNull() );

        $x = self::p( null );
        self::assertNull( $x->asEmailAddressOrNull() );

        $x = self::p( 'foo' );
        self::expectException( ParseException::class );
        $x->asEmailAddressOrNull();
    }


    public function testAsEmailUsername() : void {
        $x = self::p( 'test' );
        self::assertSame( 'test', $x->asEmailUsername() );

        $x = self::p( 'fo@o' );
        self::expectException( ParseException::class );
        $x->asEmailUsername();
    }


    public function testAsEmailUsernameOrEmpty() : void {
        $x = self::p( 'test' );
        self::assertSame( 'test', $x->asEmailUsernameOrEmpty() );

        $x = self::p( '' );
        self::assertNull( $x->asEmailUsernameOrEmpty() );

        $x = self::p( 'fo o' );
        self::expectException( ParseException::class );
        $x->asEmailUsernameOrEmpty();
    }


    public function testAsEmailUsernameOrNull() : void {
        $x = self::p( 'test' );
        self::assertSame( 'test', $x->asEmailUsernameOrNull() );

        $x = self::p( null );
        self::assertNull( $x->asEmailUsernameOrNull() );

        $x = self::p( '<foo' );
        self::expectException( ParseException::class );
        $x->asEmailUsernameOrNull();
    }


    public function testAsExistingDirectory() : void {
        $x = self::p( sys_get_temp_dir() );
        self::assertSame( sys_get_temp_dir(), $x->asExistingDirectory() );

        $x = self::p( 'foo' );
        self::expectException( ParseException::class );
        $x->asExistingDirectory();
    }


    public function testAsExistingDirectoryOrEmpty() : void {
        $x = self::p( sys_get_temp_dir() );
        self::assertSame( sys_get_temp_dir(), $x->asExistingDirectoryOrEmpty() );

        $x = self::p( '' );
        self::assertNull( $x->asExistingDirectoryOrEmpty() );

        $x = self::p( 'foo' );
        self::expectException( ParseException::class );
        $x->asExistingDirectoryOrEmpty();
    }


    public function testAsExistingDirectoryOrNull() : void {
        $x = self::p( sys_get_temp_dir() );
        self::assertSame( sys_get_temp_dir(), $x->asExistingDirectoryOrNull() );

        $x = self::p( null );
        self::assertNull( $x->asExistingDirectoryOrNull() );

        $x = self::p( 'foo' );
        self::expectException( ParseException::class );
        $x->asExistingDirectoryOrNull();
    }


    public function testAsExistingFilename() : void {
        $x = self::p( __FILE__ );
        self::assertSame( __FILE__, $x->asExistingFilename() );

        $x = self::p( 'foo' );
        self::expectException( ParseException::class );
        $x->asExistingFilename();
    }


    public function testAsExistingFilenameOrEmpty() : void {
        $x = self::p( __FILE__ );
        self::assertSame( __FILE__, $x->asExistingFilenameOrEmpty() );

        $x = self::p( '' );
        self::assertNull( $x->asExistingFilenameOrEmpty() );

        $x = self::p( 'foo' );
        self::expectException( ParseException::class );
        $x->asExistingFilenameOrEmpty();
    }


    public function testAsExistingFilenameOrNull() : void {
        $x = self::p( __FILE__ );
        self::assertSame( __FILE__, $x->asExistingFilenameOrNull() );

        $x = self::p( null );
        self::assertNull( $x->asExistingFilenameOrNull() );

        $x = self::p( 'foo' );
        self::expectException( ParseException::class );
        $x->asExistingFilenameOrNull();
    }


    public function testAsFloatForArray() : void {
        $x = self::p( [ 'foo', 'bar' ] );
        self::expectException( TypeError::class );
        $x->asFloat();
    }


    public function testAsFloatForNull() : void {
        $x = self::p( null );
        self::expectException( TypeError::class );
        $x->asFloat();
    }


    public function testAsFloatForStringFloat() : void {

        $x = self::p( '5.5' );
        /** @phpstan-ignore staticMethod.alreadyNarrowedType */
        self::assertIsFloat( $x->asFloat() );
        self::assertEqualsWithDelta( 5.5, $x->asFloat(), 0.0001 );

        $x = self::p( '.01' );
        /** @phpstan-ignore staticMethod.alreadyNarrowedType */
        self::assertIsFloat( $x->asFloat() );
        self::assertEqualsWithDelta( 0.01, $x->asFloat(), 0.0001 );

        $x = self::p( '-.01' );
        /** @phpstan-ignore staticMethod.alreadyNarrowedType */
        self::assertIsFloat( $x->asFloat() );
        self::assertEqualsWithDelta( -0.01, $x->asFloat(), 0.0001 );

        $x = self::p( '5.5.4' );
        self::expectException( ParseException::class );
        $x->asFloat();

    }


    public function testAsFloatForStringInt() : void {

        $x = self::p( '5' );
        /** @phpstan-ignore staticMethod.alreadyNarrowedType */
        self::assertIsFloat( $x->asFloat() );
        self::assertEqualsWithDelta( 5.0, $x->asFloat(), 0.0001 );

    }


    public function testAsFloatForStringText() : void {

        $x = self::p( 'foo' );
        self::expectException( ParseException::class );
        $x->asFloat();

    }


    public function testAsFloatOrEmpty() : void {
        $x = self::p( '5.5' );
        self::assertEqualsWithDelta( 5.5, $x->asFloatOrEmpty(), 0.0001 );

        $x = self::p( '' );
        self::assertNull( $x->asFloatOrEmpty() );

        $x = self::p( 'foo' );
        self::expectException( ParseException::class );
        $x->asFloatOrEmpty();
    }


    public function testAsFloatOrEmptyForArray() : void {
        $x = self::p( [ 'foo', 'bar' ] );
        self::expectException( TypeError::class );
        $x->asFloatOrEmpty();
    }


    public function testAsFloatOrEmptyForNull() : void {
        $x = self::p( null );
        self::expectException( TypeError::class );
        $x->asFloatOrEmpty();
    }


    public function testAsFloatOrNull() : void {

        $x = self::p( '5.5' );
        self::assertEqualsWithDelta( 5.5, $x->asFloatOrNull(), 0.0001 );

        $x = self::p( null );
        self::assertNull( $x->asFloatOrNull() );

    }


    public function testAsFloatRangeClosed() : void {
        $x = self::p( '5.5' );
        self::assertEqualsWithDelta( 5.5, $x->asFloatRangeClosed( 5.4, 5.6 ), 0.0001 );
        self::expectException( ParseException::class );
        $x->asFloatRangeClosed( 5.5, 5.7 );
    }


    public function testAsFloatRangeClosedOrEmpty() : void {
        $x = self::p( '5.5' );
        self::assertEqualsWithDelta( 5.5, $x->asFloatRangeClosedOrEmpty( 5.4, 5.6 ), 0.0001 );

        $x = self::p( '' );
        self::assertNull( $x->asFloatRangeClosedOrEmpty( 5.4, 5.6 ) );

        $x = self::p( 'foo' );
        self::expectException( ParseException::class );
        $x->asFloatRangeClosedOrEmpty( 5.4, 5.6 );

    }


    public function testAsFloatRangeClosedOrNull() : void {
        $x = self::p( '5.5' );
        self::assertEqualsWithDelta( 5.5, $x->asFloatRangeClosedOrNull( 5.4, 5.6 ), 0.0001 );

        $x = self::p( null );
        self::assertNull( $x->asFloatRangeClosedOrNull( 5.4, 5.6 ) );

        $x = self::p( '5.5.4' );
        self::expectException( ParseException::class );
        $x->asFloatRangeClosedOrNull( 5.4, 5.6 );
    }


    public function testAsFloatRangeHalfClosed() : void {
        $x = self::p( '5.5' );
        self::assertEqualsWithDelta( 5.5, $x->asFloatRangeHalfClosed( 5.5, 5.6 ), 0.0001 );

        self::expectException( ParseException::class );
        self::assertEqualsWithDelta( 5.5, $x->asFloatRangeHalfClosed( 5.4, 5.5 ), 0.0001 );
    }


    public function testAsFloatRangeHalfClosedOrEmpty() : void {
        $x = self::p( '5.5' );
        self::assertEqualsWithDelta( 5.5, $x->asFloatRangeHalfClosedOrEmpty( 5.5, 5.6 ), 0.0001 );

        $x = self::p( '' );
        self::assertNull( $x->asFloatRangeHalfClosedOrEmpty( 5.5, 5.6 ) );

        $x = self::p( 'foo' );
        self::expectException( ParseException::class );
        $x->asFloatRangeHalfClosedOrEmpty( 5.5, 5.6 );
    }


    public function testAsFloatRangeHalfClosedOrNull() : void {
        $x = self::p( '5.5' );
        self::assertEqualsWithDelta( 5.5, $x->asFloatRangeHalfClosedOrNull( 5.5, 5.6 ), 0.0001 );

        $x = self::p( null );
        self::assertNull( $x->asFloatRangeHalfClosedOrNull( 5.5, 5.6 ) );

        $x = self::p( '5.5.4' );
        self::expectException( ParseException::class );
        $x->asFloatRangeHalfClosedOrNull( 5.5, 5.6 );
    }


    public function testAsFloatRangeOpen() : void {
        $x = self::p( '5.5' );
        self::assertEqualsWithDelta( 5.5, $x->asFloatRangeOpen( 5.4, 5.5 ), 0.0001 );
        self::assertEqualsWithDelta( 5.5, $x->asFloatRangeOpen( 5.5, 5.6 ), 0.0001 );

        self::expectException( ParseException::class );
        $x->asFloatRangeOpen( 5.6, 5.7 );
    }


    public function testAsFloatRangeOpenOrEmpty() : void {
        $x = self::p( '5.5' );
        self::assertEqualsWithDelta( 5.5, $x->asFloatRangeOpenOrEmpty( 5.4, 5.5 ), 0.0001 );

        $x = self::p( '' );
        self::assertNull( $x->asFloatRangeOpenOrEmpty( 5.4, 5.5 ) );

        $x = self::p( 'foo' );
        self::expectException( ParseException::class );
        $x->asFloatRangeOpenOrEmpty( 5.4, 5.5 );
    }


    public function testAsFloatRangeOpenOrNull() : void {
        $x = self::p( '5.5' );
        self::assertEqualsWithDelta( 5.5, $x->asFloatRangeOpenOrNull( 5.4, 5.5 ), 0.0001 );

        $x = self::p( null );
        self::assertNull( $x->asFloatRangeOpenOrNull( 5.4, 5.5 ) );

        $x = self::p( '5.5.4' );
        self::expectException( ParseException::class );
        $x->asFloatRangeOpenOrNull( 5.4, 5.5 );
    }


    public function testAsHostname() : void {
        $x = self::p( 'www.example.com' );
        self::assertSame( 'www.example.com', $x->asHostname() );

        $x = self::p( 'foo' );
        self::expectException( ParseException::class );
        $x->asHostname();
    }


    public function testAsHostnameForTrailingDot() : void {
        $x = self::p( 'www.example.com.' );
        self::expectException( ParseException::class );
        $x->asHostname();
    }


    public function testAsHostnameOrEmpty() : void {
        $x = self::p( 'www.example.com' );
        self::assertSame( 'www.example.com', $x->asHostnameOrEmpty() );

        $x = self::p( '' );
        self::assertNull( $x->asHostnameOrEmpty() );

        $x = self::p( 'www|example.com' );
        self::expectException( ParseException::class );
        $x->asHostnameOrEmpty();
    }


    public function testAsHostnameOrNull() : void {
        $x = self::p( 'www.example.com' );
        self::assertSame( 'www.example.com', $x->asHostnameOrNull() );

        $x = self::p( null );
        self::assertNull( $x->asHostnameOrNull() );

        $x = self::p( 'www example.com' );
        self::expectException( ParseException::class );
        $x->asHostnameOrNull();
    }


    public function testAsIP() : void {
        $x = self::p( '192.0.2.1' );
        self::assertSame( '192.0.2.1', $x->asIP() );

        $x = self::p( '2001:db8::1' );
        self::assertSame( '2001:db8::1', $x->asIP() );

        $x = self::p( 'foo' );
        self::expectException( ParseException::class );
        $x->asIP();
    }


    public function testAsIPOrEmpty() : void {
        $x = self::p( '192.0.2.2' );
        self::assertSame( '192.0.2.2', $x->asIPOrEmpty() );

        $x = self::p( '' );
        self::assertNull( $x->asIPOrEmpty() );

        $x = self::p( 'foo' );
        self::expectException( ParseException::class );
        $x->asIPOrEmpty();
    }


    public function testAsIPOrNull() : void {
        $x = self::p( '192.0.2.3' );
        self::assertSame( '192.0.2.3', $x->asIPOrNull() );

        $x = self::p( null );
        self::assertNull( $x->asIPOrNull() );

        $x = self::p( 'foo' );
        self::expectException( ParseException::class );
        $x->asIPOrNull();
    }


    public function testAsIPv4() : void {
        $x = self::p( '192.0.2.1' );
        self::assertSame( '192.0.2.1', $x->asIPv4() );

        $x = self::p( '2001:db8::1' );
        self::expectException( ParseException::class );
        $x->asIPv4();
    }


    public function testAsIPv4OrEmpty() : void {
        $x = self::p( '192.0.2.2' );
        self::assertSame( '192.0.2.2', $x->asIPv4OrEmpty() );

        $x = self::p( '' );
        self::assertNull( $x->asIPv4OrEmpty() );

        $x = self::p( 'foo' );
        self::expectException( ParseException::class );
        $x->asIPv4OrEmpty();
    }


    public function testAsIPv4OrNull() : void {
        $x = self::p( '192.0.2.3' );
        self::assertSame( '192.0.2.3', $x->asIPv4OrNull() );

        $x = self::p( null );
        self::assertNull( $x->asIPv4OrNull() );

        $x = self::p( '192.0.2.3.4' );
        self::expectException( ParseException::class );
        $x->asIPv4OrNull();
    }


    public function testAsIPv6() : void {
        $x = self::p( '2001:db8::1' );
        self::assertSame( '2001:db8::1', $x->asIPv6() );

        $x = self::p( '192.0.2.1' );
        self::expectException( ParseException::class );
        $x->asIPv6();
    }


    public function testAsIPv6OrEmpty() : void {
        $x = self::p( '2001:db8::2' );
        self::assertSame( '2001:db8::2', $x->asIPv6OrEmpty() );

        $x = self::p( '' );
        self::assertNull( $x->asIPv6OrEmpty() );

        $x = self::p( 'foo' );
        self::expectException( ParseException::class );
        $x->asIPv6OrEmpty();
    }


    public function testAsIPv6OrNull() : void {
        $x = self::p( '::ffff:0:255.255.255.255' );
        self::assertSame( '::ffff:0:255.255.255.255', $x->asIPv6OrNull() );

        $x = self::p( null );
        self::assertNull( $x->asIPv6OrNull() );

        $x = self::p( '2001:db8::3.4' );
        self::expectException( ParseException::class );
        $x->asIPv6OrNull();
    }


    public function testAsInt() : void {

        $x = self::p( '5' );
        self::assertSame( 5, $x->asInt() );

        $x = self::p( '5.9' );
        self::assertSame( 5, $x->asInt() );

        self::expectException( ParseException::class );
        $x = self::p( 'foo' );
        $x->asInt();

    }


    public function testAsIntForEmpty() : void {
        $x = self::p( '' );
        self::expectException( ParseException::class );
        $x->asInt();
    }


    public function testAsIntOrEmpty() : void {
        $x = self::p( '' );
        self::assertNull( $x->asIntOrEmpty() );

        $x = self::p( '5' );
        self::assertSame( 5, $x->asIntOrEmpty() );

        $x = self::p( '5.9' );
        self::assertSame( 5, $x->asIntOrEmpty() );

        $x = self::p( 'foo' );
        self::expectException( ParseException::class );
        $x->asIntOrEmpty();
    }


    public function testAsIntOrNull() : void {

        $x = self::p( '5' );
        self::assertSame( 5, $x->asIntOrNull() );

        $x = self::p( null );
        self::assertNull( $x->asIntOrNull() );

    }


    public function testAsIntRangeClosed() : void {
        $x = self::p( '5' );
        self::assertSame( 5, $x->asIntRangeClosed( 4, 6 ) );
        self::expectException( ParseException::class );
        $x->asIntRangeClosed( 5, 7 );
    }


    public function testAsIntRangeClosedOrEmpty() : void {
        $x = self::p( '5' );
        self::assertSame( 5, $x->asIntRangeClosedOrEmpty( 4, 6 ) );

        $x = self::p( '' );
        self::assertNull( $x->asIntRangeClosedOrEmpty( 4, 6 ) );

        $x = self::p( 'foo' );
        self::expectException( ParseException::class );
        $x->asIntRangeClosedOrEmpty( 4, 6 );
    }


    public function testAsIntRangeClosedOrNull() : void {
        $x = self::p( '5' );
        self::assertSame( 5, $x->asIntRangeClosedOrNull( 4, 6 ) );

        $x = self::p( null );
        self::assertNull( $x->asIntRangeClosedOrNull( 4, 6 ) );

        $x = self::p( '' );
        self::expectException( ParseException::class );
        $x->asIntRangeClosedOrNull( 4, 6 );
    }


    public function testAsIntRangeHalfClosed() : void {
        $x = self::p( '5' );
        self::assertSame( 5, $x->asIntRangeHalfClosed( 5, 6 ) );
        self::expectException( ParseException::class );
        $x->asIntRangeHalfClosed( 4, 5 );
    }


    public function testAsIntRangeHalfClosedOrEmpty() : void {
        $x = self::p( '5' );
        self::assertSame( 5, $x->asIntRangeHalfClosedOrEmpty( 5, 6 ) );

        $x = self::p( '' );
        self::assertNull( $x->asIntRangeHalfClosedOrEmpty( 5, 6 ) );

        $x = self::p( 'foo' );
        self::expectException( ParseException::class );
        $x->asIntRangeHalfClosedOrEmpty( 5, 6 );
    }


    public function testAsIntRangeHalfClosedOrNull() : void {
        $x = self::p( '5' );
        self::assertSame( 5, $x->asIntRangeHalfClosedOrNull( 5, 6 ) );

        $x = self::p( null );
        self::assertNull( $x->asIntRangeHalfClosedOrNull( 5, 6 ) );

        $x = self::p( '' );
        self::expectException( ParseException::class );
        $x->asIntRangeHalfClosedOrNull( 5, 6 );
    }


    public function testAsIntRangeOpen() : void {
        $x = self::p( '5' );
        self::assertSame( 5, $x->asIntRangeOpen( 4, 6 ) );
        self::assertSame( 5, $x->asIntRangeOpen( 4, 5 ) );
        self::assertSame( 5, $x->asIntRangeOpen( 5, 6 ) );
        self::expectException( ParseException::class );
        $x->asIntRangeOpen( 6, 7 );
    }


    public function testAsIntRangeOpenOrEmpty() : void {
        $x = self::p( '5' );
        self::assertSame( 5, $x->asIntRangeOpenOrEmpty( 5, 6 ) );

        $x = self::p( '' );
        self::assertNull( $x->asIntRangeOpenOrEmpty( 4, 6 ) );

        $x = self::p( 'foo' );
        self::expectException( ParseException::class );
        $x->asIntRangeOpenOrEmpty( 4, 6 );
    }


    public function testAsIntRangeOpenOrNull() : void {
        $x = self::p( '5' );
        self::assertSame( 5, $x->asIntRangeOpenOrNull( 4, 6 ) );

        $x = self::p( null );
        self::assertNull( $x->asIntRangeOpenOrNull( 4, 6 ) );

        $x = self::p( '' );
        self::expectException( ParseException::class );
        $x->asIntRangeOpenOrNull( 4, 6 );
    }


    public function testAsJSON() : void {
        $x = self::p( [ 'foo', 'bar' ] );
        self::assertSame( '["foo","bar"]', $x->asJSON() );
    }


    public function testAsKeyword() : void {
        $x = self::p( 'foo' );
        self::assertSame( 'foo', $x->asKeyword( [ 'foo', 'bar' ] ) );

        $x = self::p( 'baz' );
        self::expectException( ParseException::class );
        $x->asKeyword( [ 'foo', 'bar' ] );
    }


    public function testAsKeywordOrEmpty() : void {
        $x = self::p( 'foo' );
        self::assertSame( 'foo', $x->asKeywordOrEmpty( [ 'foo', 'bar' ] ) );

        $x = self::p( '' );
        self::assertNull( $x->asKeywordOrEmpty( [ 'foo', 'bar' ] ) );

        $x = self::p( 'baz' );
        self::expectException( ParseException::class );
        $x->asKeywordOrEmpty( [ 'foo', 'bar' ] );
    }


    public function testAsKeywordOrNull() : void {
        $x = self::p( 'foo' );
        self::assertSame( 'foo', $x->asKeywordOrNull( [ 'foo', 'bar' ] ) );

        $x = self::p( null );
        self::assertNull( $x->asKeywordOrNull( [ 'foo', 'bar' ] ) );

        $x = self::p( 'baz' );
        self::expectException( ParseException::class );
        $x->asKeywordOrNull( [ 'foo', 'bar' ] );
    }


    public function testAsMap() : void {
        $rMap = [ 'foo' => 'bar', 'baz' => 'qux' ];
        $x = self::p( 'foo' );
        self::assertSame( 'bar', $x->asMap( $rMap ) );

        $x = self::p( 'quux' );
        self::expectException( ParseException::class );
        $x->asMap( $rMap );
    }


    public function testAsMapOrEmpty() : void {
        $rMap = [ 'foo' => 'bar', 'baz' => 'qux' ];
        $x = self::p( 'foo' );
        self::assertSame( 'bar', $x->asMapOrEmpty( $rMap ) );

        $x = self::p( '' );
        self::assertNull( $x->asMapOrEmpty( $rMap ) );

        $x = self::p( '5' );
        self::expectException( ParseException::class );
        $x->asMapOrEmpty( $rMap );
    }


    public function testAsMapOrNull() : void {
        $rMap = [ 'foo' => 'bar', 'baz' => 'qux' ];
        $x = self::p( 'foo' );
        self::assertSame( 'bar', $x->asMapOrNull( $rMap ) );

        $x = self::p( null );
        self::assertNull( $x->asMapOrNull( $rMap ) );

        $x = self::p( '' );
        self::expectException( ParseException::class );
        $x->asMapOrNull( $rMap );
    }


    public function testAsNonexistentFilename() : void {
        $x = self::p( __DIR__ . '/foo' );
        self::assertSame( __DIR__ . '/foo', $x->asNonexistentFilename() );

        $x = self::p( __FILE__ );
        self::expectException( ParseException::class );
        $x->asNonexistentFilename();
    }


    public function testAsNonexistentFilenameOrEmpty() : void {
        $x = self::p( __DIR__ . '/foo' );
        self::assertSame( __DIR__ . '/foo', $x->asNonexistentFilenameOrEmpty() );

        $x = self::p( '' );
        self::assertNull( $x->asNonexistentFilenameOrEmpty() );

        $x = self::p( '/no/such/dir/file.txt' );
        self::expectException( ParseException::class );
        $x->asNonexistentFilenameOrEmpty();
    }


    public function testAsNonexistentFilenameOrNull() : void {
        $x = self::p( __DIR__ . '/foo' );
        self::assertSame( __DIR__ . '/foo', $x->asNonexistentFilenameOrNull() );

        $x = self::p( null );
        self::assertNull( $x->asNonexistentFilenameOrNull() );

        $x = self::p( '' );
        self::expectException( ParseException::class );
        $x->asNonexistentFilenameOrNull();
    }


    public function testAsPositiveFloat() : void {
        $x = self::p( '3.14' );
        self::assertEqualsWithDelta( 3.14, $x->asPositiveFloat(), 0.0001 );

        $x = self::p( '-1.23' );
        self::expectException( ParseException::class );
        $x->asPositiveFloat();
    }


    public function testAsPositiveFloatOrEmpty() : void {
        $x = self::p( '0.1' );
        self::assertEqualsWithDelta( 0.1, $x->asPositiveFloatOrEmpty(), 0.0001 );

        $x = self::p( '' );
        self::assertNull( $x->asPositiveFloatOrEmpty() );

        $x = self::p( '0' );
        self::expectException( ParseException::class );
        $x->asPositiveFloatOrEmpty();
    }


    public function testAsPositiveFloatOrNull() : void {
        $x = self::p( '3.14' );
        self::assertEqualsWithDelta( 3.14, $x->asPositiveFloatOrNull(), 0.0001 );

        $x = self::p( null );
        self::assertNull( $x->asPositiveFloatOrNull() );

        $x = self::p( '' );
        self::expectException( ParseException::class );
        $x->asPositiveFloatOrNull();
    }


    public function testAsPositiveInt() : void {
        $x = self::p( '3' );
        self::assertSame( 3, $x->asPositiveInt() );

        $x = self::p( '-1' );
        self::expectException( ParseException::class );
        $x->asPositiveInt();
    }


    public function testAsPositiveIntOrEmpty() : void {
        $x = self::p( '3' );
        self::assertSame( 3, $x->asPositiveIntOrEmpty() );

        $x = self::p( '' );
        self::assertNull( $x->asPositiveIntOrEmpty() );

        $x = self::p( '0' );
        self::expectException( ParseException::class );
        $x->asPositiveIntOrEmpty();
    }


    public function testAsPositiveIntOrNull() : void {
        $x = self::p( '3' );
        self::assertSame( 3, $x->asPositiveIntOrNull() );

        $x = self::p( null );
        self::assertNull( $x->asPositiveIntOrNull() );

        $x = self::p( '' );
        self::expectException( ParseException::class );
        $x->asPositiveIntOrNull();
    }


    public function testAsRoundedInt() : void {
        $x = self::p( '5.5' );
        self::assertSame( 6, $x->asRoundedInt() );

        $x = self::p( '5.4' );
        self::assertSame( 5, $x->asRoundedInt() );
    }


    public function testAsString() : void {
        $x = self::p( '5.5' );
        /** @phpstan-ignore staticMethod.alreadyNarrowedType */
        self::assertIsString( $x->asString() );
        self::assertSame( '5.5', $x->asString() );
        $x = self::p( '5' );
        /** @phpstan-ignore staticMethod.alreadyNarrowedType */
        self::assertIsString( $x->asString() );
        self::assertSame( '5', $x->asString() );
        self::expectException( TypeError::class );
        $x = self::p( [ 'foo', 'bar' ] );
        $x->asString();
    }


    public function testAsStringOrNull() : void {

        $x = self::p( 'foo' );
        self::assertSame( 'foo', $x->asStringOrNull() );

        $x = self::p( null );
        self::assertNull( $x->asStringOrNull() );

    }


    public function testAsTime() : void {
        $x = self::p( '2024-01-25 12:34:56' );
        self::assertSame( '12:34:56', $x->asTime() );

        $x = self::p( 'March 2, 2025 3:53 PM' );
        self::assertSame( '15:53:00', $x->asTime() );

        $x = self::p( '2024-01-26 + 2 days' );
        self::assertSame( '00:00:00', $x->asTime() );

        $x = self::p( 'foo' );
        self::expectException( ParseException::class );
        $x->asTime();
    }


    public function testAsTimeOrNull() : void {
        $x = self::p( '2024-01-25 12:34:56' );
        self::assertSame( '12:34:56', $x->asTimeOrNull() );

        $x = self::p( null );
        self::assertNull( $x->asTimeOrNull() );
    }


    public function testAsTimeStamp() : void {
        $tm = time();
        $x = self::p( date( 'r', $tm ) );
        self::assertSame( $tm, $x->asTimeStamp() );

        $x = self::p( 'foo' );
        self::expectException( ParseException::class );
        $x->asTimeStamp();
    }


    public function testAsTimeStampOrNull() : void {
        $tm = time();
        $x = self::p( date( 'r', $tm ) );
        self::assertSame( $tm, $x->asTimeStampOrNull() );

        $x = self::p( null );
        self::assertNull( $x->asTimeStampOrNull() );
    }


    public function testAsUnsignedFloat() : void {
        $x = self::p( '1.23' );
        self::assertEqualsWithDelta( 1.23, $x->asUnsignedFloat(), 0.0001 );

        $x = self::p( '-1.23' );
        self::expectException( ParseException::class );
        $x->asUnsignedFloat();
    }


    public function testAsUnsignedFloatOrEmpty() : void {
        $x = self::p( '0' );
        self::assertEqualsWithDelta( 0.0, $x->asUnsignedFloatOrEmpty(), 0.0001 );

        $x = self::p( '' );
        self::assertNull( $x->asUnsignedFloatOrEmpty() );

        $x = self::p( 'foo' );
        self::expectException( ParseException::class );
        $x->asUnsignedFloatOrEmpty();
    }


    public function testAsUnsignedFloatOrNull() : void {
        $x = self::p( '1.23' );
        self::assertEqualsWithDelta( 1.23, $x->asUnsignedFloatOrNull(), 0.0001 );

        $x = self::p( null );
        self::assertNull( $x->asUnsignedFloatOrNull() );

        $x = self::p( '' );
        self::expectException( ParseException::class );
        $x->asUnsignedFloatOrNull();
    }


    public function testAsUnsignedInt() : void {
        $x = self::p( '5' );
        self::assertSame( 5, $x->asUnsignedInt() );

        $x = self::p( '-5' );
        self::expectException( ParseException::class );
        $x->asUnsignedInt();
    }


    public function testAsUnsignedIntOrEmpty() : void {
        $x = self::p( '0' );
        self::assertSame( 0, $x->asUnsignedIntOrEmpty() );

        $x = self::p( '' );
        self::assertNull( $x->asUnsignedIntOrEmpty() );

        $x = self::p( 'foo' );
        self::expectException( ParseException::class );
        $x->asUnsignedIntOrEmpty();
    }


    public function testAsUnsignedIntOrNull() : void {
        $x = self::p( '5' );
        self::assertSame( 5, $x->asUnsignedIntOrNull() );

        $x = self::p( null );
        self::assertNull( $x->asUnsignedIntOrNull() );

        $x = self::p( '' );
        self::expectException( ParseException::class );
        $x->asUnsignedIntOrNull();
    }


    public function testCoerce() : void {
        $p = self::p( 'foo' );
        self::assertSame( $p, Parameter::coerce( $p ) );
        self::assertSame( 'foo', Parameter::coerce( 'foo' )->asString() );
        self::assertNull( Parameter::coerce( null ) );
    }


    public function testConstructWithArray() : void {
        $x = self::p( [ 'foo', 'bar' ] );
        self::assertInstanceOf( Parameter::class, $x );

    }


    public function testConstructWithBool() : void {
        $x = self::p( true );
        self::assertInstanceOf( Parameter::class, $x );
        self::assertSame( 'true', $x->asString() );
        self::assertTrue( $x->asBool() );

        $x = self::p( false );
        self::assertInstanceOf( Parameter::class, $x );
        self::assertSame( 'false', $x->asString() );
        self::assertFalse( $x->asBool() );
    }


    public function testConstructWithFloat() : void {
        $x = self::p( 5.5 );
        self::assertInstanceOf( Parameter::class, $x );
        self::assertSame( '5.5', $x->asString() );
        self::assertEqualsWithDelta( 5.5, $x->asFloat(), 0.0001 );
    }


    public function testConstructWithInt() : void {
        $x = self::p( 5 );
        self::assertInstanceOf( Parameter::class, $x );
        self::assertEquals( '5', $x->asString() );
        self::assertSame( 5, $x->asInt() );
    }


    public function testConstructWithNestedArray() : void {
        $x = self::p( [
            [ 'foo', 'bar' ],
            [ 'baz', 'qux' ],
        ] );
        self::assertInstanceOf( Parameter::class, $x );
        self::assertTrue( $x->isArray() );
        self::assertTrue( isset( $x[ 0 ] ) );
        self::assertTrue( $x[ 0 ]->isArray() );
        self::assertTrue( isset( $x[ 0 ][ 0 ] ) );
        self::assertSame( 'foo', $x[ 0 ][ 0 ]->asString() );

        $x = self::p( [
            [ 'foo', 'bar' ],
            [ 'baz', 'qux' ],
        ], nuAllowArrayDepth: 1 );
        self::expectException( InvalidArgumentException::class );
        $y = $x[ 0 ];
        unset( $y );
    }


    public function testConstructWithNull() : void {
        $x = self::p( null );
        self::assertInstanceOf( Parameter::class, $x );
        self::expectException( InvalidArgumentException::class );
        $x = self::p( null, bAllowNull: false );
        unset( $x );
    }


    public function testConstructWithParameter() : void {
        $x = self::p( 'foo' );
        self::assertSame( 'foo', $x->asString() );
        $y = self::p( $x );
        self::assertSame( 'foo', $y->asString() );
    }


    public function testConstructWithString() : void {
        $x = self::p( 'foo' );
        self::assertInstanceOf( Parameter::class, $x );
    }


    public function testForEach() : void {
        $x = self::p( [ 'foo', 'bar' ] );
        $r = [];
        /** @noinspection PhpLoopCanBeConvertedToArrayMapInspection */
        foreach ( $x as $i => $p ) {
            $r[ $i ] = $p->asString();
        }
        self::assertSame( [ 'foo', 'bar' ], $r );
    }


    public function testFreeze() : void {
        $x = new MyTestParameter( 'foo' );
        self::assertFalse( $x->isFrozen() );
        $x->freeze();
        self::assertTrue( $x->isFrozen() );
    }


    public function testGetValue() : void {
        $x = self::p( '5' );
        self::assertSame( '5', $x->getValue() );

        $x = self::p( null );
        self::assertNull( $x->getValue() );

        $x = self::p( [ 'foo', 'bar' ] );
        self::assertSame( [ 'foo', 'bar' ], $x->getValue() );

    }


    public function testHas() : void {
        $x = self::p( [ 'foo', 'bar' ] );
        self::assertTrue( $x->offsetExists( 0 ) );
        self::assertTrue( $x->offsetExists( 1 ) );
        self::assertFalse( $x->offsetExists( 2 ) );
        self::assertFalse( $x->offsetExists( 'foo' ) );

        $x = self::p( [ 'foo' => 'bar', 'baz' => 'qux' ] );
        self::assertTrue( $x->offsetExists( 'foo' ) );
        self::assertTrue( $x->offsetExists( 'baz' ) );
        self::assertFalse( $x->offsetExists( 'qux' ) );
        self::assertFalse( $x->offsetExists( 0 ) );

        $x = self::p( 'foo' );
        self::expectException( TypeError::class );
        $x->has( 0 );
    }


    public function testIndexOrDefault() : void {

        $x = self::p( [ 'foo', 'bar' ] );
        self::assertSame( 'foo', $x->indexOrDefault( 0, 'baz' )->asString() );
        self::assertSame( 'baz', $x->indexOrDefault( 2, 'baz' )->asString() );

    }


    public function testIndexOrNull() : void {
        $x = self::p( [ 'foo', 'bar' ] );
        self::assertSame( 'foo', $x->indexOrNull( 0 )->asString() );
        self::assertNull( $x->indexOrNull( 2 ) );
    }


    public function testIs() : void {
        $x = self::p( 1 );
        self::assertTrue( $x->is( '1' ) );
        self::assertTrue( $x->is( self::p( '1' ) ) );
        self::assertTrue( $x->is( 1 ) );
        self::assertTrue( $x->is( 1.0 ) );
        self::assertTrue( $x->is( true ) );
        self::assertFalse( $x->is( 'foo' ) );
        self::assertFalse( $x->is( false ) );
        self::assertFalse( $x->is( null ) );
        self::assertFalse( $x->is( [ 'foo', 'bar' ] ) );

        $x = self::p( 'foo' );
        self::assertTrue( $x->is( 'foo' ) );
        self::assertFalse( $x->is( 'bar' ) );
        self::assertFalse( $x->is( null ) );
        self::assertFalse( $x->is( 0 ) );
        self::assertFalse( $x->is( 1 ) );
        self::assertFalse( $x->is( 1.0 ) );
        self::assertFalse( $x->is( true ) );
        self::assertFalse( $x->is( false ) );

        $x = self::p( [ 'foo', 'bar' ] );
        self::assertFalse( $x->is( 'foo' ) );
        self::assertFalse( $x->is( 'bar' ) );
        self::assertFalse( $x->is( null ) );
        self::assertFalse( $x->is( 0 ) );
        self::assertFalse( $x->is( 1 ) );
        self::assertFalse( $x->is( 1.0 ) );
        self::assertFalse( $x->is( true ) );
        self::assertFalse( $x->is( false ) );

        $x = self::p( null );
        self::assertTrue( $x->is( null ) );
        self::assertFalse( $x->is( '' ) );
        self::assertFalse( $x->is( 'foo' ) );
        self::assertFalse( $x->is( 0 ) );
        self::assertFalse( $x->is( 1 ) );
        self::assertFalse( $x->is( 1.0 ) );
        self::assertFalse( $x->is( true ) );
        self::assertFalse( $x->is( false ) );
        self::assertFalse( $x->is( [ 'foo', 'bar' ] ) );

        $x = self::p( [ 'foo', 'bar' ] );
        self::assertTrue( $x->is( [ 'foo', 'bar' ] ) );
        self::assertTrue( $x->is( self::p( [ 'foo', 'bar' ] ) ) );
        self::assertFalse( $x->is( [ 'foo' ] ) );
        self::assertFalse( $x->is( [ 'bar', 'foo' ] ) );
        self::assertFalse( $x->is( self::p( [ 'bar', 'foo' ] ) ) );
        self::assertFalse( $x->is( [ 'foo', 'bar', 'baz' ] ) );
        self::assertFalse( $x->is( 'foo' ) );
        self::assertFalse( $x->is( 'bar' ) );
        self::assertFalse( $x->is( null ) );
        self::assertFalse( $x->is( 0 ) );
        self::assertFalse( $x->is( 1 ) );
        self::assertFalse( $x->is( 1.0 ) );
        self::assertFalse( $x->is( true ) );
        self::assertFalse( $x->is( false ) );

        $x = self::p( true );
        self::assertTrue( $x->is( true ) );

        # This is an interesting case, because it's false but the reverse
        # is true. I.e., parameter(true)->is(1) is false but
        # parameter(1)->is(true) is true because the value true is stored
        # as "1" internally.
        self::assertFalse( $x->is( 1 ) );
        self::assertFalse( $x->is( 'yes' ) );
        self::assertFalse( $x->is( 'no' ) );
        self::assertFalse( $x->is( 'foo' ) );
        self::assertFalse( $x->is( false ) );
        self::assertFalse( $x->is( null ) );
        self::assertFalse( $x->is( 0 ) );

    }


    public function testIsArray() : void {
        $x = self::p( '5.5' );
        self::assertFalse( $x->isArray() );
        $x = self::p( [ 'foo', 'bar' ] );
        self::assertTrue( $x->isArray() );
    }


    public function testIsBool() : void {
        self::assertTrue( self::p( true )->isBool() );
        self::assertTrue( self::p( false )->isBool() );
        self::assertTrue( self::p( null )->isBool() );
        self::assertTrue( self::p( 'true' )->isBool() );
        self::assertTrue( self::p( 'no' )->isBool() );
        self::assertTrue( self::p( '5' )->isBool() );
        self::assertTrue( self::p( '0' )->isBool() );
        self::assertFalse( self::p( 'foo' )->isBool() );
        self::assertFalse( self::p( [ 'foo', 'bar' ] )->isBool() );
    }


    public function testIsEmpty() : void {
        $x = self::p( '5' );
        self::assertFalse( $x->isEmpty() );

        $x = self::p( [ 'foo', 'bar' ] );
        self::assertFalse( $x->isEmpty() );

        $x = self::p( null );
        self::assertTrue( $x->isEmpty() );

        $x = self::p( '' );
        self::assertTrue( $x->isEmpty() );

        $x = self::p( [] );
        self::assertTrue( $x->isEmpty() );

        $x = self::p( 'yup' );
        $y = self::p( $x );
        self::assertFalse( $y->isEmpty() );

        $x = self::p( null );
        $y = self::p( $x );
        self::assertTrue( $y->isEmpty() );
    }


    public function testIsNull() : void {

        $x = self::p( '5' );
        self::assertFalse( $x->isNull() );

        $x = self::p( [ 'foo', 'bar' ] );
        self::assertFalse( $x->isNull() );

        $x = self::p( null );
        self::assertTrue( $x->isNull() );

    }


    public function testIsSet() : void {
        $x = self::p( '5' );
        self::assertTrue( $x->isSet() );
    }


    public function testIsString() : void {
        $x = self::p( 'foo' );
        self::assertTrue( $x->isString() );

        $x = self::p( [ 'foo', 'bar' ] );
        self::assertFalse( $x->isString() );

        $x = self::p( null );
        self::assertFalse( $x->isString() );

    }


    public function testJsonSerialize() : void {
        $x = self::p( 'foo' );
        self::assertSame( '"foo"', json_encode( $x ) );

        $x = self::p( [ 'foo', 'bar' ] );
        self::assertSame( '["foo","bar"]', json_encode( $x ) );

        $x = self::p( null );
        self::assertSame( 'null', json_encode( $x ) );

        $pBar = self::p( 'bar' );
        $pFoo = self::p( [ 'foo' => $pBar ] );
        self::assertSame( '"bar"', json_encode( $pBar ) );
        self::assertSame( '{"foo":"bar"}', json_encode( $pFoo ) );
    }


    public function testMutable() : void {
        $x = new MyTestParameter( 'foo' );
        self::assertFalse( $x->isMutable() );
        $x->mutate( true );
        self::assertTrue( $x->isMutable() );
        $x->mutate( false );
        self::expectException( LogicException::class );
        $x->set( 'bar' );
    }


    public function testMutableFrozen() : void {
        $x = new MyTestParameter( 'foo' );
        $x->freeze();
        self::expectException( LogicException::class );
        $x->mutate( true );
    }


    public function testNew() : void {
        $x = self::p( 'foo' );
        $y = $x->new( 'bar' );
        self::assertSame( 'bar', $y->asString() );
        self::assertSame( $x::class, $y::class );
    }


    public function testOffsetExists() : void {

        $x = self::p( [ 'foo' => 'bar', 'baz' => 'qux' ] );
        self::assertTrue( isset( $x[ 'foo' ] ) );
        self::assertFalse( isset( $x[ 'qux' ] ) );

        self::expectException( InvalidArgumentException::class );
        $y = isset( $x[ null ] );
        unset( $y );

    }


    public function testOffsetGet() : void {
        $x = self::p( [ 'foo', 'bar' ] );
        self::assertTrue( isset( $x[ 0 ] ) );
        self::assertSame( 'foo', $x[ 0 ]->asString() );
        self::assertSame( 'bar', $x[ 1 ]->asString() );
    }


    public function testOffsetGetForInvalidArgument() : void {
        $x = self::p( [ 'foo', 'bar' ] );
        self::expectException( InvalidArgumentException::class );
        $y = $x[ null ];
        unset( $y );
    }


    public function testOffsetGetForOutOfBounds() : void {
        $x = self::p( [ 'foo', 'bar' ] );
        self::expectException( OutOfBoundsException::class );
        $y = $x[ 2 ];
        unset( $y );
    }


    public function testOffsetGetWithInt() : void {
        $x = self::p( [ 'foo' => 3 ] );
        self::assertSame( 3, $x[ 'foo' ]->asInt() );
    }


    public function testOffsetGetWithString() : void {
        $x = self::p( 'foo' );
        self::expectException( TypeError::class );
        $y = $x[ 0 ];
        unset( $y );
    }


    public function testOffsetSet() : void {
        $x = self::p( [ 'foo', 'bar' ] );
        self::expectException( LogicException::class );
        $x[ 0 ] = 'baz';
    }


    public function testOffsetUnset() : void {
        $x = self::p( [ 'foo', 'bar' ] );
        self::expectException( LogicException::class );
        unset( $x[ 0 ] );
    }


    public function testRoundedFloat() : void {
        $x = self::p( '5.5' );
        self::assertEqualsWithDelta( 5.5, $x->asRoundedFloat( 1 ), 0.0001 );

        $x = self::p( '5.4' );
        self::assertEqualsWithDelta( 5.4, $x->asRoundedFloat( 1 ), 0.0001 );

        $x = self::p( '1.2345' );
        self::assertEqualsWithDelta( 1.0, $x->asRoundedFloat(), 0.0001 );
        self::assertEqualsWithDelta( 1.2, $x->asRoundedFloat( 1 ), 0.0001 );
        self::assertEqualsWithDelta( 1.23, $x->asRoundedFloat( 2 ), 0.0001 );
        self::assertEqualsWithDelta( 1.235, $x->asRoundedFloat( 3 ), 0.0001 );
        self::assertEqualsWithDelta( 1.2345, $x->asRoundedFloat( 4 ), 0.0001 );
        self::assertEqualsWithDelta( 1.2345, $x->asRoundedFloat( 5 ), 0.0001 );

        $x = self::p( 'foo' );
        self::expectException( ParseException::class );
        $x->asRoundedFloat();
    }


    public function testRoundedFloatForEmpty() : void {
        $x = self::p( '' );
        self::expectException( ParseException::class );
        $x->asRoundedFloat();
    }


    public function testRoundedFloatOrEmpty() : void {
        $x = self::p( '5.5' );
        self::assertEqualsWithDelta( 5.5, $x->asRoundedFloatOrEmpty( 1 ), 0.0001 );

        $x = self::p( '' );
        self::assertNull( $x->asRoundedFloatOrEmpty() );

        $x = self::p( 'foo' );
        self::expectException( ParseException::class );
        $x->asRoundedFloatOrEmpty();
    }


    public function testRoundedFloatOrEmptyForNull() : void {
        $x = self::p( null );
        self::expectException( TypeError::class );
        $x->asRoundedFloatOrEmpty();
    }


    public function testRoundedFloatOrNull() : void {
        $x = self::p( '5.5' );
        self::assertEqualsWithDelta( 5.5, $x->asRoundedFloatOrNull( 1 ), 0.0001 );

        $x = self::p( '5.4' );
        self::assertEqualsWithDelta( 5.4, $x->asRoundedFloatOrNull( 1 ), 0.0001 );

        $x = self::p( null );
        self::assertNull( $x->asRoundedFloatOrNull() );
    }


    public function testRoundedInt() : void {
        $x = self::p( '5.5' );
        self::assertSame( 6, $x->asRoundedInt() );
        self::assertSame( 5, $x->asRoundedInt( 1 ) );


        $x = self::p( '5.4' );
        self::assertSame( 5, $x->asRoundedInt() );

        $x = self::p( '153.456' );
        self::assertSame( 153, $x->asRoundedInt() );
        self::assertSame( 150, $x->asRoundedInt( -1 ) );
        self::assertSame( 200, $x->asRoundedInt( -2 ) );

        $x = self::p( 'foo' );
        self::expectException( ParseException::class );
        $x->asRoundedInt();
    }


    public function testRoundedIntOrEmpty() : void {
        $x = self::p( '5.5' );
        self::assertSame( 6, $x->asRoundedIntOrEmpty() );

        $x = self::p( '' );
        self::assertNull( $x->asRoundedIntOrEmpty() );

        $x = self::p( 'foo' );
        self::expectException( ParseException::class );
        $x->asRoundedIntOrEmpty();
    }


    public function testRoundedIntOrNull() : void {
        $x = self::p( '5.5' );
        self::assertSame( 6, $x->asRoundedIntOrNull() );

        $x = self::p( '5.4' );
        self::assertSame( 5, $x->asRoundedIntOrNull() );

        $x = self::p( null );
        self::assertNull( $x->asRoundedIntOrNull() );
    }


    public function testToString() : void {
        $x = self::p( 'foo' );
        self::assertSame( 'foo', (string) $x );

        self::assertSame( 'xfoox', 'x' . $x . 'x' );

        $x = self::p( [ 'foo', 'bar' ] );
        self::expectException( TypeError::class );
        $y = (string) $x;
        unset( $y );
    }


}
