<?php

namespace Smart\TextMessageQueue\Driver;

use Smart\TextMessageQueue\TextMessageQueueEntity;

interface DriverInterface
{
    /**
     * @param TextMessageQueueEntity $textMessageQueue
     *
     * @return bool
     */
    public function send(TextMessageQueueEntity $textMessageQueue);
}
