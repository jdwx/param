<?php


declare( strict_types = 1 );


use JDWX\Param\StringParameter;
use PHPUnit\Framework\TestCase;


class StringParameterTest extends TestCase {


    public function testConstructWithString() : void {
        $x = new StringParameter( 'foo' );
        static::assertEquals( 'foo', $x->asString() );
    }


    public function testIsSet() : void {
        $x = new StringParameter();
        static::assertFalse( $x->isSet() );
        $x = new StringParameter( 'foo' );
        static::assertTrue( $x->isSet() );
    }


    public function testNew() : void {
        $x = new StringParameter( 'foo' );
        $y = $x->new( 'bar' );
        static::assertSame( 'bar', $y->asString() );
        static::assertSame( $x::class, $y::class );
    }


}
