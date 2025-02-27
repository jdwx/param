<?php


declare( strict_types = 1 );


namespace JDWX\Param;


use ArrayAccess;
use Iterator;
use JsonSerializable;


/**
 * A parameter is suitable to encapsulate a value that is received as a string or array (or NULL)
 * from a web request or database query.  It is used to validate and safely convert inputs to a more
 * useful type.
 *
 * @suppress PhanAccessWrongInheritanceCategoryInternal
 * @extends ArrayAccess<int|string, IParameter|string|array|null>
 * @extends Iterator<int|string, IParameter>
 */
interface IParameter extends ArrayAccess, Iterator, JsonSerializable {


    public function __toString() : string;


    /** @return mixed[] */
    public function asArray() : array;


    /** @return mixed[]|null */
    public function asArrayOrNull() : ?array;


    /** @return mixed[]|string */
    public function asArrayOrString() : array|string;


    /** @return mixed[] */
    public function asArrayRecursive() : array;


    /** @return mixed[]|null */
    public function asArrayRecursiveOrNull() : ?array;


    /** @return mixed[]|string */
    public function asArrayRecursiveOrString() : array|string;


    public function asBool() : bool;


    public function asBoolOrNull() : ?bool;


    /** Requires the parameter to be exactly the given value. */
    public function asConstant( string $i_stConstant ) : string;


    public function asCurrency() : int;


    public function asCurrencyOrEmpty() : ?int;


    public function asCurrencyOrNull() : ?int;


    public function asEmailAddress() : string;


    public function asEmailAddressOrEmpty() : ?string;


    public function asEmailAddressOrNull() : ?string;


    public function asEmailUsername() : string;


    public function asEmailUsernameOrEmpty() : ?string;


    public function asEmailUsernameOrNull() : ?string;


    public function asExistingDirectory() : string;


    public function asExistingDirectoryOrEmpty() : ?string;


    public function asExistingDirectoryOrNull() : ?string;


    public function asExistingFilename() : string;


    public function asExistingFilenameOrEmpty() : ?string;


    public function asExistingFilenameOrNull() : ?string;


    public function asFloat() : float;


    public function asFloatOrEmpty() : ?float;


    public function asFloatOrNull() : ?float;


    public function asFloatRangeClosed( float $i_fMin, float $i_fMax ) : float;


    public function asFloatRangeClosedOrEmpty( float $i_fMin, float $i_fMax ) : ?float;


    public function asFloatRangeClosedOrNull( float $i_fMin, float $i_fMax ) : ?float;


    public function asFloatRangeHalfClosed( float $i_fMin, float $i_fMax ) : float;


    public function asFloatRangeHalfClosedOrEmpty( float $i_fMin, float $i_fMax ) : ?float;


    public function asFloatRangeHalfClosedOrNull( float $i_fMin, float $i_fMax ) : ?float;


    public function asFloatRangeOpen( float $i_fMin, float $i_fMax ) : float;


    public function asFloatRangeOpenOrEmpty( float $i_fMin, float $i_fMax ) : ?float;


    public function asFloatRangeOpenOrNull( float $i_fMin, float $i_fMax ) : ?float;


    public function asHostname() : string;


    public function asHostnameOrEmpty() : ?string;


    public function asHostnameOrNull() : ?string;


    public function asIP() : string;


    public function asIPOrEmpty() : ?string;


    public function asIPOrNull() : ?string;


    public function asIPv4() : string;


    public function asIPv4OrEmpty() : ?string;


    public function asIPv4OrNull() : ?string;


    public function asIPv6() : string;


    public function asIPv6OrEmpty() : ?string;


    public function asIPv6OrNull() : ?string;


    public function asInt() : int;


    /** Returns the integer value or null for the empty string. */
    public function asIntOrEmpty() : ?int;


    public function asIntOrNull() : ?int;


    public function asIntRangeClosed( int $i_iMin, int $i_iMax ) : int;


    public function asIntRangeClosedOrEmpty( int $i_iMin, int $i_iMax ) : ?int;


    public function asIntRangeClosedOrNull( int $i_iMin, int $i_iMax ) : ?int;


    public function asIntRangeHalfClosed( int $i_iMin, int $i_iMax ) : int;


    public function asIntRangeHalfClosedOrEmpty( int $i_iMin, int $i_iMax ) : ?int;


    public function asIntRangeHalfClosedOrNull( int $i_iMin, int $i_iMax ) : ?int;


    public function asIntRangeOpen( int $i_iMin, int $i_iMax ) : int;


    public function asIntRangeOpenOrEmpty( int $i_iMin, int $i_iMax ) : ?int;


    public function asIntRangeOpenOrNull( int $i_iMin, int $i_iMax ) : ?int;


    public function asJSON() : string;


    /** @param list<string> $i_rKeywords List of allowable keywords. */
    public function asKeyword( array $i_rKeywords ) : string;


    /** @param list<string> $i_rKeywords List of allowable keywords. */
    public function asKeywordOrEmpty( array $i_rKeywords ) : ?string;


    /** @param list<string> $i_rKeywords List of allowable keywords. */
    public function asKeywordOrNull( array $i_rKeywords ) : ?string;


    /**
     * @param array<string, mixed> $i_rMap
     * @return string The mapped value for the key specified by this parameter.
     */
    public function asMap( array $i_rMap ) : string;


    /**
     * @param array<string, mixed> $i_rMap
     * @return ?string The mapped value for the key specified by this parameter,
     *                 or null if the parameter is the empty string.
     */
    public function asMapOrEmpty( array $i_rMap ) : ?string;


    /**
     * @param array<string, mixed> $i_rMap
     * @return ?string The mapped value for the key specified by this parameter, or null.
     */
    public function asMapOrNull( array $i_rMap ) : ?string;


    public function asNonexistentFilename() : string;


    public function asNonexistentFilenameOrEmpty() : ?string;


    public function asNonexistentFilenameOrNull() : ?string;


    public function asPositiveFloat() : float;


    public function asPositiveFloatOrEmpty() : ?float;


    public function asPositiveFloatOrNull() : ?float;


    public function asPositiveInt() : int;


    public function asPositiveIntOrEmpty() : ?int;


    public function asPositiveIntOrNull() : ?int;


    public function asRoundedFloat( int $i_iPrecision = 0 ) : float;


    public function asRoundedFloatOrEmpty( int $i_iPrecision = 0 ) : ?float;


    public function asRoundedFloatOrNull( int $i_iPrecision = 0 ) : ?float;


    public function asRoundedInt( int $i_iPrecision = 0 ) : int;


    public function asRoundedIntOrEmpty( int $i_iPrecision = 0 ) : ?int;


    public function asRoundedIntOrNull( int $i_iPrecision = 0 ) : ?int;


    public function asString() : string;


    public function asStringOrNull() : ?string;


    public function asUnsignedFloat() : float;


    public function asUnsignedFloatOrEmpty() : ?float;


    public function asUnsignedFloatOrNull() : ?float;


    public function asUnsignedInt() : int;


    public function asUnsignedIntOrEmpty() : ?int;


    public function asUnsignedIntOrNull() : ?int;


    /** @return mixed[]|string|null */
    public function getValue() : array|string|null;


    /**
     * @param int|string $key
     * @return bool True if this is an array that has the given key, otherwise false.
     *
     * If this parameter is not an array, disaster will ensue.
     */
    public function has( int|string $key ) : bool;


    /**
     * @param int|string $key
     * @param mixed[]|string|null $default
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


    /**
     * Provides an easy way to test whether the parameter is equal to a given
     * value without getting too hung up on types. Int, string, array, and
     * float values are compared using ==. In the case of arrays, the
     * parameter's value is recursively collapsed first. If the given value
     * is null, the parameter's value must be null to match.
     *
     * @param mixed[]|int|string|float|bool|IParameter|null $i_xValue
     * @return bool True if this parameter equals the given value.
     */
    public function is( array|int|string|float|bool|null|IParameter $i_xValue ) : bool;


    public function isArray() : bool;


    /**
     * Checks if the value is parseable as a boolean. Note that null is considered
     * parseable as a boolean (false).
     *
     * @return bool True if the value is parseable as a boolean, otherwise false.
     */
    public function isBool() : bool;


    /**
     * Returns true if the value is null, an empty array, or an empty string.
     * This is useful for interacting with parameters that can be optionally
     * left blank.
     */
    public function isEmpty() : bool;


    public function isNull() : bool;


    public function isSet() : bool;


    public function isString() : bool;


    /** @param mixed[]|string|null $xValue */
    public function new( array|string|null $xValue ) : static;


}

