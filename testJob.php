<?php
// testJob.php

require_once 'JobRunner.php';
require_once 'jobs/SuccessJob.php';

// Create a new instance of the JobRunner
$jobRunner = new JobRunner();

// Run the SuccessJob in the background with some parameters
$jobRunner->runBackgroundJob('SuccessJob', 'handle', ['param1' => 'value1', 'param2' => 'value2']);

