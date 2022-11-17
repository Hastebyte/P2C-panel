<?php

namespace Core;

require_once( __DIR__ . '/Group.php' );
require_once( __DIR__ . '/Host.php' );

class Inventory
{
    const ANSIBLE_PATH = '/etc/ansible/';
    const ANSIBLE_HOSTS_PATH = '/etc/ansible/hosts';
    const ANSIBLE_HOST_VARS_PATH = '/etc/ansible/host_vars/';
    const ANSIBLE_GROUP_VARS_PATH = '/etc/ansible/group_vars/';

    private $hosts;     // ungrouped hosts (revise) REMOVE
    private $groups;    // groups

    public function __construct( )
    {
        $this->hosts = array( );
        $this->groups = array( );


        // Create a group for hosts that are ungrouped in the hosts file
        //

        array_push( $this->groups, new Group( "" ) );
    }

    private function findGroupByName( $name )
    {
        foreach ( $this->groups as $group )
        {
            if ( $group->name( ) == $name )
                return $group;
        }

        return null;
    }

    private function removeComments( $line )
    {
        // Remove everything after the hash '#' marker
        // parse_ini_file / parse_ini_string - do not parse out # comments

        $line = preg_replace( "/(.*?)#(.*)/", "$1", $line );
        $line = trim( $line );        
        return $line;
    }

    private function getGroupVars( $group, $path )
    {
        $groupVarFile = $path;

        if ( file_exists( $groupVarFile ) )
        {
            $groupVarContent = file_get_contents( $groupVarFile );        
            $varlines = explode( "\n", $groupVarContent );           
            
            foreach ( $varlines as $var )
            {
                if ( !empty( trim( $var ) ) )
                {
                    $group->addGroupVar( trim( $var ), $groupVarFile, 1 );
                }
            }
        }       
    }

    private function getHostVars( $host, $path )
    {
        $hostVarFile = $path;

        if ( file_exists( $hostVarFile ) )
        {
            $hostVarContent = file_get_contents( $hostVarFile );        
            $varlines = explode( "\n", $hostVarContent );           
            
            foreach ( $varlines as $var )
            {
                if ( !empty( trim( $var ) ) )
                {
                    $host->addHostVar( trim( $var ), $hostVarFile, 0 );
                }
            }
        }        
    }

    public function readHosts( )
    {
        $content = file_get_contents( self::ANSIBLE_HOSTS_PATH );

        if ( $content === false )
            return false;

        // Parse the ansible hosts file, line by line
        //

        $lines = explode( "\n", $content );
        $content = "";

        // Ungrouped hosts can only be specified before any group headers
        //

        $currentGroup = $this->findGroupByName( "" );

        if ( $currentGroup == null )
        {
            // Something went wrong in the constructor or the default group was deleted
            //
            return false;
        }

        foreach ( $lines as $line )
        {
            $line = $this->removeComments( $line );

            if ( empty( $line ) )
                continue;

            // If the line starts with a '[' character we assume that this is a grouping of hosts

            if ( substr( $line, 0, 1 ) === "[" )
            {

                $line = str_replace( "[", "", $line );
                $line = str_replace( "]", "", $line );

                // Check if this group already exists
                // if not, then create a new one

                $result = $this->findGroupByName( $line );

                if ( $result == null )
                {
                    $currentGroup = new Group( $line );
                    $this->getGroupVars( $currentGroup, self::ANSIBLE_GROUP_VARS_PATH . $currentGroup->name( ) );
                    array_push( $this->groups, $currentGroup );
                } else {
                    $currentGroup = $result;
                }

                continue;
            }                

            // Everything else, here on out is assumed to be a host
            // We must check if there are variables appended after the host

            $host_parts = preg_split( "/\s+/", $line ); 
            $host = new Host( $host_parts[0] );

            // Append the variables (revise for multiple)

            if ( sizeof( $host_parts ) > 1 )
                $host->addHostVar( $host_parts[1], self::ANSIBLE_HOSTS_PATH, 0 );

            $currentGroup->addHost( $host );
            $this->getHostVars( $host, self::ANSIBLE_HOST_VARS_PATH . $host->name( ) );
        }
    }

    public function groups( )
    {
        return $this->groups;
    }

    public function exportToArray( )
    {
        // If there are no groups, something went wrong

        if ( sizeof( $this->groups ) == 0 )
            return "";

        $groups = array( );

        foreach ( $this->groups as $group )
        {
            $hosts = array( );
            $group_vars = array( );
            
            // Iterate each host in the group

            foreach ( $group->hosts( ) as $host )
            {
                $host_vars = array( );

                foreach ( $host->hostVars( ) as $var )
                {
                    $var = array(
                        "var"      => $var->var( ),
                        "source"   => $var->source( )
                        );

                    $host_vars[] = $var;
                }

                $host = array( "name" => $host->name( ), "vars" => $host_vars );
                $hosts[] = $host;

            }
            
            foreach( $group->groupVars( ) as $var )
            {
                $var = array(
                    "var"      => $var->var( ),
                    "source"   => $var->source( )
                    );

                $group_vars[] = $var;
            }                
          
            $group = array( 'name' => $group->name( ), 'hosts' => $hosts, 'vars' => $group_vars );
            $groups[] = $group;
        }

 
        //$json_string = json_encode( $groups, JSON_FORCE_OBJECT | JSON_PRETTY_PRINT );
        return $groups;
        //printf( "\n" . $json_string . "\n" );
    }

    public function dumpHosts( )
    {
        // If there are no groups, something went wrong

        if ( sizeof( $this->groups ) == 0 )
            return "";

        foreach ( $this->groups as $group )
        {
            foreach( $group->hosts( ) as $host )
            {
                printf( "[%s]:%s\n", $group->name( ), $host->name( ) );

                foreach( $host->hostVars( ) as $var )
                {
                    printf( "\tvar:%s (host)\n", $var->var( ) );
                }
            }

            foreach( $group->groupVars( ) as $var )
            {
                printf( "\tvar:%s (group)\n", $var->var( ) );
            }       
        }
    }

    protected function checkHostsFile( )
    {/*
        if ( file_exists( self::ANSIBLE_HOSTS_PATH ) )
        {
            return true;
        } else {
            return false;
        }*/
    }
}