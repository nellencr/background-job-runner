<?php

class Logger
{
    private $logFile;

    public function __construct()
    {
        $this->logFile = 'logs/background_jobs.log'; // Log file for successful job executions
    }

    public function log($message, $level = 'info')
    {
        // Set the log message format
        $logMessage = "[" . date('Y-m-d H:i:s') . "] [$level] $message\n";

        // Write the log message to the log file
        file_put_contents($this->logFile, $logMessage, FILE_APPEND);
    }
}

