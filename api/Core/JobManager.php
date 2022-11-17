<?php

namespace Core;

require_once( __DIR__ . '/../Util/API.php' );
require_once( __DIR__ . '/../Util/Format.php' );
require_once( __DIR__ . '/Job.php' );

class JobManager
{
    private $directory;
    private $jobs;

    const E_STATUS_FAILED    = -1;
    const E_STATUS_PENDING   =  0;
    const E_STATUS_COMPLETE  =  1;

    /**
    * Constructs the command manager
    * 
    * @param {String} directory where meta information about jobs is stored, the default is
    * the root php service directory + /Runs/ (consider revising this)
    *
    * @return null
    */

    public function __construct( $directory )
    {
        $this->directory = $directory;
        $this->jobs = array( );
    }

    public function newJob( $commandLine, $type )
    {
        // Check if the runs directory exists

        if ( !file_exists( $this->directory ) )
        {
            \Util\SendJSONError( 500, "Runs directory: " . $this->directory . " does not exist" );
            die( );
        }

        $date_timestamp = date_create( );
        $stamp = date_timestamp_get( $date_timestamp );
        $job_directory = $this->directory . $stamp;
        
        // If two jobs are sent in at the same second, let's wait and try again
        //

        if ( !mkdir( $job_directory ) )
        {
            sleep( 1 );
            $date_timestamp = date_create( );
            $stamp = date_timestamp_get( $date_timestamp );
            $job_directory = $this->directory . $stamp;

            if ( !mkdir( $job_directory ) )
            {
                \Util\SendJSONError( 500, "Could not create a new directory inside of the Runs folder " . exec('whoami') );
                die( );            
            }
        }

        $job = new Job( $this->directory, $stamp, $type );
        $job->execute( $commandLine );
        array_push( $this->jobs, $job );        

        return $stamp;
    }

    public function getJobStatus( $jobId, &$output )
    {
        if ( !file_exists( $this->directory . $jobId ) )
            return self::E_STATUS_FAILED;

        if ( !file_exists( $this->directory . $jobId . '/complete' ) )
            return self::E_STATUS_PENDING;

        $content = file_get_contents( $this->directory . $jobId . '/result' );

        if ( $content === false )
            return self::E_STATUS_FAILED;

        if ( empty( $content ) )
            return self::E_STATUS_FAILED;

        $output = $content;
        return self::E_STATUS_COMPLETE;
    }

    public function parseJobRequest( $request )
    {
        if ( $request['action'] == 'createJob' )
        {
            if ( !array_key_exists( 'jobType', $request ) )
            {
                \Util\SendJSONError( 500, "Invalid request received, jobType parameter is missing" );
                return false;
            }

            if ( !array_key_exists( 'jobTarget', $request ) )
            {
                \Util\SendJSONError( 500, "Invalid request received, jobTarget parameter is missing" );
                return false;
            }

            $jobId = "";

            switch( $request['jobType'] )
            {
                case 'ping':
                    $jobId = $this->newJob( "ansible -m ping " . $request['jobTarget'], "ping" );
                    \Util\SendJSONArray( array( 'scheduled' => 1, 'jobId' => ( string )$jobId, 'target' => $request['jobTarget'] ) );               
                    break;
                case 'sleep':
                    $jobId = $this->newJob( "ansible -m raw -a \"sleep 6\" " . $request['jobTarget'], "sleep --key-file /home/sysadmin/.ssh/id_rsa.pub" );
                    \Util\SendJSONArray( array( 'scheduled' => 1, 'jobId' => ( string )$jobId, 'target' => $request['jobTarget'] ) );  
                    break;
                case 'playbook':
                    break;
                default:
                    \Util\SendJSONError( 500, "Invalid request received, unknown jobType" );
                    break;      
            }

        } else if ( $request['action'] = 'getJobStatus' ) {

            if ( !array_key_exists( 'jobId', $request ) )
            {
                \Util\SendJSONError( 500, "Invalid request received, jobId parameter is missing" );
                return false;
            }

            $content = "";
            $status = $this->getJobStatus( $request['jobId'], $content );
            \Util\SendJSONArray( array( 'status' => $status, 'output' => $content ) );
        }

        return true;

        /*
        if ( !array_key_exists( 'playbook', $request ) )
        {
            SendJSONError( 400, "Invalid request received, jobType parameter is missing" );
            return false;
        } 
        */   
    }

    public function scanJobs( )
    {
        if ( !file_exists( $this->directory ) )
        {
            if ( !mkdir( $this->directory ) )
            {
                \Util\SendJSONError( 500, "Runs directory: " . $this->directory . " does not exist" );
                return false;
            }
        }

        $files = scandir( $this->directory );

        if ( $files === false )
            return false;

        $folders = array_diff( $files, array( '..', '.' ) );

        foreach( $folders as $folder )
        {
            // The constructor will parse the type, if a type file exists
            // in the runs folder

            $job = new Job( $this->directory, $folder, "" );
            array_push( $this->jobs, $job );
        }
    }

    /**
    * Executes a console command (like execute the command via putty) in
    * a linux environment or windows from php without await for the result.
    * 
    * Useful for execute extense tasks.
    * 
    * @param {String} $command
    */

    public function exportToArray( )
    {  
        $obj = array( );

        foreach ( $this->jobs as $job )
        {
            $timestamp = $job->timestamp( );
            $result = "";

            $job = array (
                "timestamp" => $timestamp,
                "status"    => $this->getJobStatus( $timestamp, $result ),
                "result"    => $result,
                "type"      => $job->type( ),
            );

            $obj[] = $job;
        }                

        // Shows most newest jobs first
        //
        $obj = array_reverse( $obj );
        return $obj;
        
        //$json_string = json_encode( $json, JSON_FORCE_OBJECT | JSON_PRETTY_PRINT );
        //printf( "\n" . $json_string . "\n" );
    }

}

?>