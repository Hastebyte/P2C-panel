<?php

namespace Core;

class Job
{
    private $timestamp;
    private $type;
    private $result;
    private $directory;
    private $commandFile;
    private $resultFile;
    private $completeFile;
    private $typeFile;

    public function __construct( $directory, $timestamp, $type )
    {
        $this->timestamp        = $timestamp;
        $this->type             = $type;
        $this->directory        = $directory . "/" . $timestamp;
        $this->commandFile      = $this->directory . "/command";
        $this->resultFile       = $this->directory . "/result";
        $this->completeFile     = $this->directory . "/complete";
        $this->typeFile         = $this->directory . "/type";

        if ( file_exists( $this->typeFile ) )
            $this->type = file_get_contents( $this->typeFile );

        //if ( file_exists( $this->resultsFile ) )
        //    $result = file_get_contents($this->resultsFile );
    }

    public function execute( $command )
    {
        $command .= "; touch " . $this->completeFile; 

        touch( $this->typeFile );
        file_put_contents( $this->typeFile, $this->type );        
        
        // printf( "[+] writing to %s\n", $this->resultFile );
        $nohup_cmd = "/usr/bin/nohup /bin/sh -c '" . $command . "' > " . $this->resultFile .  " 2>&1 &";

        touch( $this->commandFile );
        file_put_contents( $this->commandFile, $nohup_cmd );

        // shell_exec seems to be blocking even with the nohup usage above
        //

        session_write_close( ); 
        exec( $nohup_cmd );
    }

    public function timestamp( )
    {
        return $this->timestamp;
    }

    public function type( )
    {
        return $this->type;
    }

    public function result( )
    {
        $content = file_get_contents( $this->resultFile );

        if ( $content === false )
            return false;

        if ( empty( $content ) )
            return false;   

        return $content;
    }

    public function isComplete( )
    {
        if ( file_exists( $this->completeFile ) )
            return true;

        return false;
    }

    public function __toString( )
    {
        $str = "[" . $this->timestamp . "] ";

        if ( !$this->complete( ) )
            return $str;

        $str .= "result:\n";
        $str .= $this->result( );
    }    
}

?>