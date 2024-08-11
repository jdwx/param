<?php


declare( strict_types = 1 );


use JDWX\Param\Parameter;
use JDWX\Param\ParameterSet;
use PHPUnit\Framework\TestCase;


final class ParameterSetTest extends TestCase {


    public function testAddParameterForDuplicate() : void {
        $set = new ParameterSet();
        $set->setParameter( 'foo', 'bar' );
        self::expectException( InvalidArgumentException::class );
        $set->addParameter( 'foo', 'baz' );
    }


    public function testGet() : void {
        $set = new ParameterSet( [ 'foo' => 'bar', 'quux' => 'corge' ], [ 'baz' => 'qux' ], [ 'foo', 'baz' ] );
        self::assertEquals( 'bar', $set->get( 'foo' )->asString() );
        self::assertEquals( 'qux', $set->get( 'baz' )->asString() );
        self::assertNull( $set->get( 'nope' ) );
        self::assertNull( $set->get( 'quux' ) );
    }


    public function testGetIgnoredKeys() : void {
        $set = new ParameterSet( i_itAllowedKeys: [ 'foo', 'baz' ] );
        $set->setParameter( 'foo', 'bar' );
        $set->setParameter( 'baz', 'qux' );
        $set->setParameter( 'quux', 'corge' );
        self::assertSame( [ 'quux' ], $set->getIgnoredKeys() );
    }


    public function testHas() : void {
        $set = new ParameterSet( [ 'foo' => 'bar', 'quux' => 'corge' ], [ 'baz' => 'qux' ], [ 'foo', 'baz', 'grault' ] );
        self::assertTrue( $set->has( 'foo' ) );
        self::assertTrue( $set->has( 'baz' ) );
        self::assertFalse( $set->has( 'quux' ) );
        self::assertFalse( $set->has( 'nope' ) );
        self::assertTrue( $set->has( 'foo', 'baz' ) );
        self::assertFalse( $set->has( 'foo', 'quux' ) );
        self::assertFalse( $set->has( 'foo', 'nope' ) );
        self::assertFalse( $set->has( 'foo', 'baz', 'grault' ) );

        $set = new ParameterSet( [ 'foo' => 'bar', 'quux' => 'corge' ], [ 'baz' => 'qux' ] );
        self::assertTrue( $set->has( 'foo' ) );
        self::assertTrue( $set->has( 'foo', 'baz' ) );
        self::assertTrue( $set->has( 'foo', 'baz', 'quux' ) );
        self::assertFalse( $set->has( 'grault' ) );
        self::assertFalse( $set->has( 'foo', 'baz', 'quux', 'grault' ) );
    }


    public function testListKeys() : void {
        $set = new ParameterSet( [ 'foo' => 'bar', 'quux' => 'corge' ], [ 'baz' => 'qux' ], [ 'foo', 'baz' ] );
        self::assertSame( [ 'foo', 'baz' ], $set->listKeys() );
    }


    public function testOffsetExists() : void {
        $set = new ParameterSet( [ 'foo' => 'bar', 'quux' => 'corge' ], [ 'baz' => 'qux' ], [ 'foo', 'baz', 'grault' ] );
        self::assertTrue( isset( $set[ 'foo' ] ) );
        self::assertTrue( isset( $set[ 'baz' ] ) );
        self::assertFalse( isset( $set[ 'quux' ] ) );
        self::assertFalse( isset( $set[ 'grault' ] ) );
        self::assertFalse( isset( $set[ 'nope' ] ) );
        /** @phpstan-ignore isset.offset */
        self::assertTrue( isset( $set[ [ 'foo', 'baz' ] ] ) );
        /** @phpstan-ignore isset.offset */
        self::assertFalse( isset( $set[ [ 'foo', 'quux' ] ] ) );
        /** @phpstan-ignore isset.offset */
        self::assertFalse( isset( $set[ [ 'foo', 'grault' ] ] ) );
        /** @phpstan-ignore isset.offset */
        self::assertFalse( isset( $set[ [ 'foo', 'nope' ] ] ) );
        self::assertFalse( isset( $set[ null ] ) );
    }


    public function testOffsetGet() : void {
        $set = new ParameterSet( [ 'foo' => 'bar' ], [ 'baz' => 'qux' ] );
        self::assertEquals( 'bar', $set[ 'foo' ]->asString() );
        self::assertEquals( 'qux', $set[ 'baz' ]->asString() );
        self::expectException( InvalidArgumentException::class );
        $x = $set[ null ];
        unset( $x );
    }


    public function testOffsetGetForNonExistentKey() : void {
        $set = new ParameterSet();
        self::expectException( InvalidArgumentException::class );
        $x = $set[ 'foo' ];
        unset( $x );
    }


    public function testOffsetSet() : void {
        $set = new ParameterSet();
        $set[ 'foo' ] = 'bar';
        self::assertInstanceOf( Parameter::class, $set[ 'foo' ] );
        self::expectException( InvalidArgumentException::class );
        $set[ 'foo' ] = 'baz';
    }


    public function testOffsetSetForNullKey() : void {
        $set = new ParameterSet();
        $set[ 'foo' ] = 'bar';
        self::expectException( LogicException::class );
        $set[] = 'baz';
    }


}
