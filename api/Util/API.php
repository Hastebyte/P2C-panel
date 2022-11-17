<?php

namespace Util;

/**
 * Retrieves a JSON array from POST data, checks if there is an action variable
 * returns an appropriate message when sanity checks fail
 * 
 * @return boolean status
 */ 

function GetClientRequest( )
{
    $input = json_decode( file_get_contents( 'php://input' ), true );

    if ( empty( $input ) || !is_array( $input ) )
    {
        // SendJSONError( 400, "Invalid request received" );
        return array( );
    }
    
    if ( !array_key_exists( 'action', $input ) )
    {
        // SendJSONError( 400, "Invalid request received, action parameter is missing" );
        return array( );
    }

    return $input;
}

/**
 * Displays Documentation\index.html
 * 
 * @return null
 */ 

function PrintDocumentation( )
{
    header( "Status: 200" );
    echo "Welcome " . exec( "whoami" )  . " to ansiblewebservices!";
}

?>