<?php


declare( strict_types = 1 );


namespace JDWX\Param;


use InvalidArgumentException;
use LogicException;


/**
 * A parameter that can be modified after creation.
 * 
 * MutableParameter extends the base Parameter class to allow value changes
 * after instantiation. It starts in a mutable state and can be frozen to
 * become permanently immutable.
 */
class MutableParameter extends Parameter {


    /**
     * Creates a new mutable parameter with the specified value.
     * 
     * @param mixed[]|bool|float|int|string|IParameter|null $xValue The initial value
     */
    public function __construct( array|bool|float|int|string|IParameter|null $xValue = null ) {
        parent::__construct( $xValue );
        $this->_mutate( true );
    }


    /**
     * Freezes this parameter, making it permanently immutable.
     * 
     * Once frozen, the parameter cannot be modified or made mutable again.
     * 
     * @return void
     */
    public function freeze() : void {
        $this->_freeze();
    }


    /**
     * Creates a new mutable parameter with the specified value.
     * 
     * @param mixed[]|bool|float|int|string|IParameter|null $xValue The value for the new parameter
     * @return static A new MutableParameter instance with the specified value
     */
    public function new( array|bool|float|int|string|IParameter|null $xValue ) : static {
        /** @phpstan-ignore new.static */
        return new static( $xValue );
    }


    /**
     * Sets the value of this mutable parameter.
     * 
     * @param mixed[]|bool|float|int|string|IParameter|null $i_xValue The new value
     * @return void
     * @throws LogicException If the parameter is frozen
     * @throws InvalidArgumentException If null is provided but not allowed, or array depth exceeds limits
     */
    public function set( array|bool|float|int|string|IParameter|null $i_xValue ) : void {
        $this->_set( $i_xValue );
    }


}