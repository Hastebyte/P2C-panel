<?php

namespace Core;

require_once( __DIR__ . '/Host.php' );

class Group
{
    private $name;
    private $hosts;
    private $groupVars;

    public function __construct( $name )
    {
        $this->name = $name;
        $this->hosts = array( );
        $this->groupVars = array( );
    }

    public function addHost( &$host )
    {
        array_push( $this->hosts, $host );
    }

    public function name( )
    {
        return $this->name;
    }

    public function hosts( )
    {
        return $this->hosts;
    }

    public function groupVars( )
    {
        return $this->groupVars;
    }

    public function addGroupVar( $var, $source )
    {
        array_push( $this->groupVars, new Variable( $var, $source, 1 ) );
    }
}

?>