<?php

// Include the Logger class
require_once 'Logger.php';

// Dynamically include the job class from the 'jobs' folder
function autoloadJobClass($className)
{
    // Convert the namespace to a file path, ensuring no extra "Jobs" directory is used
    $classPath = 'jobs/' . str_replace('\\', '/', $className) . '.php';

    // Check if the class file exists
    if (file_exists($classPath)) {
        require_once $classPath;
    } else {
        echo "Attempting to load: $classPath\n";
    }
}

// Register the autoloader for job classes
spl_autoload_register('autoloadJobClass');

function runBackgroundJob($className, $method, $params = [], $maxRetries = 3)
{
    $logger = new Logger();
    $attempt = 0;

    while ($attempt < $maxRetries) {
        try {
            // Log the attempt to run the job
            $logger->log("Attempt $attempt: Trying to run $className::$method with parameters: " . json_encode($params));

            // Check if the class exists
            if (!class_exists($className)) {
                throw new \Exception("Class $className not found.");
            }

            // Create an instance of the job class
            $job = new $className();

            // Check if the method exists in the job class
            if (!method_exists($job, $method)) {
                throw new \Exception("Method $method not found in class $className.");
            }

            // Log the background job execution
            $logger->log("$className::$method is running in the background.");

            // Execute the method with parameters
            call_user_func_array([$job, $method], $params);

            // Log the success of the job
            $logger->log("$className::$method executed successfully with parameters: " . json_encode($params));
            return; // Job executed successfully, exit the loop

        } catch (\Exception $e) {
            // Log the error message
            $errorMsg = "Attempt $attempt - Error: " . $e->getMessage();
            $logger->log($errorMsg, 'error');

            // Write the error to the error log file
            file_put_contents('logs/background_jobs_errors.log', "[" . date('Y-m-d H:i:s') . "] $errorMsg\n", FILE_APPEND);

            // Retry the job if max retries are not reached
            if ($attempt + 1 < $maxRetries) {
                $attempt++;
                $logger->log("Retrying $className::$method, attempt $attempt", 'warning');
                // Optional: Delay before retrying (e.g., 2 seconds)
                sleep(2);
            } else {
                // If all retries failed, log and exit
                $logger->log("Max retries reached for $className::$method. Job failed.", 'error');
                echo "Max retries reached. Job failed.\n";
                return;
            }
        }
    }
}

