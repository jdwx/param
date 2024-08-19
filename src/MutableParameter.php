<?php


declare( strict_types = 1 );


namespace JDWX\Param;


class MutableParameter extends Parameter {


    public function __construct( array|bool|float|int|string|IParameter|null $xValue = null ) {
        parent::__construct( $xValue );
        $this->_mutate( true );
    }


    public function freeze() : void {
        $this->_freeze();
    }


    public function new( array|bool|float|int|string|IParameter|null $xValue ) : static {
        /** @phpstan-ignore new.static */
        return new static( $xValue );
    }


    /** @param mixed[]|bool|float|int|string|IParameter|null $i_xValue */
    public function set( array|bool|float|int|string|IParameter|null $i_xValue ) : void {
        $this->_set( $i_xValue );
    }


}