<?php

// requires YAML to be installed
// $ sudo dnf install php-pecl-yaml

namespace Core;

class Playbook
{
    private $name;
    private $yaml;
    private $parsed;
    private $hosts;
    private $tasks; // full task list
    private $roles;

    const ANSIBLE_PLAYBOOKS_PATH = '/etc/ansible/playbooks';

    public function __construct( $name )
    {
        $this->name = $name;
        $this->parsed = false;
    }

    public function analyze( )
    {
        // printf( "[+] Parsing: %s\n", $this->name );

        $content = file_get_contents( self::ANSIBLE_PLAYBOOKS_PATH . '/' . $this->name );

        if ( $content === false )
            return false;

        if ( empty( $content ) )
            return false;

        $this->yaml = yaml_parse( $content );
        
        if ( empty( $this->yaml ) )
        {
            // printf( "[!] Failed to parse playbook %s\n", $this->name );
            return false;
        }

        foreach ( $this->yaml[0] as $key => $value )
        {
            // This is never treated as an array, even if multiple hosts are supplied using the colon delimeter
            //

            if ( $key == 'hosts' )
                $hosts = $value;

            // This key can be parsed as nested array if the following format is used:
            // { role: foo_app_instance, dir: '/opt/a', app_port: 5000 }
            // todo: parse and store these variables somewhere

            if ( $key == 'roles' && is_array( $value ) )
            {
               foreach ( $value as $role )
               {
                    if ( !is_array( $role ) )
                    {
                        $this->roles[] = $role;

                    } else {
                        
                        if ( array_key_exists( 'role', $role ) )
                        {
                            $this->roles[] = $role['role'];
                        }
                    }
               }

            }

            // tasks should always be parsed as an array, even if there is only one
            //

            if ( $key == 'tasks' && is_array( $value ) && !empty( $value ) )
            {
                $this->tasks = $value;
            }
        }

        $this->parsed = true;
        return true;
    }

    public function name( )
    {
        return $this->name;
    }

    public function hosts( )
    {
        return $this->hosts;
    }

    public function roles( )
    {
        return $this->roles;
    }

    public function tasks( )
    {
        return $this->tasks;
    }
}

?>