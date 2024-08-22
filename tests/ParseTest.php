<?php


declare( strict_types = 1 );


use JDWX\Param\Parse;
use JDWX\Param\ParseException;
use PHPUnit\Framework\TestCase;


final class ParseTest extends TestCase {


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
        static::expectException( ParseException::class );
        Parse::currency( '(-$1,234.56)' );
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


}
