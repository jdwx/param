<?php


declare( strict_types = 1 );


use JDWX\Param\StringParameter;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\TestCase;


#[CoversClass( StringParameter::class )]
final class StringParameterTest extends TestCase {


    public function testConstructWithString() : void {
        $x = new StringParameter( 'foo' );
        self::assertEquals( 'foo', $x->asString() );
    }


    public function testIsSet() : void {
        $x = new StringParameter();
        self::assertFalse( $x->isSet() );
        $x = new StringParameter( 'foo' );
        self::assertTrue( $x->isSet() );
    }


    public function testNew() : void {
        $x = new StringParameter( 'foo' );
        $y = $x->new( 'bar' );
        self::assertSame( 'bar', $y->asString() );
        static::assertSame( $x::class, $y::class );
    }


}
