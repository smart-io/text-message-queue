<?php

namespace Smart\TextMessageQueue\Driver;

use Exception;
use Smart\TextMessageQueue\TextMessageQueueEntity;

class Twillio implements DriverInterface
{
    /**
     * @var string
     */
    private $apiKey;

    /**
     * @param $apiKey
     */
    public function __construct($apiKey)
    {
        $this->setApiKey($apiKey);
    }

    /**
     * @param TextMessageQueueEntity $textMessageQueue
     *
     * @return bool
     * @throws Exception
     */
    public function send(TextMessageQueueEntity $textMessageQueue)
    {

        //todo : implement twillio
        return false;
    }

    /**
     * @return string
     */
    public function getApiKey()
    {
        return $this->apiKey;
    }

    /**
     * @param string $apiKey
     *
     * @return $this
     */
    public function setApiKey($apiKey)
    {
        $this->apiKey = $apiKey;

        return $this;
    }
}
