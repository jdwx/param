<?php


declare( strict_types = 1 );


namespace JDWX\Param;


use ArrayAccess;
use InvalidArgumentException;
use Iterator;
use LogicException;
use OutOfBoundsException;
use Stringable;
use TypeError;


/**
 * Parameter is suitable to encapsulate a value that is received as a string or array (or NULL)
 * from a web request or database query.  It is used to validate and safely convert inputs to a more
 * useful type.
 */
class Parameter implements ArrayAccess, Iterator, IParameter, Stringable {


    private array $rKeys = [];

    private bool $bMutable = false;

    private bool $bSet = false;

    private bool $bFrozen = false;

    private array|string|null $xValue;


    /**
     * @param array|string|Parameter|null $xValue The value to encapsulate.
     * @param bool $bAllowNull If true, null is allowed as a value. If false, an exception is thrown when null
     *                         is encountered.
     * @param int|null $nuAllowArrayDepth If not null, the number of levels of array nesting that are allowed.
     *                                    0 means arrays are not allowed, 1 means flat arrays are allowed,
     *                                    2 means arrays of arrays are allowed, etc.
     */
    public function __construct( array|string|Parameter|null $xValue,
                                 private readonly bool       $bAllowNull = true,
                                 private readonly ?int       $nuAllowArrayDepth = null ) {
        $this->_set( $xValue );
    }


    public function __toString() : string {
        return $this->asString();
    }


    public function asArray() : array {
        if ( is_array( $this->xValue ) ) {
            return $this->xValue;
        }
        throw new TypeError( "Parameter is not an array." );
    }


    public function asArrayOrNull() : ?array {
        if ( is_null( $this->xValue ) ) {
            return null;
        }
        return $this->asArray();
    }


    public function asArrayOrString() : array|string {
        if ( is_array( $this->xValue ) ) {
            return $this->xValue;
        }
        return $this->asString();
    }


    /** asBool() treats null as false. If you need them separate, use asBoolOrNull(). */
    public function asBool() : bool {
        if ( is_null( $this->xValue ) ) {
            return false;
        }
        $st = $this->asString();
        if ( is_numeric( $st ) ) {
            return 0 !== (int) $st;
        }
        return match ( strtolower( $st ) ) {
            "true", "yes", "on" => true,
            "false", "no", "off" => false,
            default => throw new TypeError( "Parameter is not a boolean." ),
        };
    }


    /**
     * Unlike the other asTypeOrNull() methods, asBoolOrNull() really only changes
     * how a null value is handled; asBoolOrNull() returns null, whereas asBool()
     * coerces null to false. So asBoolOrNull() is more of a tri-bool.
     */
    public function asBoolOrNull() : ?bool {
        if ( is_null( $this->xValue ) ) {
            return null;
        }
        return $this->asBool();
    }


    public function asCurrency() : int {
        $f = $this->asRoundedFloat( 2 );
        return (int) round( $f * 100 );
    }


    public function asCurrencyOrEmpty() : ?int {
        $f = $this->asRoundedFloatOrEmpty( 2 );
        if ( is_null( $f ) ) {
            return null;
        }
        return (int) round( $f * 100 );
    }


    public function asCurrencyOrNull() : ?int {
        if ( is_null( $this->xValue ) ) {
            return null;
        }
        return $this->asCurrency();
    }


    public function asFloat() : float {
        $st = $this->asString();
        if ( is_numeric( $st ) ) {
            return (float) $st;
        }
        throw new TypeError( "Parameter is not numeric." );
    }


    public function asFloatOrEmpty() : ?float {
        $st = $this->asString();
        if ( '' === $st ) {
            return null;
        }
        if ( is_numeric( $st ) ) {
            return (float) $st;
        }
        throw new TypeError( "Parameter is not numeric or empty." );
    }


    public function asFloatOrNull() : ?float {
        if ( is_null( $this->xValue ) ) {
            return null;
        }
        return $this->asFloat();
    }


    public function asInt() : int {
        $st = $this->asString();
        if ( is_numeric( $st ) ) {
            return (int) $st;
        }
        throw new TypeError( "Parameter is not numeric." );
    }


    public function asIntOrEmpty() : ?int {
        $st = $this->asString();
        if ( '' === $st ) {
            return null;
        }
        if ( is_numeric( $st ) ) {
            return (int) $st;
        }
        throw new TypeError( "Parameter is not numeric or empty." );
    }


    public function asIntOrNull() : ?int {
        if ( is_null( $this->xValue ) ) {
            return null;
        }
        return $this->asInt();
    }


    /** This is primarily intended for debugging. */
    public function asJSON() : string {
        return json_encode( $this->xValue );
    }


    public function asRoundedFloat( int $i_iPrecision = 0 ) : float {
        return round( $this->asFloat(), $i_iPrecision );
    }


    public function asRoundedFloatOrEmpty( int $i_iPrecision = 0 ) : ?float {
        $nf = $this->asFloatOrEmpty();
        if ( is_null( $nf ) ) {
            return null;
        }
        return round( $nf, $i_iPrecision );
    }


    public function asRoundedFloatOrNull( int $i_iPrecision = 0 ) : ?float {
        if ( is_null( $this->xValue ) ) {
            return null;
        }
        return $this->asRoundedFloat( $i_iPrecision );
    }


    public function asRoundedInt( int $i_iPrecision = 0 ) : int {
        return (int) round( $this->asFloat(), $i_iPrecision );
    }


    public function asRoundedIntOrEmpty( int $i_iPrecision = 0 ) : ?int {
        $nf = $this->asFloatOrEmpty();
        if ( is_null( $nf ) ) {
            return null;
        }
        return (int) round( $nf, $i_iPrecision );
    }


    public function asRoundedIntOrNull( int $i_iPrecision = 0 ) : ?int {
        if ( is_null( $this->xValue ) ) {
            return null;
        }
        return $this->asRoundedInt( $i_iPrecision );
    }


    public function asString() : string {
        if ( is_string( $this->xValue ) )
            return $this->xValue;
        throw new TypeError( "Parameter is not a string" );
    }


    public function asStringOrNull() : ?string {
        if ( is_null( $this->xValue ) ) {
            return null;
        }
        return $this->asString();
    }


    public function current() : Parameter {
        $this->checkArray();
        assert( is_array( $this->xValue ) );
        return $this->child( $this->xValue[ current( $this->rKeys ) ] );
    }


    public function getValue() : array|string|null {
        return $this->xValue;
    }


    public function has( int|string $key ) : bool {
        $this->checkArray();
        assert( is_array( $this->xValue ) );
        return array_key_exists( $key, $this->xValue );
    }


    public function indexOrDefault( int|string $key, array|string|null $default ) : Parameter {
        $x = $this->indexOrNull( $key );
        if ( $x instanceof Parameter ) {
            return $x;
        }
        return new Parameter( $default, $this->bAllowNull, $this->nuAllowArrayDepth );
    }


    public function indexOrNull( int|string $key ) : ?Parameter {
        if ( !$this->has( $key ) ) {
            return null;
        }
        assert( is_array( $this->xValue ) );
        return $this->child( $this->xValue[ $key ] );
    }


    public function isArray() : bool {
        return is_array( $this->xValue );
    }


    public function isEmpty() : bool {
        if ( null === $this->xValue ) {
            return true;
        }
        if ( is_array( $this->xValue ) ) {
            return 0 === count( $this->xValue );
        }
        return '' === $this->xValue;
    }


    public function isFrozen() : bool {
        return $this->bFrozen;
    }


    public function isMutable() : bool {
        return $this->bMutable;
    }


    public function isNull() : bool {
        return is_null( $this->xValue );
    }


    public function isSet() : bool {
        return true;
    }


    public function isString() : bool {
        return is_string( $this->xValue );
    }


    public function key() : int|string {
        $this->checkArray();
        return current( $this->rKeys );
    }


    public function new( array|string|null $xValue ) : static {
        return new static( $xValue, $this->bAllowNull, $this->nuAllowArrayDepth );
    }


    public function next() : void {
        $this->checkArray();
        next( $this->rKeys );
    }


    /**
     * @suppress PhanTypeMismatchDeclaredParamNullable
     * @param int|string $offset
     */
    public function offsetExists( mixed $offset ) : bool {
        if ( !is_int( $offset ) && !is_string( $offset ) ) {
            throw new InvalidArgumentException( "Parameter key is not an integer or string." );
        }
        return $this->has( $offset );
    }


    /**
     * @suppress PhanTypeMismatchDeclaredParamNullable
     * @param int|string $offset
     */
    public function offsetGet( mixed $offset ) : Parameter {
        if ( !is_int( $offset ) && !is_string( $offset ) ) {
            throw new InvalidArgumentException( "Parameter key is not an integer or string." );
        }
        $x = $this->indexOrNull( $offset );
        if ( $x instanceof Parameter ) {
            return $x;
        }
        throw new OutOfBoundsException( "Parameter does not have key '{$offset}'." );
    }


    /**
     * @suppress PhanTypeMismatchDeclaredParamNullable
     * @param int|string $offset
     */
    public function offsetSet( mixed $offset, mixed $value ) : void {
        throw new LogicException( "Parameter is immutable." );
    }


    /**
     * @suppress PhanTypeMismatchDeclaredParamNullable
     * @param int|string $offset
     */
    public function offsetUnset( mixed $offset ) : void {
        throw new LogicException( "Parameter is immutable." );
    }


    public function rewind() : void {
        $this->checkArray();
        reset( $this->rKeys );
    }


    public function valid() : bool {
        $this->checkArray();
        return false !== current( $this->rKeys );
    }


    protected function _freeze() : void {
        $this->bFrozen = true;
        $this->bMutable = false;
    }


    protected function _mutate( bool $i_bMutable ) : void {
        if ( $i_bMutable && $this->bFrozen ) {
            throw new LogicException( "Parameter is frozen." );
        }
        $this->bMutable = $i_bMutable;
    }


    protected function _set( array|string|Parameter|null $xValue ) : void {
        if ( $this->bSet && !$this->bMutable ) {
            throw new LogicException( "Parameter is immutable." );
        }
        if ( $xValue instanceof Parameter ) {
            $xValue = $xValue->xValue;
        }
        if ( is_null( $xValue ) && !$this->bAllowNull ) {
            throw new InvalidArgumentException( "Parameter value is null but null is not allowed." );
        }
        if ( is_array( $xValue ) ) {
            if ( is_int( $this->nuAllowArrayDepth ) && $this->nuAllowArrayDepth <= 0 ) {
                throw new InvalidArgumentException( "Array parameter is nested too deep." );
            }
            $this->rKeys = array_keys( $xValue );
        }
        $this->xValue = $xValue;
        $this->bSet = true;
    }


    private function checkArray() : void {
        if ( !is_array( $this->xValue ) ) {
            throw new TypeError( "Parameter is not an array." );
        }
    }


    private function child( array|string|null $i_xValue ) : Parameter {
        $nuNewArrayDepth = $this->nuAllowArrayDepth;
        if ( is_int( $nuNewArrayDepth ) && $nuNewArrayDepth > 0 ) {
            $nuNewArrayDepth--;
        }
        return new Parameter( $i_xValue, $this->bAllowNull, $nuNewArrayDepth );
    }


}
