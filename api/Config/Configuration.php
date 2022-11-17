<?php

namespace Config;

class Configuration
{
    private $username;
    private $password;
    private $salt;
    private $inventories;

    public function __construct( )
    {
        $this->inventories = array( );
    }

    private function getINIValue( $line )
    {
        $parts = explode( '=', $line );

        if ( sizeof( $parts ) != 2 )
            return "";

        return $parts[1];
    }

    public function readConfiguration( )
    {
        if ( !file_exists( __DIR__ . "/config.ini" ) )
        {
            return false;
        }

        $content = file_get_contents( __DIR__ . "/config.ini" );

        if ( $content === false )
            return false;

        $lines = explode( "\n", $content );
        $content = "";

        foreach ( $lines as $line )
        {
            $line = preg_replace( "/(.*?)#(.*)/", "$1", $line );
            $line = trim( $line );             

            if ( empty( $line ) )
                continue;

            if ( strpos( $line, "username=" ) !== false )
            {
                $this->username = $this->getINIValue( $line );
                continue;
            }

            if ( strpos( $line, "password=" ) !== false )
            {
                $this->password = $this->getINIValue( $line );
                continue;
            }
                
            if ( strpos( $line, "salt=" ) !== false )
            {
                $this->salt = $this->getINIValue( $line );
                continue;
            }
            
            if ( strpos( $line, "inventory=" ) !== false )
            {
                $this->inventories[] = $this->getINIValue( $line );
                continue;
            }
        }
        
        return true;
    }

    public function getInventoryFiles( )
    {  
        return $this->inventories;
    }

    public function dumpConfiguration( )
    {
        printf( "[+] Username:%s\n", $this->username );
        printf( "[+] Password:%s\n", $this->password );
        printf( "[+] Salt:%s\n", $this->salt );

        if ( sizeof( $this->inventories ) == 0 )
            return "";

        foreach ( $this->inventories as $inventory )
        {
            printf( "[+] Inventory path:%s\n", $inventory );
        }
    }

}