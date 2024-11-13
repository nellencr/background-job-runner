<?php
// jobs/SuccessJob.php

class SuccessJob
{
    public function handle($params)
    {
        // Simulate doing some work
        echo "Successfully executed SuccessJob with parameters: " . json_encode($params) . "\n";
    }
}

