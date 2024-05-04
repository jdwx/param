<?php


declare( strict_types = 1 );


use JDWX\Param\Parameter as ParameterAlias;


class MyTestParameter extends ParameterAlias {


    public function freeze() : void {
        $this->_freeze();
    }

    public function mutate( bool $i_bMutable ) : void {
        parent::_mutate( $i_bMutable );
    }


    public function set( string $i_st ) : void {
        $this->_set( $i_st );
    }


}
