<?php


declare( strict_types = 1 );


namespace JDWX\Param;


class StringParameter extends Parameter {


    public function __construct( string|null|Parameter $xValue = null ) {
        parent::__construct( $xValue, true, 0 );
    }


    public function isSet() : bool {
        return $this->isString();
    }


    /** @param mixed[]|string|null $xValue */
    public function new( array|string|null $xValue ) : static {
        /** @phpstan-ignore new.static */
        return new static( $xValue );
    }


}
