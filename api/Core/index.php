<?php

require_once( __DIR__ . '/Util/API.php' );
require_once( __DIR__ . '/Util/Format.php' );
require_once( __DIR__ . '/Util/SQL.php' );
require_once( __DIR__ . '/Core/Authenticator.php' );
require_once( __DIR__ . '/Core/SerialManager.php' );

/*
require_once( __DIR__ . '/Core/System.php' );
require_once( __DIR__ . '/Core/Inventory.php' );
require_once( __DIR__ . '/Core/RoleManager.php' );
require_once( __DIR__ . '/Core/PlaybookManager.php' );
require_once( __DIR__ . '/Core/JobManager.php' );
require_once( __DIR__ . '/Config/Configuration.php' );
*/

$mysql_link = Util\ConnectSQL( );

if ( !$mysql_link )
{
    Util\SendJSONError( 500, "database is unavailable" );
    die( );
}

Util\SendCORSHeaders( );

$request = Util\GetClientRequest( );

if ( empty( $request ) )
{
    Util\SendJSONError( 500, "invalid request" );
    die( );
}

$authenticator = new Core\Authenticator( $mysql_link );
$authenticator->authUser( $request );

$serialManager = new Core\SerialManager( $mysql_link, $authenticator->userid );
$serialManager->querySerials( );

switch ( $request['action'] )
{
    case 'authUser':
        //Util\SendJSONArray( array( 'code' => '200', 'userid' => $authenticator->userid, 'level' => $authenticator->level ) );
        Util\SendJSONArray( array( 'code' => '200', 'userid' => 0, 'level' => 7 ) );
        break;
    case 'getSerials':
        Util\SendJSONArray( $serialManager->exportToArray( ) );
        break;
    
    /*
    case 'getPlaybooks':
        Util\SendJSONArray( $playbooks->exportToArray( ) );
        break;
    case 'getRoles':
        Util\SendJSONArray( $roles->exportToArray( ) );
        break;
    case 'getJobs':
        Util\SendJSONArray( $jobs->exportToArray( ) );
        break;
    */

    case 'addDay':
    case 'removeDay':
    case 'setComment':
    case 'unlockHWID':
    case 'banSerial':
        $serialManager->parseSerialRequest( $request );
        break;

    default:
        break;
}

?>
