<?php


declare( strict_types = 1 );


namespace JDWX\Param;


use ArrayAccess;
use Ds\Map;
use InvalidArgumentException;
use LogicException;


/** @implements ArrayAccess<string, Parameter> */
class ParameterSet implements ArrayAccess {


    /** @var Map<string, Parameter> */
    private Map $mapParameters;

    /** @var Map<string, Parameter> */
    private Map $mapDefaults;

    /** @var list<string>|null */
    private ?array $nrAllowedKeys = null;

    /** @var list<string> */
    private array $rIgnoredKeys = [];

    private bool $bMutable = false;


    /**
     * @param iterable<string, string>|null $i_itParameters
     * @param iterable<string, string>|null $i_itDefaults
     * @param iterable<string>|null $i_itAllowedKeys
     * @noinspection PhpDocSignatureInspection iterable<whatever> is broken in PhpStorm
     */
    public function __construct( ?iterable $i_itParameters = null, ?iterable $i_itDefaults = null,
                                 ?iterable $i_itAllowedKeys = null ) {
        $this->addAllowedKeys( $i_itAllowedKeys );
        $this->mapParameters = new Map();
        $this->addParameters( $i_itParameters );
        $this->mapDefaults = new Map();
        $this->addDefaults( $i_itDefaults );
    }


    public function addAllowedKey( string $i_stKey ) : void {
        if ( ! $this->nrAllowedKeys ) {
            $this->nrAllowedKeys = [];
        }
        if ( ! in_array( $i_stKey, $this->nrAllowedKeys ) ) {
            $this->nrAllowedKeys[] = $i_stKey;
        }
    }


    /**
     * @param iterable<string>|null $i_itAllowedKeys
     * @noinspection PhpDocSignatureInspection iterable<whatever> is broken in PhpStorm
     */
    public function addAllowedKeys( ?iterable $i_itAllowedKeys = null ) : void {
        if ( ! $i_itAllowedKeys ) {
            return;
        }
        foreach ( $i_itAllowedKeys as $stKey ) {
            $this->addAllowedKey( $stKey );
        }
    }


    /** @param mixed[]|string|Parameter|null $i_xValue */
    public function addDefault( string $i_stKey, array|string|Parameter|null $i_xValue = null ) : void {
        if ( ! $i_xValue instanceof Parameter ) {
            $i_xValue = new Parameter( $i_xValue );
        }
        $this->mapDefaults->put( $i_stKey, $i_xValue );
    }


    /**
     * @param iterable<string, mixed[]|string|Parameter|null>|null $i_itDefaults
     * @noinspection PhpDocSignatureInspection iterable<whatever> is broken in PhpStorm
     */
    public function addDefaults( ?iterable $i_itDefaults = null ) : void {
        if ( ! $i_itDefaults ) {
            return;
        }
        foreach ( $i_itDefaults as $stKey => $xValue ) {
            $this->addDefault( $stKey, $xValue );
        }
    }


    /** @param mixed[]|string|Parameter|null $i_xValue */
    public function addParameter( string $i_stKey, array|string|Parameter|null $i_xValue = null ) : void {
        if ( $this->mapParameters->hasKey( $i_stKey ) ) {
            throw new InvalidArgumentException( "Adding key already present in set: {$i_stKey}" );
        }
        $this->setParameter( $i_stKey, $i_xValue );
    }


    /**
     * @param iterable<string, mixed[]|string|Parameter|null>|null $i_itParameters
     * @noinspection PhpDocSignatureInspection iterable<whatever> is broken in PhpStorm
     */
    public function addParameters( ?iterable $i_itParameters = null ) : void {
        if ( ! $i_itParameters ) {
            return;
        }
        foreach ( $i_itParameters as $stKey => $xValue ) {
            $this->addParameter( $stKey, $xValue );
        }
    }


    public function get( string $i_stKey, mixed $i_xDefault = null ) : ?Parameter {
        if ( ! $this->isKeyAllowed( $i_stKey ) ) {
            return $i_xDefault;
        }
        return $this->mapParameters->get(
            $i_stKey,
            $this->mapDefaults->get( $i_stKey, $i_xDefault )
        );
    }


    public function getEx( string $i_stKey ) : Parameter {
        $np = $this->get( $i_stKey );
        if ( $np instanceof Parameter ) {
            return $np;
        }
        throw new InvalidArgumentException( "Key not found in ParameterSet: {$i_stKey}" );
    }


    /** @return list<string> */
    public function getIgnoredKeys() : array {
        return $this->rIgnoredKeys;
    }


    /** @param string ...$i_rstKeys */
    public function has( string ...$i_rstKeys ) : bool {
        foreach ( $i_rstKeys as $stKey ) {
            if ( is_array( $this->nrAllowedKeys ) && ! in_array( $stKey, $this->nrAllowedKeys ) ) {
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


    /** @return list<string> */
    public function listKeys() : array {
        $keys = $this->mapParameters->keys();
        return $keys->merge( $this->mapDefaults->keys() )->toArray();
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
    public function offsetGet( mixed $offset ) : Parameter {
        if ( is_null( $offset ) ) {
            throw new InvalidArgumentException( 'Null key not allowed in ParameterSet.' );
        }
        return $this->getEx( $offset );
    }


    /**
     * @param string|null $offset
     * @param mixed[]|string|Parameter|null $value
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
        if ( ! is_array( $offset ) ) {
            $offset = [ $offset ];
        }
        foreach ( $offset as $stKey ) {
            $this->mapParameters->remove( $stKey );
        }
    }


    public function setMutable( bool $i_bMutable ) : void {
        $this->bMutable = $i_bMutable;
    }


    /** @param mixed[]|string|Parameter|null $i_xValue */
    public function setParameter( string $i_stKey, array|string|Parameter|null $i_xValue ) : void {
        if ( ! $this->isKeyAllowed( $i_stKey ) ) {
            $this->rIgnoredKeys[] = $i_stKey;
            return;
        }
        if ( $this->mapParameters->hasKey( $i_stKey ) && ! $this->bMutable ) {
            throw new InvalidArgumentException( "Key already present in immutable set: {$i_stKey}" );
        }
        if ( ! $i_xValue instanceof Parameter ) {
            $i_xValue = new Parameter( $i_xValue );
        }
        $this->mapParameters->put( $i_stKey, $i_xValue );
    }


    public function subsetByKeys( callable $i_fnFilter ) : static {
        /** @phpstan-ignore new.static */
        $set = new static();
        foreach ( $this->mapParameters as $stKey => $rParameter ) {
            if ( $i_fnFilter( $stKey ) ) {
                $set->setParameter( $stKey, $rParameter );
            }
        }
        foreach ( $this->mapDefaults as $stKey => $rParameter ) {
            if ( $i_fnFilter( $stKey ) ) {
                $set->addDefault( $stKey, $rParameter );
            }
        }
        if ( $this->nrAllowedKeys ) {
            $rAllowedKeys = array_filter( $this->nrAllowedKeys, $i_fnFilter );
            $set->addAllowedKeys( $rAllowedKeys );
        }
        $set->setMutable( $this->bMutable );
        return $set;
    }


    /**
     * @param string ...$i_rstKeys
     * @return list<string>
     */
    public function testKeys( string ...$i_rstKeys ) : array {
        return array_values( array_intersect( $i_rstKeys, $this->listKeys() ) );
    }


    private function isKeyAllowed( string $i_stKey ) : bool {
        if ( ! $this->nrAllowedKeys ) {
            return true;
        }
        return in_array( $i_stKey, $this->nrAllowedKeys );
    }


}
