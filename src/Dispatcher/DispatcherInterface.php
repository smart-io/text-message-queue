<?php

namespace Smart\TextMessageQueue\Dispatcher;

interface DispatcherInterface
{
    /**
     * @param string $jobName
     * @param mixed $data
     *
     * @return mixed
     */
    public function dispatch($jobName, $data = null);
}
