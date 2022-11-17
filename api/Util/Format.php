<?php

namespace Util;

function SendCORSHeaders( )
{
        header( "Access-Control-Allow-Origin: *" );
        header( "Access-Control-Allow-Credentials: true" );
        header( "Access-Control-Max-Age: 86400" );
        header( "Access-Control-Allow-Methods: GET, POST, OPTIONS" );
        header( "Access-Control-Allow-Headers: Origin, X-Requested-With, Content-Type, Accept, Range, Cache-Control" );

        if ( $_SERVER['REQUEST_METHOD'] === 'OPTIONS' )
        {
                header( "HTTP/1.0 200" );
                die( );
        }
}

/**
 * Sends an error message to the client
 *
 * @param integer  $code   HTTP error code
 * @param string   $error  Error message
 * 
 * @return null
 */ 

function SendJSONError( $code, $error )
{
        http_response_code( $code );
        header( 'Content-type: application/json' );           
        echo json_encode( array( 'code' => $code, 'error' => $error ), JSON_PRETTY_PRINT );     
}

/**
 * Sends an object array to the client
 *
 * @param string   $array  Object to send
* 
 * @return null
 */ 

function SendJSONArray( $array )
{
        http_response_code( 200 );        
        header( 'Content-type: application/json' );           
        echo json_encode( array( 'code' => '200', 'result' => $array ), JSON_PRETTY_PRINT );     
}

?>