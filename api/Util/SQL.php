<?php

namespace Util;
use Mysqli;

/**
 * Establishes connected to the SQL database
 *
 * @return boolean mysql object
 */

function ConnectSQL( )
{
  $mysqli = new mysqli( 'localhost', 'gateway', 'password_here', 'gateway' );

  if ( mysqli_connect_errno( ) )
  {
      return null;
  }

  return $mysqli;
}

/**
 * Prevents mysql injections
 *
 * @return variant value
 */

function Sanitize( $mysqli, $value )
{
  $value = preg_replace( '/[^A-Za-z0-9\-\s]/', '', $value );
  return mysqli_real_escape_string( $mysqli, $value );
}

?>
