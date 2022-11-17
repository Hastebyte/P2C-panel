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

// public function createSerials( $count, $duration, $gameId, $resellerId, $comments, &$resultArray )

if ( php_sapi_name( ) === 'cli' )
{
    printf( "\e[0;31;40m[+] mgmtapi command line\e[0m\n" );


    printf( "[+] count:" );
    $count = fgets( fopen( 'php://stdin', 'r' ) );

    printf( "[+] duration:" );
    $duration = fgets( fopen( 'php://stdin', 'r' ) );

    printf( "[+] games:\n" );
    printf( "    r6=0\n" );
    printf( "    dayz=1\n" );
    printf( "    rust=2\n" );
    printf( "    scum=3\n" );
    printf( "    apex=4\n" );
    printf( "    fortnite=5\n" );
    printf( "    fifa=9\n" );
    printf( "    overwatch=10\n" );
    printf( "    gameid:" );
    $gameid = fgets( fopen( 'php://stdin', 'r' ) );

    printf( "[+] resellers:\n" );
    printf( "    xx=1\n" );
    printf( "    xx=4\n" );
    printf( "    resellerid:" );
    $resellerid = fgets( fopen( 'php://stdin', 'r' ) );

    printf( "[+] comments:" );
    $comments = fgets( fopen( 'php://stdin', 'r' ) );

    printf( "[+] making: %d keys with duration: %d for game: %d\n\n", $count, $duration, $gameid );

    $serialManager = new Core\SerialManager( $mysql_link, 0, 0 ); 

    $keyArray = array( );
    $serialManager->createSerials( $count, $duration, $gameid, $resellerid, $comments, $keyArray );
    print_r( $keyArray );
    printf( "\n" );


    //printf( "[+] argument count: %d\n", func_num_args( ) );
    
    //print_r( $argv );
    
    //if ($numargs >= 2) {
    //    func_get_arg(1) . "\n";
    //$arg_list = func_get_args();

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


$serialManager = new Core\SerialManager( $mysql_link, 0, $authenticator->userid /*0*/ ); 
$serialManager->querySerials( );

//$keyArray = array( );
//$serialManager->createSerials( 3, 8, 0, 0, "comment", $keyArray );
//print_r( $keyArray );

//temp
//die( "" );

switch ( $request['action'] )
{
    case 'authUser':
        Util\SendJSONArray( array( 'code' => '200', 'userid' => $authenticator->userid, 'level' => $authenticator->level ) );
        break;
    case 'getSerials':
        Util\SendJSONArray( $serialManager->exportToArray( ) );
        break;
    case 'createSerials':
    case 'setDuration':
    case 'setComment':
    case 'unlockHWID':
    case 'banSerial':
        $serialManager->parseSerialRequest( $request );
        break;

    default:
        break;
}

?>
