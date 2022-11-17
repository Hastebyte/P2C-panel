<?php

namespace Core;

require_once( __DIR__ . '/Playbook.php' );

class PlaybookManager
{
    const ANSIBLE_PLAYBOOK_PATH = '/etc/ansible/playbooks';

    private $playbooks;

    public function __construct( )
    {
        $this->playbooks = array( );
    }

    public function scanPlaybooks( )
    {
        if ( !file_exists( "/etc/ansible/playbooks" ) )
        {
            SendJSONError( 500, "/etc/ansible/playbooks does not exist" );
            return false;
        }

        $files = scandir( self::ANSIBLE_PLAYBOOK_PATH );

        if ( $files === false )
            return false;

        $files = array_diff( $files, array( '..', '.' ) );

        foreach( $files as $file )
        {
            $extension = pathinfo( $file, PATHINFO_EXTENSION );

            if ( strtoupper( $extension ) != "YML" )
                continue; 

            $playbook = new Playbook( $file );
            $playbook->analyze( );
            array_push( $this->playbooks, $playbook );
        }

        return true;
    }

    public function exportToArray( )
    {  
        $plays = array( );

        foreach( $this->playbooks as $playbook )
        {
            $var = array(
                "name"      => $playbook->name( ),
                "hosts"     => $playbook->hosts( ),
                "tasks"     => $playbook->tasks( )
                );

            $plays[] = $var;
        }                

        return $plays;

        //$json_string = json_encode( $plays, JSON_FORCE_OBJECT | JSON_PRETTY_PRINT );
        //printf( "\n" . $json_string . "\n" );
    }

    public function __toString( )
    {
        if ( sizeof( $this->playbooks ) == 0 )
            return "";

        foreach ( $this->playbooks as $playbook )
        {
            printf( "[+] %s\n", $playbook->name( ) );
        }
    }

}