<?php

namespace Core;

require_once( __DIR__ . '/Variable.php' );

abstract class E_HOSTVAR_SOURCE
{
    const HOSTS_FILE = 0;   // variable came from /etc/ansible/hosts
    const FOLDER = 1;       // variable came from /etc/ansible/host_vars/<name>
}

class Host
{
    private $name;
    private $group;
    private $hostVars;
    private $source;
    private $geoLocation;

    public function __construct( $name, $group = "" )
    {
        $this->hostVars = array( );
        $this->name = $name;
        $this->group = $group;
        $this->geoLocation = "de";
    }

    public function name( )
    {
        return $this->name;
    }

    public function group( )
    {
        return $this->group;
    }

    public function hostVars( )
    {
        return $this->hostVars;
    }

    public function geoLocation( )
    {
        return $this->geoLocation;
    }

    public function addHostVar( $var, $source )
    {
        array_push( $this->hostVars, new Variable( $var, $source, 0 ) );
    }

    public function addHostToInventory( $host, $source, $comment )
    {

    }
}

?>