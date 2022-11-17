<?php

namespace Core;
use Mysqli;

require_once( __DIR__ . '/Serial.php' );
require_once( __DIR__ . '/../Util/Format.php' );

abstract class E_ACTION_TYPE
{
    const CREATE_SERIAL                = 0; //
    const MODIFY_SERIAL_DURATION       = 5; //  
    const MODIFY_SERIAL_REMOVE_DAY     = 6; //
    const MODIFY_SERIAL_SET_COMMENT    = 7; //
    const MODIFY_SERIAL_UNLOCK_HWID    = 8; //
    const MODIFY_SERIAL_BAN            = 9; //
}

class SerialManager
{
    private $mysqli;
    private $serials;
    private $userid;

    public function __construct( $mysqli, $userid )
    {
        $this->serials = array( );
        $this->mysqli = $mysqli;
        $this->userid = $userid;
    }

    public function querySerials( )
    {
        $result = mysqli_query( $this->mysqli, "SELECT * from serials" );

        if ( !$result )
        {
            $this->mysqli->close( );
            \Util\SendJSONError( 500, "database unavailable" );
            return false;
        }

        $this->serials = array( );

        while( $row = $result->fetch_object( ) )
        {       
            $serial = new Serial( );

            $serial->id           = $row->id;
            $serial->serial       = $row->serial;
            $serial->duration     = $row->duration;
            $serial->registered   = $row->registered;
            $serial->created      = $row->created;
            $serial->commission   = $row->commission;
            $serial->comments     = $row->comments;
            $serial->computerid   = $row->computerid;
            $serial->lastip       = $row->lastip;
            $serial->gameid       = $row->gameid;
            $serial->resellerid   = $row->resellerid;

            array_push( $this->serials, $serial );
		}

        return true;
    }

    public function serials( )
    {
        return $this->serials;
    }

    // curl -d '{"action":"getSerials"}' -H "Content-Type: application/json" -X POST http://127.0.0.1/api/index.php

    public function exportToArray( )
    {
        $collection = array( );

        foreach( $this->serials as $serial )
        {
            $element = array(
                "id"            => $serial->id,
                "serial"        => $serial->serial,
                "duration"      => $serial->duration,
                "registered"    => $serial->registered,
                "created"       => $serial->created,
                "commission"    => $serial->commission,
                "comments"      => $serial->comments,
                "computerid"    => $serial->computerid,
                "lastip"        => $serial->lastip,
                "gameid"        => $serial->gameid,
                "resellerid"    => $serial->resellerid,
                );

            $collection[] = $element;
        }

        // $json_string = json_encode( $collection, JSON_FORCE_OBJECT | JSON_PRETTY_PRINT );
        return $collection;
    }
    
    // INSERT INTO `gateway`.`history` (`id`, `type`, `userid`, `serialid`, `date`) VALUES (NULL, '5', '0', '1', '2019-08-07 00:00:00');

    private function createHistoricalRecord( $type, $serialId )
    {
        $query = "INSERT INTO history ( `type`, `userid`, `serialid`, `date` ) VALUES ( '$type', '$this->userid', '$serialId', NOW( ) )";
        mysqli_query( $this->mysqli, $query );
        $this->mysqli->close( );
    }

    // curl -d '{"action":"setDuration","serialId":"1","duration":"99","username":"choose","password":"gateway"}' -H "Content-Type: application/json" -X POST http://127.0.0.1/api/index.php

    private function setDuration( $serialId, $duration )
    {
        $serialId = \Util\Sanitize( $this->mysqli, $serialId );
        $duration = \Util\Sanitize( $this->mysqli, $duration );
        $duration = intval( $duration );

        if ( !mysqli_query( $this->mysqli, "UPDATE serials SET duration = " . $duration . " WHERE id = " . $serialId  ) )
        {
            $this->mysqli->close( );
            \Util\SendJSONError( 500, "database unavailable" );
            return false;
        }

        echo json_encode( array( 'code' => '200' ), JSON_PRETTY_PRINT ); 
        $this->createHistoricalRecord( E_ACTION_TYPE::MODIFY_SERIAL_DURATION, $serialId );
        return true;
    }

    // curl -d '{"action":"removeDay","serialId":"1"}' -H "Content-Type: application/json" -X POST http://127.0.0.1/api/index.php

    private function removeDay( $serialId )
    {
        $serialId = \Util\Sanitize( $this->mysqli, $serialId );

        if ( !mysqli_query( $this->mysqli, "UPDATE serials SET duration = duration - 1 WHERE id = " . $serialId  ) )
        {
            $this->mysqli->close( );
            \Util\SendJSONError( 500, "invalid parameter" );
            return false;
        }

        echo json_encode( array( 'code' => '200' ), JSON_PRETTY_PRINT ); 
        $this->createHistoricalRecord( E_ACTION_TYPE::MODIFY_SERIAL_REMOVE_DAY, $serialId );
        return true;
    }

    // curl -d '{"action":"setComment","serialId":"1","comment":"admin serial"}' -H "Content-Type: application/json" -X POST http://127.0.0.1/api/index.php
  
    private function setComment( $serialId, $comment )
    {     
        $serialId = \Util\Sanitize( $this->mysqli, $serialId );
        $comment = \Util\Sanitize( $this->mysqli, $comment );

        $query  = "UPDATE serials SET comments = '";
        $query .= $comment;
        $query .= "' WHERE id = " . $serialId;

        if ( !mysqli_query( $this->mysqli, $query ) )
        {
            $this->mysqli->close( );
            \Util\SendJSONError( 500, "invalid parameter" );
            return false;
        }

        echo json_encode( array( 'code' => '200' ), JSON_PRETTY_PRINT ); 
        $this->createHistoricalRecord( E_ACTION_TYPE::MODIFY_SERIAL_SET_COMMENT, $serialId );
        return true;
    } 
  
    // curl -d '{"action":"unlockHWID","serialId":"1"}' -H "Content-Type: application/json" -X POST http://127.0.0.1/api/index.php

    private function unlockHWID( $serialId )
    {     
        $serialId = \Util\Sanitize( $this->mysqli, $serialId );

        if ( !mysqli_query( $this->mysqli, "UPDATE serials SET computerid = NULL WHERE id = " . $serialId ) )
        {
            $this->mysqli->close( );
            \Util\SendJSONError( 500, "database unavailable" );
            return false;
        }

        echo json_encode( array( 'code' => '200' ), JSON_PRETTY_PRINT ); 
        $this->createHistoricalRecord( E_ACTION_TYPE::MODIFY_SERIAL_UNLOCK_HWID, $serialId );
        return true;
    } 
  
    // curl -d '{"action":"banSerial","serialId":"1"}' -H "Content-Type: application/json" -X POST http://127.0.0.1/api/index.php

    private function banSerial( $serialId )
    {     
        $serialId = \Util\Sanitize( $this->mysqli, $serialId );

        if ( !mysqli_query( $this->mysqli, "UPDATE serials SET duration = 0 WHERE id = " . $serialId ) )
        {
            $this->mysqli->close( );
            \Util\SendJSONError( 500, "database unavailable" );
            return false;
        }

        echo json_encode( array( 'code' => '200' ), JSON_PRETTY_PRINT ); 
        $this->createHistoricalRecord( E_ACTION_TYPE::MODIFY_SERIAL_BAN, $serialId );
        return true;
    } 

    private function generateSerial( )
    {
        $characters = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
        $charactersLength = strlen( $characters );

        $randomString = '';

        for ( $i = 0; $i < 40; $i++ )
        {
            $randomString .= $characters[rand( 0, $charactersLength - 1 )];
        }

        return $randomString;
    }

    private function serialExists( $serial )
    {
        $serial = \Util\Sanitize( $this->mysqli, $serial );
        $query = "SELECT COUNT(*) AS num_rows from serials WHERE serial = '" . $serial . "'";      
        $result = mysqli_query( $this->mysqli, $query );
    
        if ( !$result )
        {
            $this->mysqli->close( );
            return false;
        }
        
        $row = $result->fetch_array( MYSQLI_ASSOC );
 
        if ( $row["num_rows"] == 0 )
        {
            return false;
        }
  
        return true;
    }

    // curl -d '{"action":"createSerials","count":"10","duration":"8","gameId":"0","resellerId":"0","username":"choose","password":"gateway"}' -H "Content-Type: application/json" -X POST http://127.0.0.1/api/index.php

    public function createSerials( $count, $duration, $gameId, $resellerId, $comments, &$resultArray )
    {
        $resultArray = array( );
        $keysCreated = 0;

        for ( $i = 0; $i < $count; $i++ )
        {
            $newSerial = $this->generateSerial( );

            // unlikely collision of 40 random characters

            if ( $this->serialExists( $newSerial ) )
            {
                $newSerial = $this->generateSerial( );
            
                // if still a collision, something is wrong with the random character generation

                if ( $this->serialExists( $newSerial ) )
                    die( "createSerials" );
            }

            // debug key

            //printf( "[+] key: %s\n", $newSerial );

            // create insert statement

            $query  = "INSERT INTO serials ( serial, duration, created, comments, gameid, resellerid ) ";
            $query .=" VALUES ( '$newSerial', $duration, NOW( ), '$comments', $gameId, $resellerId )";

            if ( mysqli_query( $this->mysqli, $query ) )
            {
               //printf( "New record created successfully" );
               array_push( $resultArray, $newSerial );
               $keysCreated++;
            } else {
               //printf( "Error: " . $query . "" . mysqli_error( $this->mysqli ) );
               continue;
            }
        }

        return $keysCreated;
    }

    public function parseSerialRequest( $request )
    {
        if ( !array_key_exists( 'serialId', $request ) )
        {
            \Util\SendJSONError( 500, "Invalid request received, serialId parameter is missing" );
            die( );
        }

        // todo: check if comment is valid

        switch( $request['action'] )
        {
        case 'createSerials':
            $this->createSerials( $request['count'], $request['duration'], $request['gameid'], $request['serialId'], $request['serialId'] );
            break;
        case 'setDuration':
            $this->setDuration( $request['serialId'], $request['duration'] );
            break;
        case 'setComment': 
            $this->setComment( $request['serialId'], $request['comment'] );
            break;
        case 'unlockHWID':
            $this->unlockHWID( $request['serialId'] );
            break;
        case 'banSerial':
            $this->banSerial( $request['serialId'] );
            break;
        default:
            break;
        }

        /*
        if ( $request['action'] == 'addDay' )
        {
            addDay( $request['serialId'] );


        } else if ( $request['action'] = 'removeDay' ) {
        } else if ( $request['action'] = 'setComment' ) {
        } else if ( $request['action'] = 'unlockHWID' ) {
        } else if ( $request['action'] = 'ban' ) {
        
        }
        */
    }
}

?>
