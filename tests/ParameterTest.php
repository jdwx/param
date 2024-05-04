<?php


declare( strict_types = 1 );


use JDWX\Param\Parameter;
use PHPUnit\Framework\TestCase;


require_once __DIR__ . '/MyTestParameter.php';


class ParameterTest extends TestCase {


    public function testAsArrayOrNull() : void {

        $x = new Parameter( [ "foo", "bar" ] );
        static::assertSame( [ "foo", "bar" ], $x->asArrayOrNull() );

        $x = new Parameter( null );
        static::assertNull( $x->asArrayOrNull() );

        $x = new Parameter( "foo" );
        static::expectException( TypeError::class );
        $x->asArrayOrNull();

    }


    public function testAsArrayOrString() : void {
        $x = new Parameter( [ "foo", "bar" ] );
        static::assertSame( [ "foo", "bar" ], $x->asArrayOrString() );

        $x = new Parameter( "foo" );
        static::assertSame( "foo", $x->asArrayOrString() );

        $x = new Parameter( null );
        static::expectException( TypeError::class );
        $x->asArrayOrString();
    }


    public function testAsArrayWithArray() : void {
        $x = new Parameter( [ "foo", "bar" ] );
        static::assertSame( [ "foo", "bar" ], $x->asArray() );
    }


    public function testAsArrayWithNull() : void {
        $x = new Parameter( null );
        static::expectException( TypeError::class );
        $x->asArray();
    }


    public function testAsArrayWithString() {
        $x = new Parameter( "5" );
        static::expectException( TypeError::class );
        $x->asArray();
    }


    public function testAsBool() {

        $x = new Parameter( "true" );
        static::assertTrue( $x->asBool() );

        $x = new Parameter( "false" );
        static::assertFalse( $x->asBool() );

        $x = new Parameter( "yes" );
        static::assertTrue( $x->asBool() );

        $x = new Parameter( "no" );
        static::assertFalse( $x->asBool() );

        $x = new Parameter( "on" );
        static::assertTrue( $x->asBool() );

        $x = new Parameter( "off" );
        static::assertFalse( $x->asBool() );

        $x = new Parameter( "1" );
        static::assertTrue( $x->asBool() );

        $x = new Parameter( "0" );
        static::assertFalse( $x->asBool() );

        $x = new Parameter( null );
        static::assertFalse( $x->asBool() );

        $x = new Parameter( "foo" );
        static::expectException( TypeError::class );
        $x->asBool();

    }


    public function testAsBoolOrNull() : void {
        $x = new Parameter( "false" );
        static::assertFalse( $x->asBoolOrNull() );

        $x = new Parameter( null );
        static::assertNull( $x->asBoolOrNull() );

    }


    public function testAsCurrency() : void {
        $x = new Parameter( "5.5" );
        static::assertSame( 550, $x->asCurrency() );

        $x = new Parameter( "5" );
        static::assertSame( 500, $x->asCurrency() );

        $x = new Parameter( "5.000000000001" );
        static::assertSame( 500, $x->asCurrency() );

        $x = new Parameter( "4.999999999999" );
        static::assertSame( 500, $x->asCurrency() );

        $x = new Parameter( "foo" );
        static::expectException( TypeError::class );
        $x->asCurrency();
    }


    public function testAsCurrencyForEmpty() : void {
        $x = new Parameter( "" );
        static::expectException( TypeError::class );
        $x->asCurrency();
    }


    public function testAsCurrencyOrEmpty() : void {
        $x = new Parameter( "5.5" );
        static::assertSame( 550, $x->asCurrencyOrEmpty() );

        $x = new Parameter( "" );
        static::assertNull( $x->asCurrencyOrEmpty() );

        $x = new Parameter( "foo" );
        static::expectException( TypeError::class );
        $x->asCurrencyOrEmpty();
    }


    public function testAsCurrencyOrNull() : void {
        $x = new Parameter( "5.5" );
        static::assertSame( 550, $x->asCurrencyOrNull() );

        $x = new Parameter( null );
        static::assertNull( $x->asCurrencyOrNull() );
    }


    public function testAsFloatOrEmpty() : void {
        $x = new Parameter( "5.5" );
        static::assertEqualsWithDelta( 5.5, $x->asFloatOrEmpty(), 0.0001 );

        $x = new Parameter( "" );
        static::assertNull( $x->asFloatOrEmpty() );

        $x = new Parameter( "foo" );
        static::expectException( TypeError::class );
        $x->asFloatOrEmpty();
    }


    public function testAsFloatOrEmptyForArray() : void {
        $x = new Parameter( [ "foo", "bar" ] );
        static::expectException( TypeError::class );
        $x->asFloatOrEmpty();
    }


    public function testAsFloatOrEmptyForNull() : void {
        $x = new Parameter( null );
        static::expectException( TypeError::class );
        $x->asFloatOrEmpty();
    }


    public function testAsFloatOrNull() : void {

        $x = new Parameter( "5.5" );
        static::assertEqualsWithDelta( 5.5, $x->asFloatOrNull(), 0.0001 );

        $x = new Parameter( null );
        static::assertNull( $x->asFloatOrNull() );

    }


    public function testAsFloatWithArray() : void {
        $x = new Parameter( [ "foo", "bar" ] );
        static::expectException( TypeError::class );
        $x->asFloat();
    }


    public function testAsFloatWithNull() : void {

        $x = new Parameter( null );
        static::expectException( TypeError::class );
        $x->asFloat();

    }


    public function testAsFloatWithStringFloat() : void {

        $x = new Parameter( "5.5" );
        static::assertIsFloat( $x->asFloat() );
        static::assertEqualsWithDelta( 5.5, $x->asFloat(), 0.0001 );

    }


    public function testAsFloatWithStringInt() : void {

        $x = new Parameter( "5" );
        static::assertIsFloat( $x->asFloat() );
        static::assertEqualsWithDelta( 5.0, $x->asFloat(), 0.0001 );

    }


    public function testAsFloatWithStringText() : void {

        $x = new Parameter( "foo" );
        static::expectException( TypeError::class );
        $x->asFloat();

    }


    public function testAsInt() {

        $x = new Parameter( "5" );
        static::assertSame( 5, $x->asInt() );

        $x = new Parameter( "5.9" );
        static::assertSame( 5, $x->asInt() );

        static::expectException( TypeError::class );
        $x = new Parameter( "foo" );
        $x->asInt();

    }


    public function testAsIntEmpty() : void {
        $x = new Parameter( "" );
        static::assertNull( $x->asIntOrEmpty() );

        $x = new Parameter( "5" );
        static::assertSame( 5, $x->asIntOrEmpty() );

        $x = new Parameter( "5.9" );
        static::assertSame( 5, $x->asIntOrEmpty() );

        $x = new Parameter( "foo" );
        static::expectException( TypeError::class );
        $x->asIntOrEmpty();
    }


    public function testAsIntForEmpty() : void {
        $x = new Parameter( "" );
        static::expectException( TypeError::class );
        $x->asInt();
    }


    public function testAsIntOrNull() : void {

        $x = new Parameter( "5" );
        static::assertSame( 5, $x->asIntOrNull() );

        $x = new Parameter( null );
        static::assertNull( $x->asIntOrNull() );

    }


    public function testAsJSON() : void {
        $x = new Parameter( [ "foo", "bar" ] );
        static::assertSame( '["foo","bar"]', $x->asJSON() );
    }


    public function testAsRoundedInt() : void {
        $x = new Parameter( "5.5" );
        static::assertSame( 6, $x->asRoundedInt() );

        $x = new Parameter( "5.4" );
        static::assertSame( 5, $x->asRoundedInt() );
    }


    public function testAsString() : void {
        $x = new Parameter( "5.5" );
        static::assertIsString( $x->asString() );
        static::assertSame( "5.5", $x->asString() );
        $x = new Parameter( "5" );
        static::assertIsString( $x->asString() );
        static::assertSame( "5", $x->asString() );
        static::expectException( TypeError::class );
        $x = new Parameter( [ "foo", "bar" ] );
        $x->asString();
    }


    public function testAsStringOrNull() : void {

        $x = new Parameter( "foo" );
        static::assertSame( "foo", $x->asStringOrNull() );

        $x = new Parameter( null );
        static::assertNull( $x->asStringOrNull() );

    }


    public function testConstructWithArray() : void {
        $x = new Parameter( [ "foo", "bar" ] );
        static::assertInstanceOf( Parameter::class, $x );

    }


    /** @suppress PhanTypeMismatchArgumentReal */
    public function testConstructWithInt() : void {
        static::expectException( TypeError::class );
        /** @noinspection PhpStrictTypeCheckingInspection */
        $x = new Parameter( 5 );
        unset( $x );
    }


    public function testConstructWithNestedArray() : void {
        $x = new Parameter( [
            [ "foo", "bar" ],
            [ "baz", "qux" ],
        ] );
        static::assertInstanceOf( Parameter::class, $x );
        static::assertTrue( $x->isArray() );
        static::assertTrue( isset( $x[ 0 ] ) );
        static::assertTrue( $x[ 0 ]->isArray() );
        static::assertTrue( isset( $x[ 0 ][ 0 ] ) );
        static::assertSame( "foo", $x[ 0 ][ 0 ]->asString() );

        $x = new Parameter( [
            [ "foo", "bar" ],
            [ "baz", "qux" ],
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
        static::assertSame( "foo", $x->asString() );
        $y = new Parameter( $x );
        static::assertSame( "foo", $y->asString() );
    }


    public function testConstructWithString() : void {
        $x = new Parameter( "foo" );
        static::assertInstanceOf( Parameter::class, $x );
    }


    public function testForEach() : void {
        $x = new Parameter( [ "foo", "bar" ] );
        $r = [];
        foreach ( $x as $i => $p ) {
            $r[ $i ] = $p->asString();
        }
        static::assertSame( [ "foo", "bar" ], $r );
    }


    public function testFreeze() : void {
        $x = new MyTestParameter( "foo" );
        static::assertFalse( $x->isFrozen() );
        $x->freeze();
        static::assertTrue( $x->isFrozen() );
    }


    public function testGetValue() {
        $x = new Parameter( "5" );
        static::assertSame( "5", $x->getValue() );

        $x = new Parameter( null );
        static::assertNull( $x->getValue() );

        $x = new Parameter( [ "foo", "bar" ] );
        static::assertSame( [ "foo", "bar" ], $x->getValue() );

    }


    public function testHas() : void {
        $x = new Parameter( [ "foo", "bar" ] );
        static::assertTrue( $x->offsetExists( 0 ) );
        static::assertTrue( $x->offsetExists( 1 ) );
        static::assertFalse( $x->offsetExists( 2 ) );
        static::assertFalse( $x->offsetExists( "foo" ) );

        $x = new Parameter( [ "foo" => "bar", "baz" => "qux" ] );
        static::assertTrue( $x->offsetExists( "foo" ) );
        static::assertTrue( $x->offsetExists( "baz" ) );
        static::assertFalse( $x->offsetExists( "qux" ) );
        static::assertFalse( $x->offsetExists( 0 ) );

        $x = new Parameter( "foo" );
        static::expectException( TypeError::class );
        $x->has( 0 );
    }


    public function testIndexOrDefault() : void {

        $x = new Parameter( [ "foo", "bar" ] );
        static::assertSame( "foo", $x->indexOrDefault( 0, "baz" )->asString() );
        static::assertSame( "baz", $x->indexOrDefault( 2, "baz" )->asString() );

    }


    public function testIndexOrNull() : void {
        $x = new Parameter( [ "foo", "bar" ] );
        static::assertSame( "foo", $x->indexOrNull( 0 )->asString() );
        static::assertNull( $x->indexOrNull( 2 ) );
    }


    public function testIsArray() : void {
        $x = new Parameter( "5.5" );
        static::assertFalse( $x->isArray() );
        $x = new Parameter( [ "foo", "bar" ] );
        static::assertTrue( $x->isArray() );
    }


    public function testIsNull() : void {

        $x = new Parameter( "5" );
        static::assertFalse( $x->isNull() );

        $x = new Parameter( [ "foo", "bar" ] );
        static::assertFalse( $x->isNull() );

        $x = new Parameter( null );
        static::assertTrue( $x->isNull() );

    }


    public function testIsEmpty() : void {
        $x = new Parameter( "5" );
        static::assertFalse( $x->isEmpty() );

        $x = new Parameter( [ "foo", "bar" ] );
        static::assertFalse( $x->isEmpty() );

        $x = new Parameter( null );
        static::assertTrue( $x->isEmpty() );

        $x = new Parameter( "" );
        static::assertTrue( $x->isEmpty() );

        $x = new Parameter( [] );
        static::assertTrue( $x->isEmpty() );

        $x = new Parameter( "yup" );
        $y = new Parameter( $x );
        static::assertFalse( $y->isEmpty() );

        $x = new Parameter( null );
        $y = new Parameter( $x );
        static::assertTrue( $y->isEmpty() );
    }


    public function testIsSet() : void {
        $x = new Parameter( "5" );
        static::assertTrue( $x->isSet() );
    }


    public function testIsString() : void {
        $x = new Parameter( "foo" );
        static::assertTrue( $x->isString() );

        $x = new Parameter( [ "foo", "bar" ] );
        static::assertFalse( $x->isString() );

        $x = new Parameter( null );
        static::assertFalse( $x->isString() );

    }


    public function testMutable() : void {
        $x = new MyTestParameter( "foo" );
        static::assertFalse( $x->isMutable() );
        $x->mutate( true );
        static::assertTrue( $x->isMutable() );
        $x->mutate( false );
        static::expectException( LogicException::class );
        $x->set( "bar" );
    }


    public function testMutableFrozen() : void {
        $x = new MyTestParameter( "foo" );
        $x->freeze();
        static::expectException( LogicException::class );
        $x->mutate( true );
    }


    public function testNew() : void {
        $x = new Parameter( "foo" );
        $y = $x->new( "bar" );
        static::assertSame( "bar", $y->asString() );
        static::assertSame( $x::class, $y::class );
    }


    public function testOffsetExists() : void {

        $x = new Parameter( [ "foo" => "bar", "baz" => "qux" ] );
        static::assertTrue( isset( $x[ 'foo' ] ) );
        static::assertFalse( isset( $x[ 'qux' ] ) );

        static::expectException( InvalidArgumentException::class );
        $y = isset( $x[ null ] );
        unset( $y );

    }


    public function testOffsetGet() : void {
        $x = new Parameter( [ "foo", "bar" ] );
        static::assertTrue( isset( $x[ 0 ] ) );
        static::assertSame( "foo", $x[ 0 ]->asString() );
        static::assertSame( "bar", $x[ 1 ]->asString() );
    }


    public function testOffsetGetForInvalidArgument() : void {
        $x = new Parameter( [ "foo", "bar" ] );
        static::expectException( InvalidArgumentException::class );
        $y = $x[ null ];
        unset( $y );
    }


    public function testOffsetGetForOutOfBounds() : void {
        $x = new Parameter( [ "foo", "bar" ] );
        static::expectException( OutOfBoundsException::class );
        $y = $x[ 2 ];
        unset( $y );
    }


    public function testOffsetGetWithString() : void {
        $x = new Parameter( "foo" );
        static::expectException( TypeError::class );
        $y = $x[ 0 ];
        unset( $y );
    }


    public function testOffsetSet() : void {
        $x = new Parameter( [ "foo", "bar" ] );
        static::expectException( LogicException::class );
        $x[ 0 ] = "baz";
    }


    public function testOffsetUnset() : void {
        $x = new Parameter( [ "foo", "bar" ] );
        static::expectException( LogicException::class );
        unset( $x[ 0 ] );
    }


    public function testRoundedFloat() : void {
        $x = new Parameter( "5.5" );
        static::assertEqualsWithDelta( 5.5, $x->asRoundedFloat( 1 ), 0.0001 );

        $x = new Parameter( "5.4" );
        static::assertEqualsWithDelta( 5.4, $x->asRoundedFloat( 1 ), 0.0001 );

        $x = new Parameter( "1.2345" );
        static::assertEqualsWithDelta( 1.0, $x->asRoundedFloat(), 0.0001 );
        static::assertEqualsWithDelta( 1.2, $x->asRoundedFloat( 1 ), 0.0001 );
        static::assertEqualsWithDelta( 1.23, $x->asRoundedFloat( 2 ), 0.0001 );
        static::assertEqualsWithDelta( 1.235, $x->asRoundedFloat( 3 ), 0.0001 );
        static::assertEqualsWithDelta( 1.2345, $x->asRoundedFloat( 4 ), 0.0001 );
        static::assertEqualsWithDelta( 1.2345, $x->asRoundedFloat( 5 ), 0.0001 );

        $x = new Parameter( "foo" );
        static::expectException( TypeError::class );
        $x->asRoundedFloat();
    }


    public function testRoundedFloatForEmpty() : void {
        $x = new Parameter( "" );
        static::expectException( TypeError::class );
        $x->asRoundedFloat();
    }


    public function testRoundedFloatOrEmpty() : void {
        $x = new Parameter( "5.5" );
        static::assertEqualsWithDelta( 5.5, $x->asRoundedFloatOrEmpty( 1 ), 0.0001 );

        $x = new Parameter( "" );
        static::assertNull( $x->asRoundedFloatOrEmpty() );

        $x = new Parameter( "foo" );
        static::expectException( TypeError::class );
        $x->asRoundedFloatOrEmpty();
    }


    public function testRoundedFloatOrEmptyForNull() : void {
        $x = new Parameter( null );
        static::expectException( TypeError::class );
        $x->asRoundedFloatOrEmpty();
    }


    public function testRoundedFloatOrNull() : void {
        $x = new Parameter( "5.5" );
        static::assertEqualsWithDelta( 5.5, $x->asRoundedFloatOrNull( 1 ), 0.0001 );

        $x = new Parameter( "5.4" );
        static::assertEqualsWithDelta( 5.4, $x->asRoundedFloatOrNull( 1 ), 0.0001 );

        $x = new Parameter( null );
        static::assertNull( $x->asRoundedFloatOrNull() );
    }


    public function testRoundedInt() : void {
        $x = new Parameter( "5.5" );
        static::assertSame( 6, $x->asRoundedInt() );
        static::assertSame( 5, $x->asRoundedInt( 1 ) );


        $x = new Parameter( "5.4" );
        static::assertSame( 5, $x->asRoundedInt() );

        $x = new Parameter( "153.456" );
        static::assertSame( 153, $x->asRoundedInt() );
        static::assertSame( 150, $x->asRoundedInt( -1 ) );
        static::assertSame( 200, $x->asRoundedInt( -2 ) );

        $x = new Parameter( "foo" );
        static::expectException( TypeError::class );
        $x->asRoundedInt();
    }


    public function testRoundedIntOrEmpty() : void {
        $x = new Parameter( "5.5" );
        static::assertSame( 6, $x->asRoundedIntOrEmpty() );

        $x = new Parameter( "" );
        static::assertNull( $x->asRoundedIntOrEmpty() );

        $x = new Parameter( "foo" );
        static::expectException( TypeError::class );
        $x->asRoundedIntOrEmpty();
    }


    public function testRoundedIntOrNull() : void {
        $x = new Parameter( "5.5" );
        static::assertSame( 6, $x->asRoundedIntOrNull() );

        $x = new Parameter( "5.4" );
        static::assertSame( 5, $x->asRoundedIntOrNull() );

        $x = new Parameter( null );
        static::assertNull( $x->asRoundedIntOrNull() );
    }


    public function testToString() : void {
        $x = new Parameter( "foo" );
        static::assertSame( "foo", (string) $x );

        static::assertSame( "xfoox", "x" . $x . "x" );

        $x = new Parameter( [ "foo", "bar" ] );
        static::expectException( TypeError::class );
        $y = (string) $x;
        unset( $y );
    }


}
