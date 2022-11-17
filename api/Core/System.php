<?php

namespace Core;

/**
 * Runs the ansible binary to get the version information
 * and formats it as an array for easy JSON export
 * 
 * @return array
 */ 

function GrabAnsibleMetaInformation( )
{
    $version_info = shell_exec( "/usr/bin/ansible --version" );
    $version_parts = explode( "\n", $version_info );

    foreach( $version_parts as $part )
    {
        printf( "[+] %s\n", trim( $part ) );
    }
}

/**
 * Collects system information
 *
 * @param integer  $code   HTTP error code
 * @param string   $error  Error message
 * 
 * @return array
 */ 

function GrabSystemInformation( )
{

}
?>