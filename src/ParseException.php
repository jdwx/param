<?php


declare( strict_types = 1 );


namespace JDWX\Param;


use TypeError;


/**
 * Exception thrown when a parameter value cannot be parsed or converted.
 * 
 * ParseException extends TypeError and is thrown by the Parse class methods
 * when string values cannot be converted to the expected type. This provides
 * more specific error handling for parameter parsing failures while maintaining
 * compatibility with PHP's type system.
 * 
 * @see Parse For methods that throw this exception
 */
class ParseException extends TypeError {


}
