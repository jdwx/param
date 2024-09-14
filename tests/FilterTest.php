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


}
