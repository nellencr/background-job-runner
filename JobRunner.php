<?php
// JobRunner.php

require_once 'Logger.php';

class JobRunner
{
    public function runBackgroundJob($className, $methodName, $params = [], $maxRetries = 3)
    {
        // Log the job attempt
        Logger::log("Attempting to run $className::$methodName with parameters: " . json_encode($params));

        // First, check if the class exists
        if (!class_exists($className)) {
            $errorMessage = "Error: Class $className does not exist.";
            Logger::log($errorMessage, true);
            echo $errorMessage . "\n";
            return;
        }

        // Create an instance of the class
        $job = new $className();

        // Check if the method exists in the class
        if (!method_exists($job, $methodName)) {
            $errorMessage = "Error: Method $methodName does not exist in class $className.";
            Logger::log($errorMessage, true);
            echo $errorMessage . "\n";
            return;
        }

        // Try to execute the job and catch any exceptions
        try {
            $job->$methodName($params);
            $successMessage = "$className::$methodName executed successfully with parameters: " . json_encode($params);
            Logger::log($successMessage);
            echo $successMessage . "\n";
        } catch (Exception $e) {
            $errorMessage = "Error while executing $className::$methodName: " . $e->getMessage();
            Logger::log($errorMessage, true);
            echo $errorMessage . "\n";
        }

        // Build the command to run the job in the background
        $command = "php -r 'require_once \"JobRunner.php\"; runBackgroundJob(\"$className\", \"$methodName\", " . json_encode($params) . ");'";

        // Check the OS and use the appropriate command for background execution
        if (strtoupper(substr(PHP_OS, 0, 3)) === 'WIN') {
            // If it's a Windows system, use `start /B` to run in the background
            $command = "start /B $command";
        } else {
            // If it's a Unix-based system, use `nohup` to run in the background
            $command = "nohup $command > /dev/null 2>&1 &";
        }

        // Execute the command to run the job in the background
        exec($command);
        Logger::log("Job $className::$methodName is running in the background.");
    }
}

