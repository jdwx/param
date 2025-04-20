<?php


declare( strict_types = 1 );


namespace JDWX\Param;


use InvalidArgumentException;
use LogicException;
use OutOfBoundsException;
use Stringable;
use TypeError;


/**
 * Parameter is suitable to encapsulate a value that is received as a string or array (or NULL)
 * from a web request or database query.  It is used to validate and safely convert inputs to a more
 * useful type.
 */
class Parameter implements IParameter, Stringable {


    /** @var list<int|string> */
    private array $rKeys = [];

    private bool $bMutable = false;

    private bool $bSet = false;

    private bool $bFrozen = false;

    /** @var mixed[]|string|null */
    private array|string|null $xValue;


    /**
     * @param mixed[]|string|IParameter|null $xValue The value to encapsulate.
     * @param bool $bAllowNull If true, null is allowed as a value. If false, an exception is thrown when null
     *                         is encountered.
     * @param int|null $nuAllowArrayDepth If not null, the number of levels of array nesting that are allowed.
     *                                    0 means arrays are not allowed, 1 means flat arrays are allowed,
     *                                    2 means arrays of arrays are allowed, etc.
     */
    public function __construct( array|bool|float|int|string|IParameter|null $xValue,
                                 private readonly bool                       $bAllowNull = true,
                                 private readonly ?int                       $nuAllowArrayDepth = null ) {
        $this->_set( $xValue );
    }


    /**
     * @param mixed $i_xValue
     * @return IParameter|null
     *
     * Makes sure a value is a Parameter or null. Useful for
     * handling arbitrary default values while passing
     * through Parameter values unmodified.
     */
    public static function coerce( mixed $i_xValue ) : ?IParameter {
        if ( is_null( $i_xValue ) || $i_xValue instanceof IParameter ) {
            return $i_xValue;
        }
        /** @phpstan-ignore new.static */
        return new static( $i_xValue );
    }


    /**
     * @param mixed[]|IParameter $i_xValue
     * @return mixed[]
     */
    private static function unwrap( array|IParameter $i_xValue ) : array {
        if ( $i_xValue instanceof IParameter ) {
            $i_xValue = $i_xValue->asArray();
        }
        $r = [];
        foreach ( $i_xValue as $k => $v ) {
            if ( is_array( $v ) || ( $v instanceof IParameter && $v->isArray() ) ) {
                $r[ $k ] = self::unwrap( $v );
            } elseif ( $v instanceof IParameter ) {
                $r[ $k ] = $v->getValue();
            } else {
                $r[ $k ] = $v;
            }
        }
        return $r;
    }


    public function __toString() : string {
        return $this->asString();
    }


    /** @return mixed[] */
    public function asArray( ?string $i_nstError = null ) : array {
        if ( is_array( $this->xValue ) ) {
            return $this->xValue;
        }
        throw new TypeError( $i_nstError ?? 'Parameter is not an array.' );
    }


    /** @return mixed[]|null */
    public function asArrayOrNull() : ?array {
        if ( $this->isNull() ) {
            return null;
        }
        return $this->asArray( 'Parameter is not an array or null.' );
    }


    /** @return mixed[]|string */
    public function asArrayOrString() : array|string {
        if ( is_array( $this->xValue ) ) {
            return $this->xValue;
        }
        return $this->asString();
    }


    public function asArrayRecursive( ?string $i_nstError = null ) : array {
        return self::unwrap( $this->asArray( $i_nstError ) );
    }


    public function asArrayRecursiveOrNull() : ?array {
        if ( $this->isNull() ) {
            return null;
        }
        return $this->asArrayRecursive();
    }


    public function asArrayRecursiveOrString() : array|string {
        if ( is_array( $this->xValue ) ) {
            return self::unwrap( $this->xValue );
        }
        return $this->asString();
    }


    /** asBool() treats null as false. If you need them separate, use asBoolOrNull(). */
    public function asBool() : bool {
        $nst = $this->asStringOrNull();
        if ( is_null( $nst ) ) {
            return false;
        }
        return Parse::bool( $nst, "Parameter is not a boolean: {$nst}" );
    }


    /**
     * Unlike the other asTypeOrNull() methods, asBoolOrNull() really only changes
     * how a null value is handled; asBoolOrNull() returns null, whereas asBool()
     * coerces null to false. So asBoolOrNull() is more of a tri-bool.
     */
    public function asBoolOrNull() : ?bool {
        if ( $this->isNull() ) {
            return null;
        }
        return $this->asBool();
    }


    public function asConstant( string $i_stConstant ) : string {
        $st = $this->asString();
        return Parse::constant( $st, $i_stConstant, "Parameter is not the constant value '{$i_stConstant}': {$st}" );
    }


    public function asCurrency() : int {
        $st = $this->asString();
        return Parse::currency( $st, "Parameter is not a currency amount: {$st}" );
    }


    public function asCurrencyOrEmpty() : ?int {
        if ( $this->isEmptyString() ) {
            return null;
        }
        $st = $this->asString();
        return Parse::currency( $st, "Parameter is not a currency amount or empty: {$st}" );
    }


    public function asCurrencyOrNull() : ?int {
        if ( $this->isNull() ) {
            return null;
        }
        return $this->asCurrency();
    }


    public function asDate() : string {
        $st = $this->asString();
        return Parse::date( $st, "Parameter is not a date: {$st}" );
    }


    public function asDateOrNull() : ?string {
        if ( $this->isNull() ) {
            return null;
        }
        return $this->asDate();
    }


    public function asDateTime() : string {
        $st = $this->asString();
        return Parse::dateTime( $st, "Parameter is not a date and time: {$st}" );
    }


    public function asDateTimeOrNull() : ?string {
        if ( $this->isNull() ) {
            return null;
        }
        return $this->asDateTime();
    }


    public function asEmailAddress() : string {
        $st = $this->asString();
        return Parse::emailAddress( $st, "Parameter is not an email address: {$st}" );
    }


    public function asEmailAddressOrEmpty() : ?string {
        if ( $this->isEmptyString() ) {
            return null;
        }
        $st = $this->asString();
        return Parse::emailAddress( $st, "Parameter is not an email address or empty: {$st}" );
    }


    public function asEmailAddressOrNull() : ?string {
        if ( $this->isNull() ) {
            return null;
        }
        $st = $this->asString();
        return Parse::emailAddress( $st, "Parameter is not an email address or null: {$st}" );
    }


    public function asEmailUsername() : string {
        $st = $this->asString();
        return Parse::emailUsername( $st, "Parameter is not an email username: {$st}" );
    }


    public function asEmailUsernameOrEmpty() : ?string {
        if ( $this->isEmptyString() ) {
            return null;
        }
        $st = $this->asString();
        return Parse::emailUsername( $st, "Parameter is not an email username or empty: {$st}" );
    }


    public function asEmailUsernameOrNull() : ?string {
        if ( $this->isNull() ) {
            return null;
        }
        $st = $this->asString();
        return Parse::emailUsername( $st, "Parameter is not an email username or null: {$st}" );
    }


    public function asExistingDirectory() : string {
        $st = $this->asString();
        return Parse::existingDirectory( $st, "Parameter is not an existing directory: {$st}" );
    }


    public function asExistingDirectoryOrEmpty() : ?string {
        if ( $this->isEmptyString() ) {
            return null;
        }
        $st = $this->asString();
        return Parse::existingDirectory( $st, "Parameter is not an existing directory or empty: {$st}" );
    }


    public function asExistingDirectoryOrNull() : ?string {
        if ( $this->isNull() ) {
            return null;
        }
        $st = $this->asString();
        return Parse::existingDirectory( $st, "Parameter is not an existing directory or null: {$st}" );
    }


    public function asExistingFilename() : string {
        $st = $this->asString();
        return Parse::existingFilename( $st, "Parameter is not an existing filename: {$st}" );
    }


    public function asExistingFilenameOrEmpty() : ?string {
        if ( $this->isEmptyString() ) {
            return null;
        }
        $st = $this->asString();
        return Parse::existingFilename( $st, "Parameter is not an existing filename or empty: {$st}" );
    }


    public function asExistingFilenameOrNull() : ?string {
        if ( $this->isNull() ) {
            return null;
        }
        $st = $this->asString();
        return Parse::existingFilename( $st, "Parameter is not an existing filename or null: {$st}" );
    }


    public function asFloat() : float {
        $st = $this->asString();
        return Parse::float( $st, "Parameter is not numeric: {$st}" );
    }


    public function asFloatOrEmpty() : ?float {
        if ( $this->isEmptyString() ) {
            return null;
        }
        $st = $this->asString();
        return Parse::float( $st, "Parameter is not numeric or empty: {$st}" );
    }


    public function asFloatOrNull() : ?float {
        if ( $this->isNull() ) {
            return null;
        }
        return $this->asFloat();
    }


    public function asFloatRangeClosed( float $i_fMin, float $i_fMax ) : float {
        $st = $this->asString();
        return Parse::floatRangeClosed( $st, $i_fMin, $i_fMax, "Parameter is not in range ({$i_fMin}, {$i_fMax}): {$st}" );
    }


    public function asFloatRangeClosedOrEmpty( float $i_fMin, float $i_fMax ) : ?float {
        if ( $this->isEmptyString() ) {
            return null;
        }
        $st = $this->asString();
        return Parse::floatRangeClosed( $st, $i_fMin, $i_fMax, "Parameter is not in range ({$i_fMin}, {$i_fMax}) or empty: {$st}" );
    }


    public function asFloatRangeClosedOrNull( float $i_fMin, float $i_fMax ) : ?float {
        if ( $this->isNull() ) {
            return null;
        }
        $st = $this->asString();
        return Parse::floatRangeClosed( $st, $i_fMin, $i_fMax, "Parameter is not in range ({$i_fMin}, {$i_fMax}) or null: {$st}" );
    }


    public function asFloatRangeHalfClosed( float $i_fMin, float $i_fMax ) : float {
        $st = $this->asString();
        return Parse::floatRangeHalfClosed( $st, $i_fMin, $i_fMax, "Parameter is not in range [{$i_fMin}, {$i_fMax}): {$st}" );
    }


    public function asFloatRangeHalfClosedOrEmpty( float $i_fMin, float $i_fMax ) : ?float {
        if ( $this->isEmptyString() ) {
            return null;
        }
        $st = $this->asString();
        return Parse::floatRangeHalfClosed( $st, $i_fMin, $i_fMax, "Parameter is not in range [{$i_fMin}, {$i_fMax}) or empty: {$st}" );
    }


    public function asFloatRangeHalfClosedOrNull( float $i_fMin, float $i_fMax ) : ?float {
        if ( $this->isNull() ) {
            return null;
        }
        $st = $this->asString();
        return Parse::floatRangeHalfClosed( $st, $i_fMin, $i_fMax, "Parameter is not in range [{$i_fMin}, {$i_fMax}) or null: {$st}" );
    }


    public function asFloatRangeOpen( float $i_fMin, float $i_fMax ) : float {
        $st = $this->asString();
        return Parse::floatRangeOpen( $st, $i_fMin, $i_fMax, "Parameter is not in range [{$i_fMin}, {$i_fMax}]: {$st}" );
    }


    public function asFloatRangeOpenOrEmpty( float $i_fMin, float $i_fMax ) : ?float {
        if ( $this->isEmptyString() ) {
            return null;
        }
        $st = $this->asString();
        return Parse::floatRangeOpen( $st, $i_fMin, $i_fMax, "Parameter is not in range [{$i_fMin}, {$i_fMax}] or empty: {$st}" );
    }


    public function asFloatRangeOpenOrNull( float $i_fMin, float $i_fMax ) : ?float {
        if ( $this->isNull() ) {
            return null;
        }
        $st = $this->asString();
        return Parse::floatRangeOpen( $st, $i_fMin, $i_fMax, "Parameter is not in range [{$i_fMin}, {$i_fMax}] or null: {$st}" );
    }


    public function asHostname() : string {
        $st = $this->asString();
        return Parse::hostname( $st, "Parameter is not a hostname: {$st}" );
    }


    public function asHostnameOrEmpty() : ?string {
        if ( $this->isEmptyString() ) {
            return null;
        }
        $st = $this->asString();
        return Parse::hostname( $st, "Parameter is not a hostname or empty: {$st}" );
    }


    public function asHostnameOrNull() : ?string {
        if ( $this->isNull() ) {
            return null;
        }
        $st = $this->asString();
        return Parse::hostname( $st, "Parameter is not a hostname or null: {$st}" );
    }


    public function asIP() : string {
        $st = $this->asString();
        return Parse::ip( $st, "Parameter is not an IP address: {$st}" );
    }


    public function asIPOrEmpty() : ?string {
        if ( $this->isEmptyString() ) {
            return null;
        }
        $st = $this->asString();
        return Parse::ip( $st, "Parameter is not an IP address or empty: {$st}" );
    }


    public function asIPOrNull() : ?string {
        if ( $this->isNull() ) {
            return null;
        }
        $st = $this->asString();
        return Parse::ip( $st, "Parameter is not an IP address or null: {$st}" );
    }


    public function asIPv4() : string {
        $st = $this->asString();
        return Parse::ipv4( $st, "Parameter is not an IPv4 address: {$st}" );
    }


    public function asIPv4OrEmpty() : ?string {
        if ( $this->isEmptyString() ) {
            return null;
        }
        $st = $this->asString();
        return Parse::ipv4( $st, "Parameter is not an IPv4 address or empty: {$st}" );
    }


    public function asIPv4OrNull() : ?string {
        if ( $this->isNull() ) {
            return null;
        }
        $st = $this->asString();
        return Parse::ipv4( $st, "Parameter is not an IPv4 address or null: {$st}" );
    }


    public function asIPv6() : string {
        $st = $this->asString();
        return Parse::ipv6( $st, "Parameter is not an IPv6 address: {$st}" );
    }


    public function asIPv6OrEmpty() : ?string {
        if ( $this->isEmptyString() ) {
            return null;
        }
        $st = $this->asString();
        return Parse::ipv6( $st, "Parameter is not an IPv6 address or empty: {$st}" );
    }


    public function asIPv6OrNull() : ?string {
        if ( $this->isNull() ) {
            return null;
        }
        $st = $this->asString();
        return Parse::ipv6( $st, "Parameter is not an IPv6 address or null: {$st}" );
    }


    public function asInt() : int {
        $st = $this->asString();
        return Parse::int( $st, "Parameter is not numeric: {$st}" );
    }


    public function asIntOrEmpty() : ?int {
        if ( $this->isEmptyString() ) {
            return null;
        }
        $st = $this->asString();
        return Parse::int( $st, "Parameter is not numeric or empty: {$st}" );
    }


    public function asIntOrNull() : ?int {
        if ( $this->isNull() ) {
            return null;
        }
        return $this->asInt();
    }


    public function asIntRangeClosed( int $i_iMin, int $i_iMax ) : int {
        $st = $this->asString();
        return Parse::intRangeClosed( $st, $i_iMin, $i_iMax, "Parameter is not in range ({$i_iMin}, {$i_iMax}): {$st}" );
    }


    public function asIntRangeClosedOrEmpty( int $i_iMin, int $i_iMax ) : ?int {
        if ( $this->isEmptyString() ) {
            return null;
        }
        $st = $this->asString();
        return Parse::intRangeClosed( $st, $i_iMin, $i_iMax, "Parameter is not in range ({$i_iMin}, {$i_iMax}) or empty: {$st}" );
    }


    public function asIntRangeClosedOrNull( int $i_iMin, int $i_iMax ) : ?int {
        if ( $this->isNull() ) {
            return null;
        }
        $st = $this->asString();
        return Parse::intRangeClosed( $st, $i_iMin, $i_iMax, "Parameter is not in range ({$i_iMin}, {$i_iMax}) or null: {$st}" );
    }


    public function asIntRangeHalfClosed( int $i_iMin, int $i_iMax ) : int {
        $st = $this->asString();
        return Parse::intRangeHalfClosed( $st, $i_iMin, $i_iMax, "Parameter is not in range [{$i_iMin}, {$i_iMax}): {$st}" );
    }


    public function asIntRangeHalfClosedOrEmpty( int $i_iMin, int $i_iMax ) : ?int {
        if ( $this->isEmptyString() ) {
            return null;
        }
        $st = $this->asString();
        return Parse::intRangeHalfClosed( $st, $i_iMin, $i_iMax, "Parameter is not in range [{$i_iMin}, {$i_iMax}) or empty: {$st}" );
    }


    public function asIntRangeHalfClosedOrNull( int $i_iMin, int $i_iMax ) : ?int {
        if ( $this->isNull() ) {
            return null;
        }
        $st = $this->asString();
        return Parse::intRangeHalfClosed( $st, $i_iMin, $i_iMax, "Parameter is not in range [{$i_iMin}, {$i_iMax}) or null: {$st}" );
    }


    public function asIntRangeOpen( int $i_iMin, int $i_iMax ) : int {
        $st = $this->asString();
        return Parse::intRangeOpen( $st, $i_iMin, $i_iMax, "Parameter is not in range [{$i_iMin}, {$i_iMax}]: {$st}" );
    }


    public function asIntRangeOpenOrEmpty( int $i_iMin, int $i_iMax ) : ?int {
        if ( $this->isEmptyString() ) {
            return null;
        }
        $st = $this->asString();
        return Parse::intRangeOpen( $st, $i_iMin, $i_iMax, "Parameter is not in range [{$i_iMin}, {$i_iMax}] or empty: {$st}" );
    }


    public function asIntRangeOpenOrNull( int $i_iMin, int $i_iMax ) : ?int {
        if ( $this->isNull() ) {
            return null;
        }
        $st = $this->asString();
        return Parse::intRangeOpen( $st, $i_iMin, $i_iMax, "Parameter is not in range [{$i_iMin}, {$i_iMax}] or null: {$st}" );
    }


    /** This is primarily intended for debugging. */
    public function asJSON() : string {
        return json_encode( $this->xValue, JSON_THROW_ON_ERROR );
    }


    /** @param list<string> $i_rKeywords List of allowable keywords. */
    public function asKeyword( array $i_rKeywords ) : string {
        $st = $this->asString();
        $stKeywords = Parse::summarizeOptions( $i_rKeywords );
        return Parse::arrayValue( $st, $i_rKeywords, "Parameter is not a keyword ({$stKeywords}): {$st}" );
    }


    /** @param list<string> $i_rKeywords List of allowable keywords. */
    public function asKeywordOrEmpty( array $i_rKeywords ) : ?string {
        if ( $this->isEmptyString() ) {
            return null;
        }
        $st = $this->asString();
        $stKeywords = Parse::summarizeOptions( $i_rKeywords );
        return Parse::arrayValue( $st, $i_rKeywords, "Parameter is not a keyword or empty ({$stKeywords}): {$st}" );
    }


    /** @param list<string> $i_rKeywords List of allowable keywords. */
    public function asKeywordOrNull( array $i_rKeywords ) : ?string {
        if ( $this->isNull() ) {
            return null;
        }
        $st = $this->asString();
        $stKeywords = Parse::summarizeOptions( $i_rKeywords );
        return Parse::arrayValue( $st, $i_rKeywords, "Parameter is not a keyword or null ({$stKeywords}): {$st}" );
    }


    /**
     * @param array<string, mixed> $i_rMap
     * @return string The mapped value for the key specified by this parameter.
     */
    public function asMap( array $i_rMap ) : string {
        $st = $this->asString();
        $stKeywords = Parse::summarizeOptions( array_keys( $i_rMap ) );
        return Parse::arrayMap( $st, $i_rMap, "Parameter is not a key in the map ({$stKeywords}): {$st}" );
    }


    /**
     * @param array<string, mixed> $i_rMap
     * @return ?string The mapped value for the key specified by this parameter,
     *                 or null if the parameter is the empty string.
     */
    public function asMapOrEmpty( array $i_rMap ) : ?string {
        if ( $this->isEmptyString() ) {
            return null;
        }
        $st = $this->asString();
        $stKeywords = Parse::summarizeOptions( array_keys( $i_rMap ) );
        return Parse::arrayMap( $st, $i_rMap, "Parameter is not a key in the map or empty ({$stKeywords}): {$st}" );
    }


    /**
     * @param array<string, mixed> $i_rMap
     * @return ?string The mapped value for the key specified by this parameter, or null.
     */
    public function asMapOrNull( array $i_rMap ) : ?string {
        if ( $this->isNull() ) {
            return null;
        }
        $st = $this->asString();
        $stKeywords = Parse::summarizeOptions( array_keys( $i_rMap ) );
        return Parse::arrayMap( $st, $i_rMap, "Parameter is not a key in the map or null ({$stKeywords}): {$st}" );
    }


    public function asNonexistentFilename() : string {
        $st = $this->asString();
        return Parse::nonexistentFilename( $st, "Parameter is not a nonexistent filename: {$st}" );
    }


    public function asNonexistentFilenameOrEmpty() : ?string {
        if ( $this->isEmptyString() ) {
            return null;
        }
        $st = $this->asString();
        return Parse::nonexistentFilename( $st, "Parameter is not a nonexistent filename or empty: {$st}" );
    }


    public function asNonexistentFilenameOrNull() : ?string {
        if ( $this->isNull() ) {
            return null;
        }
        $st = $this->asString();
        return Parse::nonexistentFilename( $st, "Parameter is not a nonexistent filename or null: {$st}" );
    }


    public function asPositiveFloat() : float {
        $st = $this->asString();
        return Parse::positiveFloat( $st, "Parameter is not a positive number: {$st}" );
    }


    public function asPositiveFloatOrEmpty() : ?float {
        if ( $this->isEmptyString() ) {
            return null;
        }
        $st = $this->asString();
        return Parse::positiveFloat( $st, "Parameter is not a positive number or empty: {$st}" );
    }


    public function asPositiveFloatOrNull() : ?float {
        if ( $this->isNull() ) {
            return null;
        }
        return $this->asPositiveFloat();
    }


    public function asPositiveInt() : int {
        $st = $this->asString();
        return Parse::positiveInt( $st, "Parameter is not a positive integer: {$st}" );
    }


    public function asPositiveIntOrEmpty() : ?int {
        if ( $this->isEmptyString() ) {
            return null;
        }
        $st = $this->asString();
        return Parse::positiveInt( $st, "Parameter is not a positive integer or empty: {$st}" );
    }


    public function asPositiveIntOrNull() : ?int {
        if ( $this->isNull() ) {
            return null;
        }
        $st = $this->asString();
        return Parse::positiveInt( $st, "Parameter is not a positive integer or null: {$st}" );
    }


    public function asRoundedFloat( int $i_iPrecision = 0 ) : float {
        $st = $this->asString();
        return Parse::roundedFloat( $st, $i_iPrecision, "Parameter is not numeric: {$st}" );
    }


    public function asRoundedFloatOrEmpty( int $i_iPrecision = 0 ) : ?float {
        if ( $this->isEmptyString() ) {
            return null;
        }
        $st = $this->asString();
        return Parse::roundedFloat( $st, $i_iPrecision, "Parameter is not numeric or empty: {$st}" );
    }


    public function asRoundedFloatOrNull( int $i_iPrecision = 0 ) : ?float {
        if ( $this->isNull() ) {
            return null;
        }
        $st = $this->asString();
        return Parse::roundedFloat( $st, $i_iPrecision, "Parameter is not numeric or null: {$st}" );
    }


    public function asRoundedInt( int $i_iPrecision = 0 ) : int {
        $st = $this->asString();
        return Parse::roundedInt( $st, $i_iPrecision, "Parameter is not numeric: {$st}" );
    }


    public function asRoundedIntOrEmpty( int $i_iPrecision = 0 ) : ?int {
        if ( $this->isEmptyString() ) {
            return null;
        }
        $st = $this->asString();
        return Parse::roundedInt( $st, $i_iPrecision, "Parameter is not numeric or empty: {$st}" );
    }


    public function asRoundedIntOrNull( int $i_iPrecision = 0 ) : ?int {
        if ( $this->isNull() ) {
            return null;
        }
        return $this->asRoundedInt( $i_iPrecision );
    }


    public function asString() : string {
        if ( is_string( $this->xValue ) ) {
            return $this->xValue;
        }
        throw new TypeError( 'Parameter is not a string: ' . gettype( $this->xValue ) );
    }


    public function asStringOrNull() : ?string {
        if ( $this->isNull() ) {
            return null;
        }
        return $this->asString();
    }


    public function asTime() : string {
        $st = $this->asString();
        return Parse::time( $st, "Parameter is not a time: {$st}" );
    }


    public function asTimeOrNull() : ?string {
        if ( $this->isNull() ) {
            return null;
        }
        return $this->asTime();
    }


    public function asTimeStamp() : int {
        $st = $this->asString();
        return Parse::timeStamp( $st, "Parameter is not a timestamp: {$st}" );
    }


    public function asTimeStampOrNull() : ?int {
        if ( $this->isNull() ) {
            return null;
        }
        return $this->asTimeStamp();
    }


    public function asUnsignedFloat() : float {
        $st = $this->asString();
        return Parse::unsignedFloat( $st, "Parameter is not a non-negative number: {$st}" );
    }


    public function asUnsignedFloatOrEmpty() : ?float {
        if ( $this->isEmptyString() ) {
            return null;
        }
        $st = $this->asString();
        return Parse::unsignedFloat( $st, "Parameter is not a non-negative number or empty: {$st}" );
    }


    public function asUnsignedFloatOrNull() : ?float {
        if ( $this->isNull() ) {
            return null;
        }
        $st = $this->asString();
        return Parse::unsignedFloat( $st, "Parameter is not a non-negative number or null: {$st}" );
    }


    public function asUnsignedInt() : int {
        $st = $this->asString();
        return Parse::unsignedInt( $st, "Parameter is not a non-negative integer: {$st}" );
    }


    public function asUnsignedIntOrEmpty() : ?int {
        if ( $this->isEmptyString() ) {
            return null;
        }
        $st = $this->asString();
        return Parse::unsignedInt( $st, "Parameter is not a non-negative integer or empty: {$st}" );
    }


    public function asUnsignedIntOrNull() : ?int {
        if ( $this->isNull() ) {
            return null;
        }
        $st = $this->asString();
        return Parse::unsignedInt( $st, "Parameter is not a non-negative integer or null: {$st}" );
    }


    public function current() : Parameter {
        $this->checkArray();
        assert( is_array( $this->xValue ) );
        return $this->child( $this->xValue[ current( $this->rKeys ) ] );
    }


    /** @return mixed[]|string|null */
    public function getValue() : array|string|null {
        return $this->xValue;
    }


    public function has( int|string $key ) : bool {
        $this->checkArray();
        assert( is_array( $this->xValue ) );
        return array_key_exists( $key, $this->xValue );
    }


    /** @param mixed[]|string|null $default */
    public function indexOrDefault( int|string $key, array|string|null $default ) : Parameter {
        $x = $this->indexOrNull( $key );
        if ( $x instanceof Parameter ) {
            return $x;
        }
        return new Parameter( $default, $this->bAllowNull, $this->nuAllowArrayDepth );
    }


    public function indexOrNull( int|string $key ) : ?Parameter {
        if ( ! $this->has( $key ) ) {
            return null;
        }
        assert( is_array( $this->xValue ) );
        return $this->child( $this->xValue[ $key ] );
    }


    /**
     * @param mixed[]|int|string|float|bool|IParameter|null $i_xValue
     * @return bool
     */
    public function is( array|int|string|float|bool|null|IParameter $i_xValue ) : bool {

        # If the value is a Parameter, get its value. (Recursively, if an array.)
        if ( $i_xValue instanceof IParameter ) {
            if ( $i_xValue->isArray() ) {
                $i_xValue = $i_xValue->asArrayRecursive();
            } else {
                $i_xValue = $i_xValue->getValue();
            }
        }

        # Nulls only match each other.
        if ( $this->isNull() ) {
            return $i_xValue === null;
        }
        if ( is_null( $i_xValue ) ) {
            return false;
        }

        # Bools match only the allowable boolean values.
        # NOTE: This does include all numeric values.
        if ( is_bool( $i_xValue ) ) {
            if ( ! $this->isBool() ) {
                return false;
            }
            return $this->asBool() === $i_xValue;
        }

        # Arrays are compared recursively.
        if ( is_array( $i_xValue ) ) {
            if ( ! $this->isArray() ) {
                return false;
            }
            return $this->asArrayRecursive() == $i_xValue;
        }

        # Options remaining are int, string, and float. These
        # can be directly compared with ==.
        return $this->getValue() == $i_xValue;
    }


    public function isArray() : bool {
        return is_array( $this->xValue );
    }


    public function isBool() : bool {
        if ( is_array( $this->xValue ) ) {
            return false;
        }
        if ( is_null( $this->xValue ) ) {
            return true;
        }
        return Validate::bool( strval( $this->xValue ) );
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


    public function isEmptyString() : bool {
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


    /** @return mixed[]|string|null */
    public function jsonSerialize() : array|string|null {
        return $this->xValue;
    }


    public function key() : int|string {
        $this->checkArray();
        return current( $this->rKeys );
    }


    /** @param mixed[]|bool|float|int|string|IParameter|null $xValue */
    public function new( array|bool|float|int|string|IParameter|null $xValue ) : static {
        /** @phpstan-ignore new.static */
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
        /** @phpstan-ignore booleanAnd.alwaysFalse, function.alreadyNarrowedType */
        if ( ! is_int( $offset ) && ! is_string( $offset ) ) {
            throw new InvalidArgumentException( 'Parameter key is not an integer or string.' );
        }
        return $this->has( $offset );
    }


    /**
     * @suppress PhanTypeMismatchDeclaredParamNullable
     * @param int|string $offset
     */
    public function offsetGet( mixed $offset ) : Parameter {
        /** @phpstan-ignore booleanAnd.alwaysFalse, function.alreadyNarrowedType */
        if ( ! is_int( $offset ) && ! is_string( $offset ) ) {
            throw new InvalidArgumentException( 'Parameter key is not an integer or string.' );
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
     * @param Parameter|mixed[]|string|null $value
     */
    public function offsetSet( mixed $offset, mixed $value ) : void {
        throw new LogicException( 'Parameter is immutable.' );
    }


    /**
     * @suppress PhanTypeMismatchDeclaredParamNullable
     * @param int|string $offset
     */
    public function offsetUnset( mixed $offset ) : void {
        throw new LogicException( 'Parameter is immutable.' );
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
            throw new LogicException( 'Parameter is frozen.' );
        }
        $this->bMutable = $i_bMutable;
    }


    /** @param mixed[]|bool|float|int|string|IParameter|null $i_xValue */
    protected function _set( array|bool|float|int|string|IParameter|null $i_xValue ) : void {
        if ( $this->bSet && ! $this->bMutable ) {
            throw new LogicException( 'Parameter is immutable.' );
        }
        if ( $i_xValue instanceof IParameter ) {
            $i_xValue = $i_xValue->getValue();
        }
        if ( is_null( $i_xValue ) && ! $this->bAllowNull ) {
            throw new InvalidArgumentException( 'Parameter value is null but null is not allowed.' );
        }
        if ( is_array( $i_xValue ) ) {
            if ( is_int( $this->nuAllowArrayDepth ) && $this->nuAllowArrayDepth <= 0 ) {
                throw new InvalidArgumentException( 'Array parameter is nested too deep.' );
            }
            $this->rKeys = array_keys( $i_xValue );
        }
        if ( is_bool( $i_xValue ) ) {
            $i_xValue = $i_xValue ? 'true' : 'false';
        }
        if ( is_float( $i_xValue ) || is_int( $i_xValue ) ) {
            $i_xValue = (string) $i_xValue;
        }
        $this->xValue = $i_xValue;
        $this->bSet = true;
    }


    private function checkArray() : void {
        if ( ! is_array( $this->xValue ) ) {
            throw new TypeError( 'Parameter is not an array.' );
        }
    }


    /** @param mixed[]|bool|float|int|string|IParameter|null $i_xValue */
    private function child( array|bool|float|int|string|IParameter|null $i_xValue ) : Parameter {
        $nuNewArrayDepth = $this->nuAllowArrayDepth;
        if ( is_int( $nuNewArrayDepth ) && $nuNewArrayDepth > 0 ) {
            $nuNewArrayDepth--;
        }
        return new Parameter( $i_xValue, $this->bAllowNull, $nuNewArrayDepth );
    }


}
