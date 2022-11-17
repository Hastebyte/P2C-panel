<?php

namespace Core;

abstract class E_VAR_TYPE
{
    const HOST_VARIABLE  = 0;   // Host variable
    const GROUP_VARIABLE = 1;   // Group variable
}

class Serial
{
    public $id;
    public $serial;
    public $duration;
    public $registered;
    public $created;
    public $commission;
    public $comments;
    public $computerid;
    public $lastip;
    public $gameid;
    public $resellerid;

    public $hours_remaining; // computer

    public function __construct( )
    {

    }
}

?>
