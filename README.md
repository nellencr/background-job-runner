
# Background Job Runner for Laravel

This repository provides a background job runner system for handling jobs within a Laravel environment. It allows you to run jobs with retry attempts, logs, and security measures to prevent unauthorized code execution. This solution is ideal for executing long-running or potentially unreliable tasks asynchronously.

## Installation

To set up the background job runner system:

1. **Clone this repository** to your Laravel project directory:
    ```bash
    git clone https://github.com/nellencr/background-job-runner.git

    ```
2. Navigate to the project directory.
3. Run the following command to install the necessary dependencies:
   ```bash
   composer install
   ```


## Usage
#### Running Jobs with Parameters
You can pass parameters to the job method by providing them as an associative array. The runBackgroundJob function will automatically handle the parameters.
#### Example:
```php
runBackgroundJob('Jobs\\FailingJob', 'handle', ['param1' => 'value1', 'param2' => 'value2']);
```

### Basic Usage

To run a background job, simply call the `runBackgroundJob` function with the fully qualified class name and the method to run.
#### Retry Attempts and Delays
The background job runner supports retrying a job up to a maximum number of times in case of failure. You can specify the number of retry attempts and delay between retries.

#### Example with retries:
```php
runBackgroundJob('Jobs\\FailingJob', 'handle', ['param1' => 'value1', 'param2' => 'value2'], 5);
```
- Max retries: The maximum number of retry attempts. Default is 3.
- Retry delay: A 2-second delay is added between retries. This can be modified by updating the sleep(2) delay in the helpers.php file.

### Configuration
#### Security Settings
- To prevent unauthorized execution of harmful code, the system ensures that only pre-approved job classes and methods can be run. The list of approved classes is stored in the helpers.php file.

#### Example:
```php // Approved classes
$approvedClasses = [
    'Jobs\\SuccessJob',
    'Jobs\\FailingJob',
];
```
- Only these classes will be allowed to run through the runBackgroundJob function.
- Any attempt to run a job outside of this list will result in an exception.
- Validation and Sanitization: Class and method names are validated to ensure only authorized jobs can be run.
- Whitelisted Jobs: Only pre-approved classes can be executed. This prevents the execution of any arbitrary class that could pose a security risk.

  ### Testing
#### Sample Test Cases
Here are some sample test cases you can use to test the background job runner system:

#### Test Case 1: Run a SuccessJob
```php
runBackgroundJob('Jobs\\SuccessJob', 'handle', ['param1' => 'value1', 'param2' => 'value2']);
```
- Expected Result: The job should run successfully and log the output.
- Logs: Check the background_jobs.log file for success logs.

#### Test Case 2: Run a FailingJob with retries
```php
runBackgroundJob('Jobs\\FailingJob', 'handle', ['param1' => 'value1', 'param2' => 'value2'], 3);
```
- Expected Result: The job should fail 3 times and then log the error.
- Logs: Check the background_jobs_errors.log file for error logs.

#### Test Case 3: Unauthorized Job Class
```php
runBackgroundJob('Jobs\\UnauthorizedJob', 'handle', ['param1' => 'value1']);
```
- Expected Result: An exception should be thrown for the unauthorized job class.
- Logs: The error log should indicate an unauthorized class.

  ## Logs
**The system maintains logs for both successful and failed job executions.**

- Success Logs: Stored in logs/background_jobs.log.
- Error Logs: Stored in logs/background_jobs_errors.log.
Each entry includes a timestamp, job name, and any error messages if applicable.

