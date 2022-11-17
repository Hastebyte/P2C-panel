<?php

namespace Core;
use Mysqli;

require_once( __DIR__ . '/../Util/Format.php' );

abstract class E_USER_TYPE
{
    const USER_ADMIN = 6; //
}

class Authenticator
{
    private $mysqli;
    private $serials;

    public $userid;
    public $level;

    public function __construct( $mysqli )
    {
        $this->serials = array( );
        $this->mysqli = $mysqli;
        $this->userid = 0;
        $this->level = 0;
    }

    // curl -d '{"action":"authuUser","username":"choose","password":"test"}' -H "Content-Type: application/json" -X POST http://127.0.0.1/api/index.php

    public function authUser( $request )
    {
        if ( !array_key_exists( 'username', $request ) || 
             !array_key_exists( 'password', $request ) )
        {
            \Util\SendJSONError( 500, "invalid parameter" );
            die( );
        }

        $username = \Util\Sanitize( $this->mysqli, $request['username'] );
        $password = \Util\Sanitize( $this->mysqli, $request['password'] );
        $password = hash( 'sha256', $password );

        $query = "SELECT * from users WHERE username = '" . $username . "' AND password = '" . $password . "'";
        $result = mysqli_query( $this->mysqli, $query );

        if ( !$result )
        {
            $this->mysqli->close( );
            \Util\SendJSONError( 500, "invalid parameter" );
            die( );
        }

        $row = $result->fetch_object( );

        if ( $row == NULL )
        {
            $this->mysqli->close( );
            \Util\SendJSONError( 500, "user not found" );
            die( );
        }

        $this->userid = $row->userid;
        $this->level = $row->level;

        //echo json_encode( array( 'code' => '200', 'userid' => $this->userid, 'level' => $this->level ), JSON_PRETTY_PRINT );
        return true;
    }

}

?>
