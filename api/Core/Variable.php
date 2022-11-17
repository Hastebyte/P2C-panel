<?php

namespace Core;

abstract class E_VAR_TYPE
{
    const HOST_VARIABLE  = 0;   // Host variable
    const GROUP_VARIABLE = 1;   // Group variable
}

class Variable
{
    private $var;
    private $source;
    private $type;

    public function __construct( $var, $source, $type )
    {
        $this->var = $var;
        $this->source = $source;
        $this->type = $type;
    }

    public function var( )
    {
        return $this->var;
    }

    public function source( )
    {
        return $this->source;
    }

    public function type( )
    {
        return $this->type;
    }
}

?>