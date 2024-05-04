<?php


declare( strict_types = 1 );


namespace JDWX\Param;


interface IParameter {


    public function __toString() : string;


    public function asArray() : array;


    public function asArrayOrNull() : ?array;


    public function asArrayOrString() : array|string;


    public function asBool() : bool;


    public function asBoolOrNull() : ?bool;


    public function asCurrency() : int;


    public function asCurrencyOrEmpty() : ?int;


    public function asCurrencyOrNull() : ?int;


    public function asFloat() : float;


    public function asFloatOrEmpty() : ?float;


    public function asFloatOrNull() : ?float;


    public function asInt() : int;


    /** Returns the integer value or null for the empty string. */
    public function asIntOrEmpty() : ?int;

    public function asIntOrNull() : ?int;


    public function asJSON() : string;


    public function asRoundedFloat( int $i_iPrecision = 0 ) : float;


    public function asRoundedFloatOrEmpty( int $i_iPrecision = 0 ) : ?float;


    public function asRoundedFloatOrNull( int $i_iPrecision = 0 ) : ?float;


    public function asRoundedInt( int $i_iPrecision = 0 ) : int;


    public function asRoundedIntOrEmpty( int $i_iPrecision = 0 ) : ?int;


    public function asRoundedIntOrNull( int $i_iPrecision = 0 ) : ?int;


    public function asString() : string;


    public function asStringOrNull() : ?string;


    /**
     * @param int|string $key
     * @return bool True if this is an array that has the given key, otherwise false.
     *
     * If this parameter is not an array, disaster will ensue.
     */
    public function has( int|string $key ) : bool;


    /**
     * @param int|string $key
     * @param array|string|null $default
     * @return IParameter Returns a parameter containing the value for a given key,
     *                    or the specified default value if the key is not set.
     *
     * If this parameter is not an array, disaster will ensue.
     */
    public function indexOrDefault( int|string $key, array|string|null $default ) : IParameter;


    /**
     * @param int|string $key
     * @return IParameter|null Returns a parameter containing the value for a given key,
     *                         or null if the key is not set.
     *
     * If this parameter is not an array, disaster will ensue.
     */
    public function indexOrNull( int|string $key ) : ?IParameter;


    public function isArray() : bool;


    /**
     * Returns true if the value is null, an empty array, or an empty string.
     * This is useful for interacting with parameters that can be optionally
     * left blank.
     */
    public function isEmpty() : bool;


    public function isNull(): bool;


    public function isSet() : bool;


    public function isString() : bool;


    public function new( array|string|null $xValue ) : static;


}

