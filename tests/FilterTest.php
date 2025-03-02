<?php


declare( strict_types = 1 );


use JDWX\Param\Filter;
use PHPUnit\Framework\TestCase;


class FilterTest extends TestCase {


    public function testCurrency() : void {
        self::assertSame( '-0.01', Filter::currency( '-0.01' ) );
        self::assertSame( '0.01', Filter::currency( '0.01' ) );
        self::assertSame( '0.01', Filter::currency( '.01' ) );
        self::assertSame( '-0.01', Filter::currency( '-.01' ) );
        self::assertSame( '123.45', Filter::currency( '123.45' ) );
        self::assertSame( '-123.45', Filter::currency( '-123.45' ) );
        self::assertSame( '-123.45', Filter::currency( '(123.45)' ) );
        self::assertSame( '123.45', Filter::currency( '$123.45' ) );
        self::assertSame( '1234.56', Filter::currency( '$1234.56' ) );
        self::assertSame( '1234.56', Filter::currency( '$1,234.56' ) );
        self::assertNull( Filter::currency( '$$123.45' ) );
        self::assertNull( Filter::currency( '(-123.45)' ) );
        self::assertNull( Filter::currency( '-(123.45)' ) );
        self::assertNull( Filter::currency( '(-$123.45)' ) );
        self::assertNull( Filter::currency( '$1,234.56-' ) );
    }


    public function testDate() : void {
        self::assertSame( '2024-01-25', Filter::date( '2024-01-25 12:34:56' ) );
        self::assertSame( '2025-03-02', Filter::date( 'March 2, 2025 3:53 PM' ) );
        self::assertSame( '2024-01-28', Filter::date( '2024-01-26 + 2 days' ) );
        self::assertNull( Filter::date( 'foo' ) );
    }


    public function testDateTime() : void {
        self::assertSame( '2024-01-25 12:34:56', Filter::dateTime( '2024-01-25 12:34:56' ) );
        self::assertSame( '2025-03-02 15:53:00', Filter::dateTime( 'March 2, 2025 3:53 PM' ) );
        self::assertSame( '2024-01-28 00:00:00', Filter::dateTime( '2024-01-26 + 2 days' ) );
        self::assertNull( Filter::dateTime( 'foo' ) );
    }


    public function testTime() : void {
        self::assertSame( '12:34:56', Filter::time( '2024-01-25 12:34:56' ) );
        self::assertSame( '15:53:00', Filter::time( 'March 2, 2025 3:53 PM' ) );
        self::assertSame( '00:00:00', Filter::time( '2024-01-26 + 2 days' ) );
        self::assertNull( Filter::time( 'foo' ) );
    }


}
