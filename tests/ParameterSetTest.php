<?php


declare( strict_types = 1 );


use JDWX\Param\Parameter;
use JDWX\Param\ParameterSet;
use PHPUnit\Framework\TestCase;


final class ParameterSetTest extends TestCase {


    public function testAddAllowedKeys() : void {
        $set = new ParameterSet();
        $set->addAllowedKeys( [ 'foo', 'baz' ] );
        self::assertSame( [ 'foo', 'baz' ], $set->getAllowedKeys() );

        $set = new ParameterSet();
        $set->addAllowedKeys( [ 'foo', 1 ] );
        self::assertSame( [ 'foo', '1' ], $set->getAllowedKeys() );
    }


    public function testAddDefaultForDuplicate() : void {
        $set = new ParameterSet();
        $set->setDefault( 'foo', 'bar' );
        self::expectException( InvalidArgumentException::class );
        $set->addDefault( 'foo', 'baz' );
    }


    public function testAddDefaults() : void {
        $set = new ParameterSet();
        $set->addDefaults( [ 'foo' => 'bar', 1 => 'baz' ] );
        self::assertSame( 'bar', $set->get( 'foo' )->asString() );
        self::assertSame( 'baz', $set->get( '1' )->asString() );
    }


    public function testAddParameterForDuplicate() : void {
        $set = new ParameterSet();
        $set->setParameter( 'foo', 'bar' );
        self::expectException( InvalidArgumentException::class );
        $set->addParameter( 'foo', 'baz' );
    }


    public function testAddParametersForIntegerKey() : void {
        $set = new ParameterSet();
        $set->addParameters( [ 'foo' => 'bar', 1 => 'baz' ] );
        self::assertSame( 'bar', $set->get( 'foo' )->asString() );
        self::assertSame( 'baz', $set->get( '1' )->asString() );
    }


    public function testGet() : void {
        $set = new ParameterSet( [ 'foo' => 'bar', 'quux' => 'corge' ], [ 'baz' => 'qux' ], [ 'foo', 'baz' ] );
        self::assertEquals( 'bar', $set->get( 'foo' )->asString() );
        self::assertEquals( 'qux', $set->get( 'baz' )->asString() );
        self::assertNull( $set->get( 'nope' ) );
        self::assertNull( $set->get( 'quux' ) );
        self::assertSame( 'grault', $set->get( 'quux', 'grault' )->asString() );
    }


    public function testGetForAllowedKey() : void {
        $set = new ParameterSet( [ 'foo' => 'bar', 'quux' => 'corge' ], [ 'baz' => 'qux' ], [ 'foo', 'baz', 'grault' ] );
        self::assertEquals( 'bar', $set->get( 'foo' )->asString() );
        self::assertEquals( 'qux', $set->get( 'baz' )->asString() );
        self::assertNull( $set->get( 'grault' ) );
        self::assertSame(
            'garply',
            $set->get( 'grault', new Parameter( 'garply' ) )->asString()
        );
        self::assertSame( 'garply', $set->get( 'grault', 'garply' )->asString() );
    }


    public function testGetForDisallowedKey() : void {
        $set = new ParameterSet( [ 'foo' => 'bar', 'quux' => 'corge' ], [ 'baz' => 'qux' ], [ 'foo', 'baz' ] );
        self::assertNull( $set->get( 'quux' ) );
        self::assertSame( 'grault', $set->get( 'quux', new Parameter( 'grault' ) )->asString() );
        self::assertSame( 'grault', $set->get( 'quux', 'grault' )->asString() );
    }


    public function testGetIgnoredKeys() : void {
        $set = new ParameterSet( i_itAllowedKeys: [ 'foo', 'baz' ] );
        $set->setParameter( 'foo', 'bar' );
        $set->setParameter( 'baz', 'qux' );
        $set->setParameter( 'quux', 'corge' );
        self::assertSame( [ 'quux' ], $set->getIgnoredKeys() );
    }


    public function testGetValues() : void {
        $set = new ParameterSet( [ 'foo' => 'bar', 'baz' => [ 'qux', 'quux' ] ], [ 'corge' => 'garply' ] );
        $r = $set->getValues();
        self::assertCount( 3, $r );
        self::assertSame( 'bar', $r[ 'foo' ] );
        self::assertSame( [ 'qux', 'quux' ], $r[ 'baz' ] );
        self::assertSame( 'garply', $r[ 'corge' ] );

        $pQux = new Parameter( 'qux' );
        $pBar = new Parameter( 'bar' );
        $set = new ParameterSet( [ 'foo' => $pBar, 'baz' => [ $pQux, 'quux' ] ], [ 'corge' => 'garply' ] );
        $r = $set->getValues();
        self::assertCount( 3, $r );
        self::assertSame( 'bar', $r[ 'foo' ] );
        self::assertSame( [ 'qux', 'quux' ], $r[ 'baz' ] );
        self::assertSame( 'garply', $r[ 'corge' ] );
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


    public function testIter() : void {
        $set = new ParameterSet( [ 'foo' => 'bar', 'quux' => 'corge' ], [ 'baz' => 'qux' ], [ 'foo', 'baz', 'grault' ] );
        $a = [];
        /** @noinspection PhpLoopCanBeConvertedToArrayMapInspection */
        foreach ( $set->iter() as $stKey => $oParam ) {
            $a[ $stKey ] = $oParam->asString();
        }
        self::assertSame( [ 'foo' => 'bar', 'baz' => 'qux' ], $a );
    }


    public function testJsonSerialize() : void {
        $pQux = new Parameter( 'qux' );
        $pBar = new Parameter( 'bar' );
        $set = new ParameterSet( [ 'foo' => $pBar, 'baz' => [ $pQux, 'quux' ] ], [ 'corge' => 'garply' ] );
        $r = $set->jsonSerialize();
        self::assertCount( 3, $r );
        self::assertSame( 'bar', $r[ 'foo' ] );
        self::assertSame( [ 'qux', 'quux' ], $r[ 'baz' ] );
        self::assertSame( 'garply', $r[ 'corge' ] );

    }


    public function testKeys() : void {
        $set = new ParameterSet(
            [ 'foo' => 'bar', 'quux' => 'corge', 'grault' => 'garply' ],
            [ 'foo' => 'xyz', 'baz' => 'qux' ],
            [ 'foo', 'baz', 'quux' ] );
        self::assertSame( [ 'foo', 'baz' ], $set->testKeys( 'foo', 'baz', 'xyz', 'grault' ) );
    }


    public function testListKeys() : void {
        $set = new ParameterSet( [ 'foo' => 'bar', 'quux' => 'corge' ], [ 'baz' => 'qux' ], [ 'foo', 'baz' ] );
        self::assertSame( [ 'foo', 'baz' ], $set->listKeys() );

        $set = new ParameterSet();
        self::assertSame( [], $set->listKeys() );

        $set = new ParameterSet( [ 'foo' => 'bar' ], [ 'baz' => 'qux' ], [ 'quux' ] );
        self::assertSame( [], $set->listKeys() );

    }


    public function testOffsetExists() : void {
        $set = new ParameterSet( [ 'foo' => 'bar', 'quux' => 'corge' ], [ 'baz' => 'qux' ], [ 'foo', 'baz', 'grault' ] );
        self::assertTrue( isset( $set[ 'foo' ] ) );
        self::assertTrue( isset( $set[ 'baz' ] ) );
        self::assertFalse( isset( $set[ 'quux' ] ) );
        self::assertFalse( isset( $set[ 'grault' ] ) );
        self::assertFalse( isset( $set[ 'nope' ] ) );
        /** @phpstan-ignore isset.offset, staticMethod.impossibleType */
        self::assertTrue( isset( $set[ [ 'foo', 'baz' ] ] ) );
        /** @phpstan-ignore isset.offset, staticMethod.alreadyNarrowedType */
        self::assertFalse( isset( $set[ [ 'foo', 'quux' ] ] ) );
        /** @phpstan-ignore isset.offset, staticMethod.alreadyNarrowedType */
        self::assertFalse( isset( $set[ [ 'foo', 'grault' ] ] ) );
        /** @phpstan-ignore isset.offset, staticMethod.alreadyNarrowedType */
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
        /** @noinspection PhpConditionAlreadyCheckedInspection */
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


    public function testOffsetUnset() : void {
        $set = new ParameterSet( [ 'foo' => 'bar' ], [ 'baz' => 'qux' ] );
        $set->setMutable( true );
        unset( $set[ 'foo' ] );
        /** @noinspection PhpConditionAlreadyCheckedInspection */
        self::assertFalse( isset( $set[ 'foo' ] ) );
        self::expectException( InvalidArgumentException::class );
        unset( $set[ null ] );
    }


    public function testOffsetUnsetForImmutable() : void {
        $set = new ParameterSet( [ 'foo' => 'bar' ], [ 'baz' => 'qux' ] );
        self::expectException( LogicException::class );
        unset( $set[ 'foo' ] );
    }


    public function testSetDefault() : void {
        $set = new ParameterSet( i_itAllowedKeys: [ 'foo' ] );
        $set->setDefault( 'foo', 'bar' );
        self::assertSame( 'bar', $set->get( 'foo' )->asString() );

        $set->setDefault( 'baz', 'qux' );
        self::assertNull( $set->get( 'baz' ) );

        $set->setMutable( true );
        $set->setDefault( 'foo', 'quux' );
        self::assertSame( 'quux', $set->get( 'foo' )->asString() );

        $set->setMutable( false );
        self::expectException( LogicException::class );
        $set->setDefault( 'foo', 'corge' );

    }


    public function testSubsetByKeyPrefix() : void {
        $set = new ParameterSet(
            [ 'a:1' => 'foo', 'a:2' => 'bar', 'a:3' => 'baz', 'b:1' => 'qux' ],
            [ 'a:2' => 'quux', 'a:3' => 'corge', 'b:2' => 'grault' ],
            [ 'a:1', 'a:2', 'b:1', 'b:2' ]
        );
        $subset = $set->subsetByKeyPrefix( 'a:' );
        self::assertSame( [ 'a:1', 'a:2' ], $subset->getAllowedKeys() );
        self::assertSame( [ 'a:1', 'a:2' ], $subset->listKeys() );
    }


    public function testSubsetByKeys() : void {
        $set = new ParameterSet(
            [ 'a:1' => 'foo', 'a:2' => 'bar', 'a:3' => 'baz', 'b:1' => 'qux' ],
            [ 'a:2' => 'quux', 'a:3' => 'corge', 'b:2' => 'grault' ],
            [ 'a:1', 'a:2', 'b:1', 'b:2' ]
        );
        $subset = $set->subsetByKeys( fn( string $stKey ) => str_ends_with( $stKey, ':1' ) );
        self::assertSame( [ 'a:1', 'b:1' ], $subset->getAllowedKeys() );
        self::assertSame( [ 'a:1', 'b:1' ], $subset->listKeys() );
    }


    public function testUnset() : void {
        $set = new ParameterSet( [ 'foo' => 'bar', 'baz' => 'quux' ], [ 'baz' => 'qux' ] );
        self::assertTrue( $set->has( 'foo' ) );
        $set->setMutable( true );
        $set->unset( 'foo' );
        self::assertFalse( $set->has( 'foo' ) );
        self::assertSame( 'quux', $set->get( 'baz' )->asString() );
        $set->unset( 'baz' );
        self::assertSame( 'qux', $set->get( 'baz' )->asString() );
        $set->unset( 'baz', true );
        self::assertNull( $set->get( 'baz' ) );
        $set->setMutable( false );
        self::expectException( LogicException::class );
        $set->unset( 'foo' );
    }


}
