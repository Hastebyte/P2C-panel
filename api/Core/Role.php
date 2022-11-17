<?php

namespace Core;

class Role
{
    private $name;
    private $defaults;
    private $handlers;
    private $meta;
    private $tasks;
    private $templates;
    private $tests;
    private $vars;

    const ANSIBLE_PLAYBOOKS_PATH = '/etc/ansible/playbooks';

    public function __construct( $name )
    {
        $this->name = $name;
        $this->parsed = false;
    }

    public function analyze( )
    {
        //$content = file_get_contents( self::ANSIBLE_PLAYBOOKS_PATH . '/' . $this->name );
        //$this->parsed = true;
        return true;
    }

    public function name( )
    {
        return $this->name;
    }

    public function defaults( )
    {
        return $this->defaults;
    }

    public function handlers( )
    {
        return $this->handlers;
    }

    public function meta( )
    {
        return $this->meta;
    }

    public function tasks( )
    {
        return $this->tasks;
    }

    public function templates( )
    {
        return $this->templates;
    }

    public function tests( )
    {
        return $this->tests;
    }

    public function vars( )
    {
        return $this->vars;
    }
}

?>