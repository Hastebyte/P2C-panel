<?php
        date_default_timezone_set( 'America/Los_Angeles' );

        $GLOBALS['loader_version']      = 119; // minimum loader version supported
        $GLOBALS['detected_status']     = 0;
        $GLOBALS['last_checked_date']   = date( "m.d.y" );

		//if ( empty($_SERVER['HTTPS'] )
		//	die( );
			
		
        dev1 class response_code
        {
                const success							= 402;
                const success_first_registration		= 403;
                const failure_invalid_serial			= 404;
                const failure_invalid_computerid		= 405;
                const failure_expired_serial			= 406;
                const failure_banned					= 407;
                const failure_database					= 408;
                const failure_outdated					= 409;
                const failure_bad_request				= 410;
                const failure_maintenance				= 411;
        }

        //register_serial( 1, "test" );
        //die( );

        // ---------------------------------------------------------------

        if ( empty( $_POST['bin'] ) )
        {
                //create_response( response_code::failure_bad_request );
				
				$return_text  = $GLOBALS['loader_version'] . '|' . $GLOBALS['detected_status'] . '|' . $GLOBALS['last_checked_date'] . '|';
				$return_text .= response_code::failure_bad_request;
                $return_text .= "|<";
				$return_text .= $_POST['bin'];
                $return_text .= "|>";
				$return_text  = encrypt_decrypt( $return_text );
                $return_text  = base64_encode( $return_text );
				print_r( $return_text );
				
				//file_put_contents( '_debug.txt', print_r( $_POST, true ) );				
				
                die( );
        }

        // decrypt and parse the passed in data


        $data_submitted = $_POST['bin'];
        $data_submitted = base64_decode( $data_submitted );
        $data_submitted = encrypt_decrypt( $data_submitted );

        $data_pieces = explode( '|', $data_submitted );

        if ( sizeof( $data_pieces ) < 2 )
        {
                //create_response( response_code::failure_bad_request );
				
				$return_text  = $GLOBALS['loader_version'] . '|' . $GLOBALS['detected_status'] . '|' . $GLOBALS['last_checked_date'] . '|';
				$return_text .= response_code::failure_bad_request;
                $return_text .= "|{";
				$return_text .= $_POST['bin'];
                $return_text .= "|}";				
				$return_text  = encrypt_decrypt( $return_text );
                $return_text  = base64_encode( $return_text );
				printf( $return_text );
				
                die( );
        }

        $key = $data_pieces[0];
        $computerid = $data_pieces[1];
        $gameid = $data_pieces[2];

        authenticate( $key, $computerid, $gameid );
        die( );
		
      // functions

        function authenticate( $key, $computerid_supplied, $gameid )
        {
                $mysqli = connect_mysql( );

                // sanitize input

                $key = preg_replace( '/[^A-Za-z0-9\-]/', '', $key );
                $key = mysqli_real_escape_string( $mysqli, $key );

                $computerid_supplied = preg_replace( '/[^A-Za-z0-9\-]/', '', $computerid_supplied );
                $computerid_supplied = mysqli_real_escape_string( $mysqli, $computerid_supplied );

                $gameid = preg_replace( '/[^A-Za-z0-9\-]/', '', $gameid );
                $gameid = mysqli_real_escape_string( $mysqli, $gameid );

				// disable a game here
				
				
                // look up serial in database
				// this "mapped" id lets you link two games together (ie: siege v1 and siege v2)
				// and lets you create global keys that work on all games
				
				switch( $gameid )
				{
				case 7:
					$query_game_id = 0; // siege
					break;
				case 8:
					$query_game_id = 2; // rust
					break;
				case 11:
					$query_game_id = 4; // apex
					break;
				//case 100:
				//	$query_game_id = 99; // apex
				//	break;
				default:
					$query_game_id = $gameid;
					break;
				}
				
				//if ( $gameid == 13 )
				//{
				//	create_response( response_code::failure_maintenance );
				//	die( );					
				//}

                $query = "SELECT * from serials WHERE serial = '" . $key . "' AND gameid='" . $query_game_id . "'";

                $result = mysqli_query( $mysqli, $query );

                if ( !$result )
                {
                        //printf( "failed1\n" );
                        $mysqli->close( );
                        return create_response( response_code::failure_database );
                }

                $result = $result->fetch_object( );

                if ( !$result )
                {
                        //printf( "failed2\n" );
                        $mysqli->close( );
                        return create_response( response_code::failure_invalid_serial );
                }


                // print_r( $result );

                $id = $result->id;
                $computerid = trim( $result->computerid );
                $registered_date = $result->registered;
                $duration = $result->duration;

                $hours_left = get_hours_remaining( $registered_date, $duration );

                if ( $hours_left == 0 )
                {
                        return create_response( response_code::failure_expired_serial, $gameid, $hours_left );
                }

                // if this serial is not registered to a machine
                // register

                if ( empty( $computerid ) )
                {
                        $mysqli->close( );

                        register_serial( $id, $computerid_supplied, $registered_date );
                        return create_response( response_code::success, $gameid, $hours_left );
                }

                // if one exists make sure it matches the old one

                if ( trim( $computerid, ' ' ) == trim( $computerid_supplied, ' ' ) )
                {
                        return create_response( response_code::success, $gameid, $hours_left );
                }

                // clearly it doesn't, return a code

                $mysqli->close( );
				return create_response( response_code::success, $gameid, $hours_left );
                //return create_response( response_code::failure_invalid_computerid );
        }

		function register_serial( $id, $computerid, $registered_date )
        {
                //printf( "[+] registering %s to %d\n", $computerid, $id );

                $mysqli = connect_mysql( );

				if ( empty( $registration_date ) )
				{
					$query = "UPDATE serials SET registered=CURDATE( ), "   .
							 "computerid='" .       $computerid             . "', " .
							 "lastip='" .           getenv('REMOTE_ADDR')   . "' "  .
							 "WHERE id=" .          $id;			
				} else {
					$query = "UPDATE serials SET "   .
							 "computerid='" .       $computerid             . "', " .
							 "lastip='" .           getenv('REMOTE_ADDR')   . "' "  .
							 "WHERE id=" .          $id;								
				}

                if ( $mysqli->query( $query ) === TRUE )
                {
                        //printf( "[+] record updated successfully\n" );
                        return true;
                } else {
                        //printf( "[!] error updating record: " . $conn->error );
                        return false;
                }
        }
		
        function get_hours_remaining( $registered_date, $duration )
        {
                // current date
                $today = new DateTime( );
                // registered date plus duration = end date
                //
                $end_date = new DateTime( $registered_date );
                $end_date->modify( '+' . $duration . 'day' );

                // get difference
                $difference = $today->diff( $end_date );

                $hours_left = 0;

                if ( $today > $end_date )
                {
                        $hours_left = 0;
                } else {
                        $hours_left  = $difference->days * 24;
                        $hours_left += $difference->h;
                }

                // $return_text = encrypt_decrypt( 'hours:' . $hours_left.  ', end date:' . $end_date->format( "Y-m-d" )  . ', registered:' . $registered_date . ' duration:' . $duration );
                // $return_text = base64_encode( $return_text );
                // die( $return_text );

                return $hours_left;
        }
		
		function create_response( $code, $gameid = 0, $time_left = 0 )
        {
                $return_text = $GLOBALS['loader_version'] . '|' . $GLOBALS['detected_status'] . '|' . $GLOBALS['last_checked_date'] . '|';

                switch( $code )
                {
                        case response_code::success:
                                $return_text .= $code;
								
								
								
								if ( $gameid == 0 )
								{
									$return_text .= "|http://127.0.0.1/download_siege.dll";
									$return_text .= "|" . $time_left;
									$return_text .= "|http://127.0.0.1/access/driver13.bin"; // dev1 driver 2 (was driver5)
								} else if ( $gameid == 1 ) {
									$return_text .= "|http://127.0.0.1/download_dayz.dll";
									$return_text .= "|" . $time_left;
									$return_text .= "|http://127.0.0.1/access/driver14.bin"; // dev2 driver
								} else if ( $gameid == 2 ) {
									$return_text .= "|http://127.0.0.1/download_rust.exe";
									$return_text .= "|" . $time_left;
									$return_text .= "|http://127.0.0.1/access/driver5.bin"; // dev3 driver -> changed to dev1 driver (5)
								} else if ( $gameid == 3 ) {
									$return_text .= "|http://127.0.0.1/download_scum.dll";
									$return_text .= "|" . $time_left;
									$return_text .= "|http://127.0.0.1/access/driver14.bin"; // dev2 driver
								} else if ( $gameid == 4 ) {
									$return_text .= "|http://127.0.0.1/download_apex.exe";
									$return_text .= "|" . $time_left;
									$return_text .= "|http://127.0.0.1/access/driver3.bin"; // dev4 driver not used
								} else if ( $gameid == 5 ) {
									$return_text .= "|http://127.0.0.1/download_fortnite.dll";
									$return_text .= "|" . $time_left;
									$return_text .= "|http://127.0.0.1/access/driver14.bin"; // dev2 driver
								} else if ( $gameid == 7 ) {
									$return_text .= "|http://127.0.0.1/download_siege.dll";
									$return_text .= "|" . $time_left;
									$return_text .= "|http://127.0.0.1/access/driver13.bin"; // dev1 driver 2 (was driver5)
								} else if ( $gameid == 8 ) {
									$return_text .= "|http://127.0.0.1/download_rust.exe";
									$return_text .= "|" . $time_left;
									$return_text .= "|http://127.0.0.1/access/driver13.bin"; // dev1 driver 2 (was driver5)
								} else if ( $gameid == 9 ) {
									$return_text .= "|http://127.0.0.1/download_fifa.dll";
									$return_text .= "|" . $time_left;
									$return_text .= "|http://127.0.0.1/access/driver3.bin"; // could be anything, not used
								} else if ( $gameid == 10 ) {
									$return_text .= "|http://127.0.0.1/download_overwatch.dll";
									$return_text .= "|" . $time_left;
									$return_text .= "|http://127.0.0.1/access/driver8.bin";  // dev1 driver 2
								} else if ( $gameid == 11 ) {
									$return_text .= "|http://127.0.0.1/download_apex.dll";
									$return_text .= "|" . $time_left;
									$return_text .= "|http://127.0.0.1/access/driver3.bin";  // dev4 driver 2
								} else if ( $gameid == 12 ) {
									$return_text .= "|http://127.0.0.1/download_pubglite.dll";
									$return_text .= "|" . $time_left;
									$return_text .= "|http://127.0.0.1/access/driver12.bin";  // dev6 driver
								} else if ( $gameid == 13 ) {
									$return_text .= "|http://127.0.0.1/download_tarkov.exe";
									$return_text .= "|" . $time_left;
									$return_text .= "|http://127.0.0.1/access/driver7.bin";  // dev1 driver tarkov
								} else if ( $gameid == 99 ) {
									$return_text .= "|http://127.0.0.1/download_hw.exe";
									$return_text .= "|" . $time_left;
									$return_text .= "|http://127.0.0.1/access/driver99.bin";  // dev3 driver hw
								} else if ( $gameid == 100 ) {
									$return_text .= "|http://127.0.0.1/big_download.exe";
									$return_text .= "|" . $time_left;
									$return_text .= "|http://127.0.0.1/access/driver100.bin"; 
								}
								
                                break;
						
                        case response_code::failure_invalid_serial:
                                $return_text .= $code;
                                $return_text .= "|";
                                break;

                        case response_code::failure_invalid_computerid:
                                $return_text .= $code;
                                $return_text .= "|";
                                break;

                        case response_code::failure_expired_serial:
                                $return_text .= $code;
                                $return_text .= "|";
                                break;

                        case response_code::failure_banned:
                                $return_text .= $code;
                                $return_text .= "|";
                                break;

                        case response_code::failure_database:
                                $return_text .= $code;
                                $return_text .= "|";
                                break;
								
						case response_code::failure_bad_request:
                                $return_text .= $code;
                                $return_text .= "|";
                                break;

						case response_code::failure_maintenance:
                                $return_text .= $code;
                                $return_text .= "|";
                                break;						
								
                        default:
                                $return_text .= response_code::failure_bad_request;
                                $return_text .= "|";
                                break;
                }

                $return_text = encrypt_decrypt( $return_text );
                $return_text = base64_encode( $return_text );

                printf( $return_text );
        }
		
		function connect_mysql( )
        {
                $mysqli = new mysqli( 'localhost', 'gateway', 'yourpassword_here', 'gateway' );

                if ( mysqli_connect_errno( ) )
                {
                        die( create_response( response_code::failure_database ) );
                }

                return $mysqli;
        }

        function encrypt_decrypt( $text )
        {
                $key = "\xD0\xF2\x01\x12\xEF\x22";
                $result = "";

                for ( $i = 0; $i < strlen( $text ); $i++ )
                {
                        $result .= chr( ord( $text[$i] ) ^ ord( $key[$i % strlen( $key )] ) );
                }

                return $result;
        }
?>