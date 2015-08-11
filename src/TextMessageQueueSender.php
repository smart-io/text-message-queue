<?php

namespace Smart\TextMessageQueue;

use Exception;
use Psr\Log\LoggerInterface;
use Smart\TextMessageQueue\TextMessageDriver\TextMessageDriverInterface;

class TextMessageQueueSender
{
    /**
     * @var string
     */
    protected $errorMessage;

    /**
     * @var TextMessageDriverInterface
     */
    protected $driver;

    /**
     * @var LoggerInterface
     */
    protected $textMessageQueueLogger;

    /**
     * @param TextMessageDriverInterface $driver
     * @param LoggerInterface $textMessageQueueLogger
     */
    public function __construct(
        TextMessageDriverInterface $driver,
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
                        'to' => $textMessageQueue->getTo(),
                        'mediaUrls' => $textMessageQueue->getMediaUrls(),
                    ]);

                return true;
            }
        } catch (Exception $e) {
            $this->errorMessage = $e->getMessage();
            $this->textMessageQueueLogger->error($this->errorMessage, [
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
