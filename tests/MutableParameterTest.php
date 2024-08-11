<?php


declare( strict_types = 1 );


use JDWX\Param\MutableParameter;
use PHPUnit\Framework\TestCase;


class MutableParameterTest extends TestCase {


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
        static::assertSame( 'bar', $y->asString() );
        static::assertSame( $x::class, $y::class );
    }


    public function testSet() : void {
        $x = new MutableParameter();
        $x->set( 'foo' );
        static::assertSame( 'foo', $x->asString() );
        $x->set( 'bar' );
        static::assertSame( 'bar', $x->asString() );
    }


}
