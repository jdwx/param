<?php


declare( strict_types = 1 );


namespace JDWX\Param;


use Ds\Map;
use Ds\Set;
use Generator;
use InvalidArgumentException;
use LogicException;


class ParameterSet implements IParameterSet {


    /** @var Map<string, IParameter> */
    private Map $mapParameters;

    /** @var Map<string, IParameter> */
    private Map $mapDefaults;

    /** @var Set<string> */
    private Set $setAllowedKeys;

    /** @var Set<string> */
    private Set $setIgnoredKeys;

    private bool $bMutable = false;


    /**
     * @param iterable<int|string, list<string|IParameter>|string|IParameter>|null $i_itParameters
     * @param iterable<int|string, list<string|IParameter>|string|IParameter>|null $i_itDefaults
     * @param iterable<int|string>|null $i_itAllowedKeys
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


    public function addAllowedKey( string $i_stKey ) : void {
        $this->setAllowedKeys->add( $i_stKey );
    }


    /**
     * @param iterable<int|string>|null $i_itAllowedKeys
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


    /** @param mixed[]|string|IParameter|null $i_xValue */
    public function addDefault( string $i_stKey, array|string|IParameter|null $i_xValue = null ) : void {
        if ( $this->mapDefaults->hasKey( $i_stKey ) ) {
            throw new InvalidArgumentException( "Adding key default already present in set: {$i_stKey}" );
        }
        $this->setDefault( $i_stKey, $i_xValue );
    }


    /**
     * @param iterable<int|string, mixed[]|string|IParameter|null>|null $i_itDefaults
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


    /** @param mixed[]|string|IParameter|null $i_xValue */
    public function addParameter( string $i_stKey, array|string|IParameter|null $i_xValue = null ) : void {
        if ( $this->mapParameters->hasKey( $i_stKey ) ) {
            throw new InvalidArgumentException( "Adding key already present in set: {$i_stKey}" );
        }
        $this->setParameter( $i_stKey, $i_xValue );
    }


    /**
     * @param iterable<int|string, mixed[]|string|IParameter|null>|null $i_itParameters
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


    public function get( string $i_stKey, mixed $i_xDefault = null ) : ?IParameter {
        if ( $this->isKeyAllowed( $i_stKey ) ) {
            return Parameter::coerce( $this->mapParameters->get(
                $i_stKey,
                $this->mapDefaults->get( $i_stKey, $i_xDefault )
            ) );
        }
        return Parameter::coerce( $i_xDefault );
    }


    /** @return list<string>|null */
    public function getAllowedKeys() : ?array {
        return $this->setAllowedKeys->toArray();
    }


    public function getEx( string $i_stKey ) : IParameter {
        $np = $this->get( $i_stKey );
        if ( $np instanceof IParameter ) {
            return $np;
        }
        throw new InvalidArgumentException( "Key not found in ParameterSet: {$i_stKey}" );
    }


    /** @return list<string> */
    public function getIgnoredKeys() : array {
        return $this->setIgnoredKeys->toArray();
    }


    /** @return array<int|string, string|list<string>> */
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


    /** @param string ...$i_rstKeys */
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


    /** @return Generator<string, IParameter> */
    public function iter() : Generator {
        foreach ( $this->listKeys() as $stKey ) {
            yield $stKey => $this->getEx( $stKey );
        }
    }


    /** @return array<int|string, mixed[]|bool|float|int|string|null> */
    public function jsonSerialize() : array {
        $r = [];
        /** @noinspection PhpLoopCanBeConvertedToArrayMapInspection */
        foreach ( $this->iter() as $stKey => $pValue ) {
            $r[ $stKey ] = $pValue->jsonSerialize();
        }
        return $r;
    }


    /** @return list<string> */
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


    /** @param list<string>|string|null $offset */
    public function offsetExists( mixed $offset ) : bool {
        if ( is_null( $offset ) ) {
            return false;
        }
        if ( ! is_array( $offset ) ) {
            return $this->has( $offset );
        }
        return $this->has( ... $offset );
    }


    /** @param string|null $offset */
    public function offsetGet( mixed $offset ) : IParameter {
        if ( is_null( $offset ) ) {
            throw new InvalidArgumentException( 'Null key not allowed in ParameterSet.' );
        }
        return $this->getEx( $offset );
    }


    /**
     * @param string|null $offset
     * @param mixed[]|string|IParameter|null $value
     * @return void
     */
    public function offsetSet( mixed $offset, mixed $value ) : void {
        if ( is_null( $offset ) ) {
            throw new LogicException( 'Cannot append to ParameterSet without key.' );
        }
        $this->setParameter( $offset, $value );
    }


    /**
     * @param list<string>|string|null $offset
     */
    public function offsetUnset( mixed $offset ) : void {
        if ( is_null( $offset ) ) {
            throw new InvalidArgumentException( 'Cannot unset ParameterSet without key.' );
        }
        $this->unset( $offset );
    }


    /** @param mixed[]|string|IParameter|null $i_xValue */
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


    public function setMutable( bool $i_bMutable ) : void {
        $this->bMutable = $i_bMutable;
    }


    /** @param mixed[]|string|IParameter|null $i_xValue */
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


    public function subsetByKeyPrefix( string $i_stPrefix ) : static {
        return $this->subsetByKeys( fn( string $stKey ) => str_starts_with( $stKey, $i_stPrefix ) );
    }


    /** @param callable(string) : bool $i_fnFilter */
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


    public function subsetByRegExp( string $i_reFilter ) : static {
        return $this->subsetByKeys( fn( string $stKey ) => preg_match( $i_reFilter, $stKey ) );
    }


    /**
     * @param string ...$i_rstKeys
     * @return list<string>
     */
    public function testKeys( string ...$i_rstKeys ) : array {
        return array_values( array_intersect( $i_rstKeys, $this->listKeys() ) );
    }


    /**
     * @param string|iterable<string> $i_keys
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


    private function isKeyAllowed( string $i_stKey ) : bool {
        return $this->setAllowedKeys->isEmpty() || $this->setAllowedKeys->contains( $i_stKey );
    }


}
