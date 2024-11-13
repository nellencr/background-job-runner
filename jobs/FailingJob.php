<?php

namespace Jobs;

class FailingJob
{
    public function handle($param1, $param2)
    {
        echo "Error: This job is failing intentionally.\n";
        // Simulate failure by throwing an exception
        throw new \Exception("Job failed");
    }
}

