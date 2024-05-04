# jdwx/param

A simple PHP module for parsing values from strings with type safety.

This is useful in a number of contexts:
* Parsing HTTP request arguments.
* Parsing database values returned as string-or-NULL.
* Parsing values from configuration files.
* Parsing values from command-line arguments.

## Installation

You can require it directly with Composer:

```php
composer require jdwx/param
```

Or download the source from GitHub: https://github.com/jdwx/param.git

## Requirements

This library requires PHP 8.1 or later. It does not work with earlier
versions. (Uses readonly properties.)  It requires the mbstring extension
but has no other external dependencies.

## Usage

```php
<?php

declare( strict_types = 1 );

require 'vendor/autoload.php';

use JDWX\Param\Parameter;

$st = readline( "Enter an integer: " );
if ( $st === false ) {
    echo "No input\n";
    exit( 1 );
}

try {
    $p = new Parameter( $st );
    $i = $p->asInt();
    echo "You entered: {$i}\n";
} catch ( TypeError $e ) {
    echo "ERROR: ", $e->getMessage(), "\n";
}
```

## Stability

This library is stable and has been used in production code. It is not 
expected to change in ways that break existing code. Additional parsing
methods may be added in the future, but existing methods should not
be changed in a way that breaks existing code without at least a minor
version update.

## History

This library has been in use for several years in production code. It
was originally part of a larger library that was broken up into smaller
pieces in 2023 and has been released as a standalone library in 2024.
