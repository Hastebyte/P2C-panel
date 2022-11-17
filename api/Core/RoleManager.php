<?php

namespace Core;

require_once( __DIR__ . '/Role.php' );

class RoleManager
{
    private $roles;

    const ANSIBLE_ROLES_PATH = '/etc/ansible/roles';

    public function __construct( )
    {
        $this->roles = array( );
    }

    public function scanRoles( )
    {
        if ( !file_exists( "/etc/ansible/roles" ) )
        {
            SendJSONError( 500, "/etc/ansible/roles does not exist" );
            return false;
        }

        $files = scandir( self::ANSIBLE_ROLES_PATH );

        if ( $files === false )
            return false;

        $folders = array_diff( $files, array( '..', '.' ) );

        foreach( $folders as $folder )
        {
            //printf( "[+] %s\n", $folder );

            $role = new Role( $folder );
            $role->analyze( );
            array_push( $this->roles, $role );
        }

        return true;
    }

    public function exportToArray( )
    {  
        $collection = array( );

        foreach( $this->roles as $role )
        {
            $element = array(
                "name"      => $role->name( ),
                "hosts"     => '',
                "tasks"     => ''
                );

            $collection[] = $element;
        }                

        return $collection;

        //$json_string = json_encode( $plays, JSON_FORCE_OBJECT | JSON_PRETTY_PRINT );
        //printf( "\n" . $json_string . "\n" );
    }
}

?>