<?php


declare( strict_types = 1 );


namespace JDWX\Param;


use Ds\Map;
use Ds\Set;
use Generator;
use InvalidArgumentException;
use LogicException;


/**
 * A collection of named parameters with support for defaults, allowed keys, and filtering.
 * 
 * ParameterSet provides a type-safe way to manage collections of parameters,
 * commonly used for handling web request data, configuration options, or database query results.
 * It supports default values, key filtering, and various subset operations.
 */
class ParameterSet implements IParameterSet {


    /**
     * Map of parameter keys to their values.
     * @var Map<string, IParameter>
     */
    private Map $mapParameters;

    /**
     * Map of parameter keys to their default values.
     * @var Map<string, IParameter>
     */
    private Map $mapDefaults;

    /**
     * Set of keys that are allowed in this parameter set.
     * @var Set<string>
     */
    private Set $setAllowedKeys;

    /**
     * Set of keys that were ignored due to not being in the allowed keys.
     * @var Set<string>
     */
    private Set $setIgnoredKeys;

    /**
     * Whether this parameter set can be modified after creation.
     * @var bool
     */
    private bool $bMutable = false;


    /**
     * Creates a new ParameterSet with optional initial parameters, defaults, and allowed keys.
     * 
     * @param iterable<int|string, list<string|IParameter>|string|IParameter>|null $i_itParameters Initial parameters
     * @param iterable<int|string, list<string|IParameter>|string|IParameter>|null $i_itDefaults Default values
     * @param iterable<int|string>|null $i_itAllowedKeys Keys that are allowed (null means all keys allowed)
     * @noinspection PhpDocSignatureInspection iterable<whatever> is broken in PhpStorm
     */
    public function __construct( ?iterable $i_itParameters = null, ?iterable $i_itDefaults = null,
                                 ?iterable $i_itAllowedKeys = null ) {
        $this->setIgnoredKeys = new Set();
        $this->setAllowedKeys = new Set();
        $this->addAllowedKeys( $i_itAllowedKeys );
        $this->mapParameters = new Map();
        $this->addParameters( $i_itParameters );
        $this->mapDefaults = new Map();
        $this->addDefaults( $i_itDefaults );
    }


    /**
     * Adds a key to the set of allowed keys.
     * 
     * @param string $i_stKey The key to allow
     * @return void
     */
    public function addAllowedKey( string $i_stKey ) : void {
        $this->setAllowedKeys->add( $i_stKey );
    }


    /**
     * Adds multiple keys to the set of allowed keys.
     * 
     * @param iterable<int|string>|null $i_itAllowedKeys The keys to allow
     * @return void
     * @noinspection PhpDocSignatureInspection iterable<whatever> is broken in PhpStorm
     */
    public function addAllowedKeys( ?iterable $i_itAllowedKeys = null ) : void {
        if ( ! $i_itAllowedKeys ) {
            return;
        }
        foreach ( $i_itAllowedKeys as $stKey ) {
            $this->addAllowedKey( strval( $stKey ) );
        }
    }


    /**
     * Adds a default value for a key (throws exception if default already exists).
     * 
     * @param string $i_stKey The parameter key
     * @param mixed[]|string|IParameter|null $i_xValue The default value
     * @return void
     * @throws InvalidArgumentException If a default for this key already exists
     */
    public function addDefault( string $i_stKey, array|string|IParameter|null $i_xValue = null ) : void {
        if ( $this->mapDefaults->hasKey( $i_stKey ) ) {
            throw new InvalidArgumentException( "Adding key default already present in set: {$i_stKey}" );
        }
        $this->setDefault( $i_stKey, $i_xValue );
    }


    /**
     * Adds multiple default values (throws exception if any defaults already exist).
     * 
     * @param iterable<int|string, mixed[]|string|IParameter|null>|null $i_itDefaults The defaults to add
     * @return void
     * @throws InvalidArgumentException If any defaults for the keys already exist
     * @noinspection PhpDocSignatureInspection iterable<whatever> is broken in PhpStorm
     */
    public function addDefaults( ?iterable $i_itDefaults = null ) : void {
        if ( ! $i_itDefaults ) {
            return;
        }
        foreach ( $i_itDefaults as $stKey => $xValue ) {
            $this->addDefault( strval( $stKey ), $xValue );
        }
    }


    /**
     * Adds a parameter value (throws exception if parameter already exists).
     * 
     * @param string $i_stKey The parameter key
     * @param mixed[]|string|IParameter|null $i_xValue The parameter value
     * @return void
     * @throws InvalidArgumentException If a parameter for this key already exists
     */
    public function addParameter( string $i_stKey, array|string|IParameter|null $i_xValue = null ) : void {
        if ( $this->mapParameters->hasKey( $i_stKey ) ) {
            throw new InvalidArgumentException( "Adding key already present in set: {$i_stKey}" );
        }
        $this->setParameter( $i_stKey, $i_xValue );
    }


    /**
     * Adds multiple parameters (throws exception if any parameters already exist).
     * 
     * @param iterable<int|string, mixed[]|string|IParameter|null>|null $i_itParameters The parameters to add
     * @return void
     * @throws InvalidArgumentException If any parameters for the keys already exist
     * @noinspection PhpDocSignatureInspection iterable<whatever> is broken in PhpStorm
     */
    public function addParameters( ?iterable $i_itParameters = null ) : void {
        if ( ! $i_itParameters ) {
            return;
        }
        foreach ( $i_itParameters as $stKey => $xValue ) {
            $this->addParameter( strval( $stKey ), $xValue );
        }
    }


    /**
     * Gets a parameter by key, falling back to defaults and then the provided default.
     * 
     * @param string $i_stKey The parameter key
     * @param mixed $i_xDefault The default value if parameter and default are not found
     * @return IParameter|null The parameter, or null if not found and no default provided
     */
    public function get( string $i_stKey, mixed $i_xDefault = null ) : ?IParameter {
        if ( $this->isKeyAllowed( $i_stKey ) ) {
            return Parameter::coerce( $this->mapParameters->get(
                $i_stKey,
                $this->mapDefaults->get( $i_stKey, $i_xDefault )
            ) );
        }
        return Parameter::coerce( $i_xDefault );
    }


    /**
     * Returns the list of allowed keys, or null if all keys are allowed.
     * 
     * @return list<string>|null The allowed keys, or null if no restrictions
     */
    public function getAllowedKeys() : ?array {
        return $this->setAllowedKeys->toArray();
    }


    /**
     * Gets a parameter by key, throwing an exception if not found.
     * 
     * @param string $i_stKey The parameter key
     * @return IParameter The parameter
     * @throws InvalidArgumentException If the key is not found
     */
    public function getEx( string $i_stKey ) : IParameter {
        $np = $this->get( $i_stKey );
        if ( $np instanceof IParameter ) {
            return $np;
        }
        throw new InvalidArgumentException( "Key not found in ParameterSet: {$i_stKey}" );
    }


    /**
     * Returns the list of keys that were ignored due to not being in allowed keys.
     * 
     * @return list<string> The ignored keys
     */
    public function getIgnoredKeys() : array {
        return $this->setIgnoredKeys->toArray();
    }


    /**
     * Returns all parameter values as an associative array.
     * 
     * Arrays are recursively unwrapped, other values are returned as-is.
     * 
     * @return array<int|string, mixed[]|string|null> The parameter values
     */
    public function getValues() : array {
        $r = [];
        foreach ( $this->iter() as $stKey => $pValue ) {
            if ( $pValue->isArray() ) {
                $r[ $stKey ] = $pValue->asArrayRecursiveOrNull();
            } else {
                $r[ $stKey ] = $pValue->getValue();
            }
        }
        return $r;
    }


    /**
     * Checks if all specified keys exist (either as parameters or defaults).
     * 
     * @param string ...$i_rstKeys The keys to check
     * @return bool True if all keys exist, false otherwise
     */
    public function has( string ...$i_rstKeys ) : bool {
        foreach ( $i_rstKeys as $stKey ) {
            if ( ! $this->isKeyAllowed( $stKey ) ) {
                return false;
            }
            if ( $this->mapParameters->hasKey( $stKey ) ) {
                continue;
            }
            if ( $this->mapDefaults->hasKey( $stKey ) ) {
                continue;
            }
            return false;
        }
        return true;
    }


    /**
     * Returns a generator that yields all key-parameter pairs.
     * 
     * @return Generator<string, IParameter> Generator yielding key => parameter pairs
     */
    public function iter() : Generator {
        foreach ( $this->listKeys() as $stKey ) {
            yield $stKey => $this->getEx( $stKey );
        }
    }


    /**
     * Returns the parameter set as an array suitable for JSON serialization.
     * 
     * @return array<int|string, mixed[]|bool|float|int|string|null> The JSON-serializable array
     */
    public function jsonSerialize() : array {
        $r = [];
        /** @noinspection PhpLoopCanBeConvertedToArrayMapInspection */
        foreach ( $this->iter() as $stKey => $pValue ) {
            $r[ $stKey ] = $pValue->jsonSerialize();
        }
        return $r;
    }


    /**
     * Returns a list of all available keys (parameters + defaults, filtered by allowed keys).
     * 
     * @return list<string> The available keys
     */
    public function listKeys() : array {
        $keys = $this->mapParameters->keys();
        $keys = $keys->merge( $this->mapDefaults->keys() );
        if ( ! $this->setAllowedKeys->isEmpty() ) {
            $keys = $keys->intersect( $this->setAllowedKeys );
        }
        if ( $keys->isEmpty() ) {
            return [];
        }
        $keys = $keys->toArray();
        /** @phpstan-ignore-next-line */
        assert( is_array( $keys ) && is_string( $keys[ array_key_first( $keys ) ] ) );
        return $keys;
    }


    /**
     * Checks whether an offset exists (ArrayAccess interface).
     * 
     * @param mixed $offset The offset to check (string key or array of keys)
     * @return bool True if the offset(s) exist, false otherwise
     */
    public function offsetExists( mixed $offset ) : bool {
        if ( is_null( $offset ) ) {
            return false;
        }
        if ( ! is_array( $offset ) ) {
            return $this->has( $offset );
        }
        return $this->has( ... $offset );
    }


    /**
     * Returns the parameter at the specified offset (ArrayAccess interface).
     * 
     * @param mixed $offset The offset to retrieve (must be a string key)
     * @return IParameter The parameter at the specified offset
     * @throws InvalidArgumentException If the offset is null or the key is not found
     */
    public function offsetGet( mixed $offset ) : IParameter {
        if ( is_null( $offset ) ) {
            throw new InvalidArgumentException( 'Null key not allowed in ParameterSet.' );
        }
        return $this->getEx( $offset );
    }


    /**
     * Sets the parameter at the specified offset (ArrayAccess interface).
     * 
     * @param mixed $offset The offset to set (must be a string key)
     * @param mixed $value The parameter value to set
     * @return void
     * @throws LogicException If the offset is null
     */
    public function offsetSet( mixed $offset, mixed $value ) : void {
        if ( is_null( $offset ) ) {
            throw new LogicException( 'Cannot append to ParameterSet without key.' );
        }
        $this->setParameter( $offset, $value );
    }


    /**
     * Unsets the parameter at the specified offset (ArrayAccess interface).
     * 
     * @param mixed $offset The offset to unset (string key or array of keys)
     * @return void
     * @throws InvalidArgumentException If the offset is null
     * @throws LogicException If the set is not mutable
     */
    public function offsetUnset( mixed $offset ) : void {
        if ( is_null( $offset ) ) {
            throw new InvalidArgumentException( 'Cannot unset ParameterSet without key.' );
        }
        $this->unset( $offset );
    }


    /**
     * Sets or updates a default value for a key.
     * 
     * @param string $i_stKey The parameter key
     * @param mixed[]|string|IParameter|null $i_xValue The default value
     * @return void
     * @throws LogicException If trying to reset a default in an immutable set
     */
    public function setDefault( string $i_stKey, array|string|IParameter|null $i_xValue ) : void {
        if ( ! $this->isKeyAllowed( $i_stKey ) ) {
            $this->setIgnoredKeys->add( $i_stKey );
            return;
        }
        if ( $this->mapDefaults->hasKey( $i_stKey ) && ! $this->bMutable ) {
            throw new LogicException( "Cannot reset default in immutable set: {$i_stKey}" );
        }
        if ( ! $i_xValue instanceof Parameter ) {
            $i_xValue = new Parameter( $i_xValue );
        }
        $this->mapDefaults->put( $i_stKey, $i_xValue );
    }


    /**
     * Sets the mutability state of this parameter set.
     * 
     * @param bool $i_bMutable Whether the parameter set should be mutable
     * @return void
     */
    public function setMutable( bool $i_bMutable ) : void {
        $this->bMutable = $i_bMutable;
    }


    /**
     * Sets or updates a parameter value.
     * 
     * @param string $i_stKey The parameter key
     * @param mixed[]|string|IParameter|null $i_xValue The parameter value
     * @return void
     * @throws InvalidArgumentException If trying to set a parameter in an immutable set
     */
    public function setParameter( string $i_stKey, array|string|IParameter|null $i_xValue ) : void {
        if ( ! $this->isKeyAllowed( $i_stKey ) ) {
            $this->setIgnoredKeys->add( $i_stKey );
            return;
        }
        if ( $this->mapParameters->hasKey( $i_stKey ) && ! $this->bMutable ) {
            throw new InvalidArgumentException( "Key already present in immutable set: {$i_stKey}" );
        }
        if ( ! $i_xValue instanceof IParameter ) {
            $i_xValue = new Parameter( $i_xValue );
        }
        $this->mapParameters->put( $i_stKey, $i_xValue );
    }


    /**
     * Creates a subset containing only parameters with keys that start with the given prefix.
     * 
     * @param string $i_stPrefix The key prefix to filter by
     * @return static A new ParameterSet containing only matching parameters
     */
    public function subsetByKeyPrefix( string $i_stPrefix ) : static {
        return $this->subsetByKeys( fn( string $stKey ) => str_starts_with( $stKey, $i_stPrefix ) );
    }


    /**
     * Creates a subset containing only parameters whose keys match the filter function.
     * 
     * @param callable(string): bool $i_fnFilter Function that returns true for keys to include
     * @return static A new ParameterSet containing only matching parameters
     */
    public function subsetByKeys( callable $i_fnFilter ) : static {
        /** @phpstan-ignore new.static */
        $set = new static( i_itAllowedKeys: $this->setAllowedKeys->filter( $i_fnFilter ) );
        foreach ( $this->mapParameters as $stKey => $rParameter ) {
            if ( $i_fnFilter( $stKey ) ) {
                $set->setParameter( $stKey, $rParameter );
            }
        }
        foreach ( $this->mapDefaults as $stKey => $rParameter ) {
            if ( $i_fnFilter( $stKey ) ) {
                $set->setDefault( $stKey, $rParameter );
            }
        }
        $set->setMutable( $this->bMutable );
        return $set;
    }


    /**
     * Creates a subset containing only parameters with keys that match the regular expression.
     * 
     * @param string $i_reFilter The regular expression to match keys against
     * @return static A new ParameterSet containing only matching parameters
     */
    public function subsetByRegExp( string $i_reFilter ) : static {
        return $this->subsetByKeys( fn( string $stKey ) => preg_match( $i_reFilter, $stKey ) );
    }


    /**
     * Returns which of the specified keys actually exist in this parameter set.
     * 
     * @param string ...$i_rstKeys The keys to test
     * @return list<string> The keys that exist (subset of the input keys)
     */
    public function testKeys( string ...$i_rstKeys ) : array {
        return array_values( array_intersect( $i_rstKeys, $this->listKeys() ) );
    }


    /**
     * Removes parameters (and optionally their defaults) from the set.
     * 
     * @param string|iterable<string> $i_keys The key(s) to remove
     * @param bool $i_bAlsoDropDefault Whether to also remove default values
     * @return void
     * @throws LogicException If the set is not mutable
     * @noinspection PhpDocSignatureInspection
     */
    public function unset( string|iterable $i_keys, bool $i_bAlsoDropDefault = false ) : void {
        if ( ! $this->bMutable ) {
            throw new LogicException( 'Cannot drop keys in immutable set' );
        }
        if ( is_string( $i_keys ) ) {
            $i_keys = [ $i_keys ];
        }
        foreach ( $i_keys as $stKey ) {
            if ( $this->mapParameters->hasKey( $stKey ) ) {
                $this->mapParameters->remove( $stKey );
            }
            if ( $i_bAlsoDropDefault && $this->mapDefaults->hasKey( $stKey ) ) {
                $this->mapDefaults->remove( $stKey );
            }
        }
    }


    /**
     * Checks if a key is allowed based on the allowed keys set.
     * 
     * @param string $i_stKey The key to check
     * @return bool True if the key is allowed (or if no allowed keys are set), false otherwise
     */
    private function isKeyAllowed( string $i_stKey ) : bool {
        return $this->setAllowedKeys->isEmpty() || $this->setAllowedKeys->contains( $i_stKey );
    }


}
