<?php


declare( strict_types = 1 );


use JDWX\Param\MutableParameter;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\TestCase;


#[CoversClass( MutableParameter::class )]
final class MutableParameterTest extends TestCase {


    public function testFreeze() : void {
        $x = new MutableParameter();
        $x->set( 'foo' );
        $x->freeze();
        $this->expectException( LogicException::class );
        $x->set( 'bar' );
    }


    public function testNew() : void {
        $x = new MutableParameter( 'foo' );
        $y = $x->new( 'bar' );
        self::assertSame( 'bar', $y->asString() );
        self::assertSame( $x::class, $y::class );
    }


    public function testSet() : void {
        $x = new MutableParameter();
        $x->set( 'foo' );
        self::assertSame( 'foo', $x->asString() );
        $x->set( 'bar' );
        self::assertSame( 'bar', $x->asString() );
        $x->set( 5 );
        self::assertSame( 5, $x->asInt() );
        $x->set( 5.5 );
        self::assertEqualsWithDelta( 5.5, $x->asFloat(), 0.0001 );
        $x->set( true );
        self::assertTrue( $x->asBool() );
        $x->set( null );
        self::assertTrue( $x->isNull() );
    }


}
