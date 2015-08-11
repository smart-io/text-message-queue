<?php

namespace Smart\TextMessageQueue\Worker;

interface WorkerDriverInterface
{
    /**
     * @param string $jobName
     * @param mixed $data
     * @return bool
     */
    public function execute($jobName, $data = null);
}
