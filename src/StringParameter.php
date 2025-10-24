<?php


declare( strict_types = 1 );


namespace JDWX\Param;


/**
 * A parameter optimized for string-only values.
 * 
 * StringParameter extends the base Parameter class with restrictions that prevent
 * array values (array depth is set to 0). It allows null values and is optimized
 * for scenarios where you only expect string data.
 */
class StringParameter extends Parameter {


    /**
     * Creates a new string parameter with the specified value.
     * 
     * Arrays are not allowed (array depth is set to 0). Null values are allowed.
     * Boolean, integer, and float values are converted to strings.
     * 
     * @param bool|int|float|string|IParameter|null $xValue The initial value
     */
    public function __construct( bool|int|float|string|IParameter|null $xValue = null ) {
        parent::__construct( $xValue, true, 0 );
    }


    /**
     * Checks if this parameter has been set with a string value.
     * 
     * Unlike the parent class which always returns true, this method
     * only returns true if the parameter contains an actual string value.
     * 
     * @return bool True if the parameter contains a string value, false otherwise
     */
    public function isSet() : bool {
        return $this->isString();
    }


    /**
     * Creates a new string parameter with the specified value.
     * 
     * @param mixed[]|bool|int|float|string|IParameter|null $xValue The value for the new parameter
     * @return static A new StringParameter instance with the specified value
     */
    public function new( array|bool|int|float|string|IParameter|null $xValue ) : static {
        /** @phpstan-ignore new.static */
        return new static( $xValue );
    }


}
