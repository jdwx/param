<?php


declare( strict_types = 1 );

require 'vendor/autoload.php';


use JDWX\Param\Parameter;


$st = readline( 'Enter an integer: ' );
if ( $st === false ) {
    echo "No input\n";
    exit( 1 );
}

try {
    $p = new Parameter( $st );
    $i = $p->asInt();
    echo "You entered: {$i}\n";
} catch ( TypeError $e ) {
    echo 'ERROR: ', $e->getMessage(), "\n";
}

