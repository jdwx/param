<?php


declare( strict_types = 1 );


use JDWX\Param\Parse;
use JDWX\Param\ParseException;
use PHPUnit\Framework\TestCase;


final class ParseTest extends TestCase {


    public function testConstant() : void {
        self::assertSame( 'foo', Parse::constant( 'foo', 'foo' ) );
        self::expectException( ParseException::class );
        Parse::constant( 'foo', 'bar' );
    }


    public function testCurrency() : void {
        self::assertSame( 123456, Parse::currency( '1234.56' ) );
        self::assertSame( -123456, Parse::currency( '-1234.56' ) );
        self::assertSame( 123456, Parse::currency( '1,234.56' ) );
        self::assertSame( -123456, Parse::currency( '-1,234.56' ) );
        self::assertSame( 123456, Parse::currency( '$1234.56' ) );
        self::assertSame( -123456, Parse::currency( '-$1234.56' ) );
        self::assertSame( -123456, Parse::currency( '$-1234.56' ) );
        self::assertSame( 123456, Parse::currency( '$1,234.56' ) );
        self::assertSame( -123456, Parse::currency( '-$1,234.56' ) );
        self::assertSame( -123456, Parse::currency( '(1234.56)' ) );
        self::assertSame( -123456, Parse::currency( '(1,234.56)' ) );
        self::assertSame( -123456, Parse::currency( '($1234.56)' ) );
        self::assertSame( -123456, Parse::currency( '($1,234.56)' ) );
        self::expectException( ParseException::class );
        Parse::currency( '(-$1,234.56)' );
    }


    public function testDate() : void {
        self::assertSame( '2024-01-25', Parse::date( '2024-01-25 12:34:56' ) );
        self::assertSame( '2025-03-02', Parse::date( 'March 2, 2025 3:53 PM' ) );
        self::assertSame( '2024-01-28', Parse::date( '2024-01-26 + 2 days' ) );
        self::expectException( ParseException::class );
        Parse::date( 'foo' );
    }


    public function testDateTime() : void {
        self::assertSame( '2024-01-25 12:34:56', Parse::dateTime( '2024-01-25 12:34:56' ) );
        self::assertSame( '2025-03-02 15:53:00', Parse::dateTime( 'March 2, 2025 3:53 PM' ) );
        self::assertSame( '2024-01-28 00:00:00', Parse::dateTime( '2024-01-26 + 2 days' ) );
        self::expectException( ParseException::class );
        Parse::dateTime( 'foo' );
    }


    public function testGlob() : void {
        $r = Parse::glob( __DIR__ . '/*.php' );
        self::assertContains( __FILE__, $r );

        self::expectException( ParseException::class );
        Parse::glob( __DIR__ . '/*.foo', GLOB_ERR );
    }


    public function testSummarizeOptions() : void {
        $r = [ 'a', 'b', 'c', 'd', 'e' ];
        self::assertEquals( 'a, b, c, d, e', Parse::summarizeOptions( $r ) );

        $r = [ 'a', 'b', 'c' ];
        self::assertEquals( 'a, b, c', Parse::summarizeOptions( $r ) );

        $r = [];
        self::assertEquals( '(no options)', Parse::summarizeOptions( $r ) );

        $r = [ 'a', 'b', 'c', 'd', 'e', 'f', 'g', 'h', 'i', 'j' ];
        self::assertEquals( 'a, b, c, d, ...', Parse::summarizeOptions( $r ) );
    }


    public function testTime() : void {
        self::assertSame( '12:34:56', Parse::time( '2024-01-25 12:34:56' ) );
        self::assertSame( '15:53:00', Parse::time( 'March 2, 2025 3:53 PM' ) );
        self::assertSame( '00:00:00', Parse::time( '2024-01-26 + 2 days' ) );
        self::expectException( ParseException::class );
        Parse::time( 'foo' );
    }


}
