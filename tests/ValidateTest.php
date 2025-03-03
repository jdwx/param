<?php


declare( strict_types = 1 );


use JDWX\Param\Validate;
use PHPUnit\Framework\TestCase;


class ValidateTest extends TestCase {


    public function testBool() : void {
        self::assertTrue( Validate::bool( 'true' ) );
        self::assertTrue( Validate::bool( 'yes' ) );
        self::assertTrue( Validate::bool( 'yeah' ) );
        self::assertTrue( Validate::bool( 'y' ) );
        self::assertTrue( Validate::bool( 'on' ) );
        self::assertTrue( Validate::bool( '1' ) );
        self::assertTrue( Validate::bool( '2' ) );

        self::assertTrue( Validate::bool( 'false' ) );
        self::assertTrue( Validate::bool( 'no' ) );
        self::assertTrue( Validate::bool( 'nope' ) );
        self::assertTrue( Validate::bool( 'n' ) );
        self::assertTrue( Validate::bool( 'off' ) );
        self::assertTrue( Validate::bool( '0' ) );

        self::assertFalse( Validate::bool( '' ) );
        self::assertFalse( Validate::bool( 'foo' ) );
        self::assertFalse( Validate::bool( '!' ) );

        self::assertTrue( Validate::bool( 'TRUE' ) );
    }


    public function testCurrency() : void {
        self::assertTrue( Validate::currency( '1234.56' ) );
        self::assertTrue( Validate::currency( '-1234.56' ) );
        self::assertTrue( Validate::currency( '1,234.56' ) );
        self::assertTrue( Validate::currency( '-1,234.56' ) );
        self::assertTrue( Validate::currency( '$1234.56' ) );
        self::assertTrue( Validate::currency( '-$1234.56' ) );
        self::assertTrue( Validate::currency( '$-1234.56' ) );
        self::assertTrue( Validate::currency( '$1,234.56' ) );
        self::assertTrue( Validate::currency( '-$1,234.56' ) );
        self::assertTrue( Validate::currency( '(1234.56)' ) );
        self::assertTrue( Validate::currency( '(1,234.56)' ) );
        self::assertTrue( Validate::currency( '($1234.56)' ) );
        self::assertTrue( Validate::currency( '($1,234.56)' ) );
        self::assertFalse( Validate::currency( '(-$1,234.56)' ) );
        self::assertTrue( Validate::currency( '1234' ) );
    }


    public function testDateTime() : void {
        self::assertTrue( Validate::dateTime( '2024-01-25' ) );
        self::assertTrue( Validate::dateTime( '2024-01-26 + 2 days' ) );
        self::assertFalse( Validate::dateTime( 'foo' ) );
    }


}
