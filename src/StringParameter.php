<?php


declare( strict_types = 1 );


namespace JDWX\Param;


class StringParameter extends Parameter {


    public function __construct( bool|int|float|string|IParameter|null $xValue = null ) {
        parent::__construct( $xValue, true, 0 );
    }


    public function isSet() : bool {
        return $this->isString();
    }


    /** @param mixed[]|bool|int|float|string|IParameter|null $xValue */
    public function new( array|bool|int|float|string|IParameter|null $xValue ) : static {
        /** @phpstan-ignore new.static */
        return new static( $xValue );
    }


}
