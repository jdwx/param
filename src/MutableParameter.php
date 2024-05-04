<?php


declare( strict_types = 1 );


namespace JDWX\Param;


class MutableParameter extends StringParameter {


    public function __construct( string|null $xValue = null ) {
        parent::__construct( $xValue );
        $this->_mutate( true );
    }


    public function freeze() : void {
        $this->_freeze();
    }


    public function new( array|string|null $xValue ) : static {
        return new static( $xValue );
    }


    public function set( string $i_stValue ) : void {
        $this->_set( $i_stValue );
    }


}