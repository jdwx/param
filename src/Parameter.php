<?php


declare( strict_types = 1 );


namespace JDWX\Param;


use InvalidArgumentException;
use JsonException;
use LogicException;
use OutOfBoundsException;
use Stringable;
use TypeError;


/**
 * Enable safe handling of untrusted values received as string, array, or null values.
 *
 * Parameter is suitable to encapsulate a value received as a string or array (or NULL)
 * such as query parameters from a web request or database query result.  It is used to
 * validate and safely convert inputs to a more useful type.
 */
class Parameter implements IParameter, Stringable {


    /**
     * Array keys for iteration support.
     * @var list<int|string>
     */
    private array $rKeys = [];

    /**
     * Whether this parameter can be modified after creation.
     * @var bool
     */
    private bool $bMutable = false;

    /**
     * Whether the value has been set.
     * @var bool
     */
    private bool $bSet = false;

    /**
     * Whether this parameter is frozen (permanently immutable).
     * @var bool
     */
    private bool $bFrozen = false;

    /**
     * The encapsulated value - can be a string, array, or null.
     * @var mixed[]|string|null
     */
    private array|string|null $xValue;


    /**
     * @param mixed[]|string|IParameter|null $xValue The value to encapsulate.
     * @param bool $bAllowNull If true, null is allowed as a value. If false, an exception is thrown when null
     *                         is encountered.
     * @param int|null $nuAllowArrayDepth If not null, the maximum levels of array nesting that are allowed.
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
     * Recursively unwraps nested IParameter objects into plain arrays.
     *
     * @param mixed[]|IParameter $i_xValue The value to unwrap
     * @return mixed[] The unwrapped array with all IParameter objects converted to their values
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


    /**
     * String representation of the parameter.
     *
     * @return string The parameter value as a string
     * @throws TypeError If the parameter is not a string
     */
    public function __toString() : string {
        if ( $this->isArray() ) {
            return $this->asJSON();
        }
        return $this->asString();
    }


    /**
     * Converts the parameter to an array.
     *
     * @param string|null $i_nstError Optional custom error message
     * @return mixed[] The parameter value as an array
     * @throws TypeError If the parameter is not an array
     */
    public function asArray( ?string $i_nstError = null ) : array {
        if ( is_array( $this->xValue ) ) {
            return $this->xValue;
        }
        throw new TypeError( $i_nstError ?? 'Parameter is not an array.' );
    }


    /**
     * Converts the parameter to an array or returns null.
     *
     * @return mixed[]|null The parameter value as an array, or null if the parameter is null
     * @throws TypeError If the parameter is neither an array nor null
     */
    public function asArrayOrNull() : ?array {
        if ( $this->isNull() ) {
            return null;
        }
        return $this->asArray( 'Parameter is not an array or null.' );
    }


    /**
     * Returns the parameter as either an array or string.
     *
     * @return mixed[]|string The parameter value as an array if it's an array, otherwise as a string
     * @throws TypeError If the parameter is neither an array nor convertible to string
     */
    public function asArrayOrString() : array|string {
        if ( is_array( $this->xValue ) ) {
            return $this->xValue;
        }
        return $this->asString();
    }


    /**
     * Converts the parameter to an array with all nested IParameter objects unwrapped.
     *
     * @param string|null $i_nstError Optional custom error message
     * @return mixed[] The parameter value as an array with all IParameter objects converted to their values
     * @throws TypeError If the parameter is not an array
     */
    public function asArrayRecursive( ?string $i_nstError = null ) : array {
        return self::unwrap( $this->asArray( $i_nstError ) );
    }


    /**
     * Converts the parameter to a recursively unwrapped array or returns null.
     *
     * @return mixed[]|null The recursively unwrapped array, or null if the parameter is null
     * @throws TypeError If the parameter is neither an array nor null
     */
    public function asArrayRecursiveOrNull() : ?array {
        if ( $this->isNull() ) {
            return null;
        }
        return $this->asArrayRecursive();
    }


    /**
     * Returns the parameter as either a recursively unwrapped array or string.
     *
     * @return mixed[]|string The recursively unwrapped array if it's an array, otherwise as a string
     * @throws TypeError If the parameter is neither an array nor convertible to string
     */
    public function asArrayRecursiveOrString() : array|string {
        if ( is_array( $this->xValue ) ) {
            return self::unwrap( $this->xValue );
        }
        return $this->asString();
    }


    /**
     * Converts the parameter to a boolean value.
     *
     * Note: This method treats null as false. If you need to distinguish between
     * null and false, use asBoolOrNull() instead.
     *
     * @return bool The parameter value as a boolean
     * @throws ParseException If the parameter cannot be parsed as a boolean
     */
    public function asBool() : bool {
        $nst = $this->asStringOrNull();
        if ( is_null( $nst ) ) {
            return false;
        }
        return Parse::bool( $nst, "Parameter is not a boolean: {$nst}" );
    }


    /**
     * Converts the parameter to a boolean or returns null.
     *
     * Unlike the other asTypeOrNull() methods, asBoolOrNull() really only changes
     * how a null value is handled; asBoolOrNull() returns null, whereas asBool()
     * coerces null to false. So asBoolOrNull() is more of a tri-bool.
     *
     * @return bool|null The parameter value as a boolean, or null if the parameter is null
     * @throws ParseException If the parameter cannot be parsed as a boolean
     */
    public function asBoolOrNull() : ?bool {
        if ( $this->isNull() ) {
            return null;
        }
        return $this->asBool();
    }


    /**
     * Validates that the parameter matches a specific constant value.
     *
     * @param string $i_stConstant The expected constant value
     * @return string The parameter value (which matches the constant)
     * @throws ParseException If the parameter does not match the constant value
     */
    public function asConstant( string $i_stConstant ) : string {
        $st = $this->asString();
        return Parse::constant( $st, $i_stConstant, "Parameter is not the constant value '{$i_stConstant}': {$st}" );
    }


    /**
     * Converts the parameter to a currency amount in cents.
     *
     * @return int The currency amount in cents (e.g., $1.23 becomes 123)
     * @throws ParseException If the parameter cannot be parsed as a currency amount
     */
    public function asCurrency() : int {
        $st = $this->asString();
        return Parse::currency( $st, "Parameter is not a currency amount: {$st}" );
    }


    /**
     * Converts the parameter to a currency amount or returns null for empty strings.
     *
     * @return int|null The currency amount in cents, or null if the parameter is an empty string
     * @throws ParseException If the parameter is not a valid currency amount or empty
     */
    public function asCurrencyOrEmpty() : ?int {
        if ( $this->isEmptyString() ) {
            return null;
        }
        $st = $this->asString();
        return Parse::currency( $st, "Parameter is not a currency amount or empty: {$st}" );
    }


    /**
     * Converts the parameter to a currency amount or returns null.
     *
     * @return int|null The currency amount in cents, or null if the parameter is null
     * @throws ParseException If the parameter is not a valid currency amount or null
     */
    public function asCurrencyOrNull() : ?int {
        if ( $this->isNull() ) {
            return null;
        }
        return $this->asCurrency();
    }


    /**
     * Converts the parameter to a date string in YYYY-MM-DD format.
     *
     * @return string The date in YYYY-MM-DD format
     * @throws ParseException If the parameter cannot be parsed as a valid date
     */
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


    /**
     * Converts the parameter to a date-time string.
     *
     * @return string The date and time in a standard format
     * @throws ParseException If the parameter cannot be parsed as a valid date-time
     */
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


    /**
     * Validates and returns the parameter as an email address.
     *
     * @return string The validated email address
     * @throws ParseException If the parameter is not a valid email address
     */
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


    /**
     * Validates and returns the parameter as an email username (local part).
     *
     * @return string The validated email username (part before @)
     * @throws ParseException If the parameter is not a valid email username
     */
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


    /**
     * Validates that the parameter is a path to an existing directory.
     *
     * @return string The validated directory path
     * @throws ParseException If the parameter is not a path to an existing directory
     */
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


    /**
     * Validates that the parameter is a path to an existing file.
     *
     * @return string The validated file path
     * @throws ParseException If the parameter is not a path to an existing file
     */
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


    /**
     * Converts the parameter to a floating-point number.
     *
     * @return float The parameter value as a float
     * @throws ParseException If the parameter cannot be parsed as a number
     */
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


    /**
     * Validates that the parameter is a float within a closed range (exclusive).
     *
     * @param float $i_fMin The minimum allowed value (exclusive)
     * @param float $i_fMax The maximum allowed value (exclusive)
     * @return float The parameter value as a float
     * @throws ParseException If the parameter is not a number within the specified range
     */
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


    /**
     * Validates that the parameter is a float within a half-closed range [min, max).
     *
     * @param float $i_fMin The minimum allowed value (inclusive)
     * @param float $i_fMax The maximum allowed value (exclusive)
     * @return float The parameter value as a float
     * @throws ParseException If the parameter is not a number within the specified range
     */
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


    /**
     * Validates that the parameter is a float within an open range [min, max].
     *
     * @param float $i_fMin The minimum allowed value (inclusive)
     * @param float $i_fMax The maximum allowed value (inclusive)
     * @return float The parameter value as a float
     * @throws ParseException If the parameter is not a number within the specified range
     */
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


    /**
     * Validates and returns the parameter as a hostname.
     *
     * @return string The validated hostname
     * @throws ParseException If the parameter is not a valid hostname
     */
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


    /**
     * Validates and returns the parameter as an IP address (IPv4 or IPv6).
     *
     * @return string The validated IP address
     * @throws ParseException If the parameter is not a valid IP address
     */
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


    /**
     * Validates and returns the parameter as an IPv4 address.
     *
     * @return string The validated IPv4 address
     * @throws ParseException If the parameter is not a valid IPv4 address
     */
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


    /**
     * Validates and returns the parameter as an IPv6 address.
     *
     * @return string The validated IPv6 address
     * @throws ParseException If the parameter is not a valid IPv6 address
     */
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


    /**
     * Converts the parameter to an integer.
     *
     * @return int The parameter value as an integer
     * @throws ParseException If the parameter cannot be parsed as an integer
     */
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


    /**
     * Validates that the parameter is an integer within a closed range (exclusive).
     *
     * @param int $i_iMin The minimum allowed value (exclusive)
     * @param int $i_iMax The maximum allowed value (exclusive)
     * @return int The parameter value as an integer
     * @throws ParseException If the parameter is not an integer within the specified range
     */
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


    /**
     * Validates that the parameter is an integer within a half-closed range [min, max).
     *
     * @param int $i_iMin The minimum allowed value (inclusive)
     * @param int $i_iMax The maximum allowed value (exclusive)
     * @return int The parameter value as an integer
     * @throws ParseException If the parameter is not an integer within the specified range
     */
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


    /**
     * Validates that the parameter is an integer within an open range [min, max].
     *
     * @param int $i_iMin The minimum allowed value (inclusive)
     * @param int $i_iMax The maximum allowed value (inclusive)
     * @return int The parameter value as an integer
     * @throws ParseException If the parameter is not an integer within the specified range
     */
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


    /**
     * Returns the parameter value as a JSON string.
     *
     * This is primarily intended for debugging purposes.
     *
     * @return string The parameter value encoded as JSON
     * @throws JsonException If the value cannot be encoded as JSON
     */
    public function asJSON() : string {
        return json_encode( $this->xValue, JSON_THROW_ON_ERROR );
    }


    /**
     * Validates that the parameter matches one of the allowed keywords.
     *
     * @param list<string> $i_rKeywords List of allowable keywords
     * @return string The parameter value (which matches one of the keywords)
     * @throws ParseException If the parameter does not match any of the allowed keywords
     */
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


    /**
     * Validates that the parameter is a path that does not exist.
     *
     * @return string The validated path
     * @throws ParseException If the parameter is a path to an existing file or directory
     */
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


    /**
     * Validates that the parameter is a positive floating-point number (> 0).
     *
     * @return float The parameter value as a positive float
     * @throws ParseException If the parameter is not a positive number
     */
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


    /**
     * Validates that the parameter is a positive integer (> 0).
     *
     * @return int The parameter value as a positive integer
     * @throws ParseException If the parameter is not a positive integer
     */
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


    /**
     * Converts the parameter to a float rounded to the specified precision.
     *
     * @param int $i_iPrecision Number of decimal places to round to (default: 0)
     * @return float The rounded float value
     * @throws ParseException If the parameter cannot be parsed as a number
     */
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


    /**
     * Converts the parameter to an integer after rounding to the specified precision.
     *
     * @param int $i_iPrecision Number of decimal places before rounding (default: 0)
     * @return int The rounded integer value
     * @throws ParseException If the parameter cannot be parsed as a number
     */
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


    /**
     * Returns the parameter value as a string.
     *
     * @return string The parameter value as a string
     * @throws TypeError If the parameter is not a string (e.g., if it's an array)
     */
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


    /**
     * Converts the parameter to a time string in HH:MM:SS format.
     *
     * @return string The time in HH:MM:SS format
     * @throws ParseException If the parameter cannot be parsed as a valid time
     */
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


    /**
     * Converts the parameter to a Unix timestamp.
     *
     * @return int The Unix timestamp
     * @throws ParseException If the parameter cannot be parsed as a valid timestamp
     */
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


    /**
     * Validates that the parameter is a non-negative floating-point number (>= 0).
     *
     * @return float The parameter value as a non-negative float
     * @throws ParseException If the parameter is not a non-negative number
     */
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


    /**
     * Validates that the parameter is a non-negative integer (>= 0).
     *
     * @return int The parameter value as a non-negative integer
     * @throws ParseException If the parameter is not a non-negative integer
     */
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


    /**
     * Returns the current parameter during array iteration.
     *
     * @return Parameter The current parameter
     * @throws TypeError If this parameter is not an array
     */
    public function current() : Parameter {
        $this->checkArray();
        assert( is_array( $this->xValue ) );
        return $this->child( $this->xValue[ current( $this->rKeys ) ] );
    }


    /**
     * Returns the raw value stored in this parameter.
     *
     * @return mixed[]|string|null The raw parameter value
     */
    public function getValue() : array|string|null {
        return $this->xValue;
    }


    /**
     * Checks if the parameter array has the specified key.
     *
     * @param int|string $key The key to check for
     * @return bool True if the key exists, false otherwise
     * @throws TypeError If this parameter is not an array
     */
    public function has( int|string $key ) : bool {
        $this->checkArray();
        assert( is_array( $this->xValue ) );
        return array_key_exists( $key, $this->xValue );
    }


    /**
     * Returns a parameter for the given key, or a parameter with the default value.
     *
     * @param int|string $key The array key to access
     * @param mixed[]|string|null $default The default value if the key doesn't exist
     * @return Parameter A Parameter containing either the key's value or the default
     * @throws TypeError If this parameter is not an array
     */
    public function indexOrDefault( int|string $key, array|string|null $default ) : self {
        $x = $this->indexOrNull( $key );
        if ( $x instanceof self ) {
            return $x;
        }
        return new self( $default, $this->bAllowNull, $this->nuAllowArrayDepth );
    }


    /**
     * Returns a parameter for the given key, or null if the key doesn't exist.
     *
     * @param int|string $key The array key to access
     * @return Parameter|null A Parameter for the key's value, or null if the key doesn't exist
     * @throws TypeError If this parameter is not an array
     */
    public function indexOrNull( int|string $key ) : ?Parameter {
        if ( ! $this->has( $key ) ) {
            return null;
        }
        assert( is_array( $this->xValue ) );
        return $this->child( $this->xValue[ $key ] );
    }


    /**
     * Compares this parameter's value with another value for equality.
     *
     * This method handles type-aware comparisons including boolean validation,
     * recursive array comparison, and proper null handling.
     *
     * @param mixed[]|int|string|float|bool|IParameter|null $i_xValue The value to compare against
     * @return bool True if the values are equal, false otherwise
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


    /**
     * Checks if this parameter contains an array value.
     *
     * @return bool True if the parameter is an array, false otherwise
     */
    public function isArray() : bool {
        return is_array( $this->xValue );
    }


    /**
     * Checks if this parameter can be converted to a boolean.
     *
     * @return bool True if the parameter can be parsed as a boolean, false otherwise
     */
    public function isBool() : bool {
        if ( is_array( $this->xValue ) ) {
            return false;
        }
        if ( is_null( $this->xValue ) ) {
            return true;
        }
        return Validate::bool( strval( $this->xValue ) );
    }


    /**
     * Checks if this parameter is empty (null, empty string, or empty array).
     *
     * @return bool True if the parameter is empty, false otherwise
     */
    public function isEmpty() : bool {
        if ( null === $this->xValue ) {
            return true;
        }
        if ( is_array( $this->xValue ) ) {
            return 0 === count( $this->xValue );
        }
        return '' === $this->xValue;
    }


    /**
     * Checks if this parameter is an empty string.
     *
     * @return bool True if the parameter is an empty string, false otherwise
     */
    public function isEmptyString() : bool {
        return '' === $this->xValue;
    }


    /**
     * Checks if this parameter is frozen (permanently immutable).
     *
     * @return bool True if the parameter is frozen, false otherwise
     */
    public function isFrozen() : bool {
        return $this->bFrozen;
    }


    /**
     * Checks if this parameter is currently mutable.
     *
     * @return bool True if the parameter can be modified, false otherwise
     */
    public function isMutable() : bool {
        return $this->bMutable;
    }


    /**
     * Checks if this parameter contains a null value.
     *
     * @return bool True if the parameter is null, false otherwise
     */
    public function isNull() : bool {
        return is_null( $this->xValue );
    }


    /**
     * Checks if this parameter has been set with a value.
     *
     * @return bool Always returns true for immutable parameters
     */
    public function isSet() : bool {
        return true;
    }


    /**
     * Checks if this parameter contains a string value.
     *
     * @return bool True if the parameter is a string, false otherwise
     */
    public function isString() : bool {
        return is_string( $this->xValue );
    }


    /**
     * Returns the value for JSON serialization.
     *
     * @return mixed[]|string|null The value suitable for JSON encoding
     */
    public function jsonSerialize() : array|string|null {
        if ( ! is_array( $this->xValue ) ) {
            return $this->xValue;
        }
        return self::unwrap( $this->xValue );
    }


    /**
     * Returns the current key during array iteration.
     *
     * @return int|string The current array key
     * @throws TypeError If this parameter is not an array
     */
    public function key() : int|string {
        $this->checkArray();
        return current( $this->rKeys );
    }


    /**
     * Creates a new parameter with the same configuration but different value.
     *
     * @param mixed[]|bool|float|int|string|IParameter|null $xValue The value for the new parameter
     * @return static A new parameter instance with the same configuration
     */
    public function new( array|bool|float|int|string|IParameter|null $xValue ) : static {
        /** @phpstan-ignore new.static */
        return new static( $xValue, $this->bAllowNull, $this->nuAllowArrayDepth );
    }


    /**
     * Advances the internal pointer during array iteration.
     *
     * @return void
     * @throws TypeError If this parameter is not an array
     */
    public function next() : void {
        $this->checkArray();
        next( $this->rKeys );
    }


    /**
     * Checks whether an offset exists (ArrayAccess interface).
     *
     * @suppress PhanTypeMismatchDeclaredParamNullable
     * @param mixed $offset The offset to check (must be int or string)
     * @return bool True if the offset exists, false otherwise
     * @throws InvalidArgumentException If the offset is not an integer or string
     * @throws TypeError If this parameter is not an array
     */
    public function offsetExists( mixed $offset ) : bool {
        if ( ! is_int( $offset ) && ! is_string( $offset ) ) {
            throw new InvalidArgumentException( 'Parameter key is not an integer or string.' );
        }
        return $this->has( $offset );
    }


    /**
     * Returns the value at the specified offset (ArrayAccess interface).
     *
     * @suppress PhanTypeMismatchDeclaredParamNullable
     * @param mixed $offset The offset to retrieve (must be int or string)
     * @return Parameter The parameter at the specified offset
     * @throws InvalidArgumentException If the offset is not an integer or string
     * @throws OutOfBoundsException If the offset does not exist
     * @throws TypeError If this parameter is not an array
     */
    public function offsetGet( mixed $offset ) : self {
        if ( ! is_int( $offset ) && ! is_string( $offset ) ) {
            throw new InvalidArgumentException( 'Parameter key is not an integer or string.' );
        }
        $x = $this->indexOrNull( $offset );
        if ( $x instanceof self ) {
            return $x;
        }
        throw new OutOfBoundsException( "Parameter does not have key '{$offset}'." );
    }


    /**
     * Sets the value at the specified offset (ArrayAccess interface).
     *
     * Always throws LogicException since Parameters are immutable.
     *
     * @suppress PhanTypeMismatchDeclaredParamNullable
     * @param mixed $offset The offset to set (must be int or string)
     * @param mixed $value The value to set
     * @return void
     * @throws LogicException Always thrown since Parameter is immutable
     */
    public function offsetSet( mixed $offset, mixed $value ) : void {
        throw new LogicException( 'Parameter is immutable.' );
    }


    /**
     * Unsets the value at the specified offset (ArrayAccess interface).
     *
     * Always throws LogicException since Parameters are immutable.
     *
     * @suppress PhanTypeMismatchDeclaredParamNullable
     * @param mixed $offset The offset to unset (must be int or string)
     * @return void
     * @throws LogicException Always thrown since Parameter is immutable
     */
    public function offsetUnset( mixed $offset ) : void {
        throw new LogicException( 'Parameter is immutable.' );
    }


    /**
     * Resets the internal pointer to the beginning during array iteration.
     *
     * @return void
     * @throws TypeError If this parameter is not an array
     */
    public function rewind() : void {
        $this->checkArray();
        reset( $this->rKeys );
    }


    /**
     * Checks if the current position is valid during array iteration.
     *
     * @return bool True if the current position is valid, false otherwise
     * @throws TypeError If this parameter is not an array
     */
    public function valid() : bool {
        $this->checkArray();
        return false !== current( $this->rKeys );
    }


    /**
     * Freezes this parameter, making it permanently immutable.
     *
     * @return void
     */
    protected function _freeze() : void {
        $this->bFrozen = true;
        $this->bMutable = false;
    }


    /**
     * Changes the mutability state of this parameter.
     *
     * @param bool $i_bMutable Whether the parameter should be mutable
     * @return void
     * @throws LogicException If trying to make a frozen parameter mutable
     */
    protected function _mutate( bool $i_bMutable ) : void {
        if ( $i_bMutable && $this->bFrozen ) {
            throw new LogicException( 'Parameter is frozen.' );
        }
        $this->bMutable = $i_bMutable;
    }


    /**
     * Sets the value of this parameter.
     *
     * @param mixed[]|bool|float|int|string|IParameter|null $i_xValue The value to set
     * @return void
     * @throws LogicException If the parameter is immutable and already set
     * @throws InvalidArgumentException If null is provided but not allowed, or array depth exceeds limits
     */
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


    /**
     * Validates that this parameter contains an array value.
     *
     * @return void
     * @throws TypeError If this parameter is not an array
     */
    private function checkArray() : void {
        if ( ! is_array( $this->xValue ) ) {
            throw new TypeError( 'Parameter is not an array.' );
        }
    }


    /**
     * Creates a child parameter with inherited configuration.
     *
     * @param mixed[]|bool|float|int|string|IParameter|null $i_xValue The value for the child parameter
     * @return Parameter A new Parameter instance with inherited configuration
     */
    private function child( array|bool|float|int|string|IParameter|null $i_xValue ) : Parameter {
        $nuNewArrayDepth = $this->nuAllowArrayDepth;
        if ( is_int( $nuNewArrayDepth ) && $nuNewArrayDepth > 0 ) {
            $nuNewArrayDepth--;
        }
        return new Parameter( $i_xValue, $this->bAllowNull, $nuNewArrayDepth );
    }


}
