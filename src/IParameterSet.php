<?php


declare( strict_types = 1 );


namespace JDWX\Param;


use ArrayAccess;
use Generator;
use JsonSerializable;


/**
 * @suppress PhanAccessWrongInheritanceCategoryInternal
 * @extends ArrayAccess<string, IParameter>
 */
interface IParameterSet extends ArrayAccess, JsonSerializable {


    public function addAllowedKey( string $i_stKey ) : void;


    /**
     * @param iterable<string>|null $i_itAllowedKeys
     * @noinspection PhpDocSignatureInspection iterable<whatever> is broken in PhpStorm
     */
    public function addAllowedKeys( ?iterable $i_itAllowedKeys = null ) : void;


    /** @param mixed[]|string|IParameter|null $i_xValue */
    public function addDefault( string $i_stKey, array|string|IParameter|null $i_xValue = null ) : void;


    /**
     * @param iterable<string, mixed[]|string|IParameter|null>|null $i_itDefaults
     * @noinspection PhpDocSignatureInspection iterable<whatever> is broken in PhpStorm
     */
    public function addDefaults( ?iterable $i_itDefaults = null ) : void;


    /** @param mixed[]|string|IParameter|null $i_xValue */
    public function addParameter( string $i_stKey, array|string|IParameter|null $i_xValue = null ) : void;


    /**
     * @param iterable<string, mixed[]|string|IParameter|null>|null $i_itParameters
     * @noinspection PhpDocSignatureInspection iterable<whatever> is broken in PhpStorm
     */
    public function addParameters( ?iterable $i_itParameters = null ) : void;


    public function get( string $i_stKey, mixed $i_xDefault = null ) : ?IParameter;


    /** @return list<string>|null */
    public function getAllowedKeys() : ?array;


    public function getEx( string $i_stKey ) : IParameter;


    /** @return list<string> */
    public function getIgnoredKeys() : array;


    /**
     * @return array<int|string, string|list<string>> All values, de-parameterized.
     *
     * Suitable for displaying in debug output or serializing.
     */
    public function getValues() : array;


    /** @param string ...$i_rstKeys */
    public function has( string ...$i_rstKeys ) : bool;


    public function iter() : Generator;


    /** @return list<string> */
    public function listKeys() : array;


    /** @param list<string>|string|null $offset */
    public function offsetExists( mixed $offset ) : bool;


    /** @param string|null $offset */
    public function offsetGet( mixed $offset ) : IParameter;


    /**
     * @param string|null $offset
     * @param mixed[]|string|Parameter|null $value
     * @return void
     */
    public function offsetSet( mixed $offset, mixed $value ) : void;


    /**
     * @param iterable<string>|string|null $offset
     */
    public function offsetUnset( mixed $offset ) : void;


    /** @param mixed[]|string|Parameter|null $i_xValue */
    public function setDefault( string $i_stKey, array|string|Parameter|null $i_xValue ) : void;


    public function setMutable( bool $i_bMutable ) : void;


    /** @param mixed[]|string|Parameter|null $i_xValue */
    public function setParameter( string $i_stKey, array|string|Parameter|null $i_xValue ) : void;


    public function subsetByKeyPrefix( string $i_stPrefix ) : static;


    /** @param callable(string) : bool $i_fnFilter */
    public function subsetByKeys( callable $i_fnFilter ) : static;


    public function subsetByRegExp( string $i_reFilter ) : static;


    /**
     * @param string ...$i_rstKeys
     * @return list<string>
     */
    public function testKeys( string ...$i_rstKeys ) : array;


    /**
     * @param string|iterable<string> $i_keys The key (or keys) to remove from the set
     * @param bool $i_bAlsoDropDefault If true, any default value for the key will also be removed.
     * @return void
     *
     * Drops the parameter from the set. If the parameter is not in the set, it does nothing.
     * @noinspection PhpDocSignatureInspection
     */
    public function unset( string|iterable $i_keys, bool $i_bAlsoDropDefault = false ) : void;


}