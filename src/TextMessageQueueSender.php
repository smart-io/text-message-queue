<?php

namespace Smart\TextMessageQueue;

use Exception;
use Psr\Log\LoggerInterface;
use Smart\TextMessageQueue\Driver\DriverInterface;

class TextMessageQueueSender
{
    /**
     * @var string
     */
    protected $errorMessage;

    /**
     * @var DriverInterface
     */
    protected $driver;

    /**
     * @var LoggerInterface
     */
    protected $textMessageQueueLogger;

    /**
     * @param DriverInterface $driver
     * @param LoggerInterface $textMessageQueueLogger
     */
    public function __construct(
        DriverInterface $driver,
        LoggerInterface $textMessageQueueLogger = null
    ) {
        $this->driver = $driver;
        $this->textMessageQueueLogger = $textMessageQueueLogger;
    }

    /**
     * @param TextMessageQueueEntity $textMessageQueue
     *
     * @return bool
     */
    public function send(TextMessageQueueEntity $textMessageQueue)
    {
        try {
            if ($this->driver->send($textMessageQueue)) {
                $this->textMessageQueueLogger->info($textMessageQueue->getBody(),
                    [
                        'fromNumber' => $textMessageQueue->getFromNumber(),
                        'to' => $textMessageQueue->getTo(),
                        'mediaUrls' => $textMessageQueue->getMediaUrls(),
                    ]);

                return true;
            }
        } catch (Exception $e) {
            $this->errorMessage = $e->getMessage();
            $this->textMessageQueueLogger->error($this->errorMessage, [
                'fromNumber' => $textMessageQueue->getFromNumber(),
                'to' => $textMessageQueue->getTo(),
                'mediaUrls' => $textMessageQueue->getMediaUrls(),
            ]);

            return false;
        }

        return false;
    }

    /**
     * @return string
     */
    public function getError()
    {
        return $this->errorMessage;
    }
}
