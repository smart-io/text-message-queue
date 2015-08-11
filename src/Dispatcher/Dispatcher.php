<?php

namespace Smart\TextMessageQueue\Dispatcher;

use Smart\TextMessageQueue\Worker\WorkerDriverInterface;

class Dispatcher implements DispatcherInterface
{
    /**
     * @var WorkerDriverInterface
     */
    protected $worker;

    /**
     * @param WorkerDriverInterface $worker
     */
    public function __construct(WorkerDriverInterface $worker)
    {
        $this->setWorker($worker);
    }

    /**
     * @return WorkerDriverInterface
     */
    public function getWorker()
    {
        return $this->worker;
    }

    /**
     * @param WorkerDriverInterface $worker
     *
     * @return $this
     */
    public function setWorker(WorkerDriverInterface $worker)
    {
        $this->worker = $worker;

        return $this;
    }

    /**
     * @param string $jobName
     * @param mixed  $data
     *
     * @return mixed
     */
    public function dispatch($jobName, $data = null)
    {

        $this->getWorker()->execute($jobName, $data);
    }
}
