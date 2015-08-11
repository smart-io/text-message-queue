<?php

namespace Smart\TextMessageQueue\TextMessageDriver;

use Smart\TextMessageQueue\TextMessageQueueEntity;

interface TextMessageDriverInterface
{
    /**
     * @param TextMessageQueueEntity $textMessageQueue
     *
     * @return bool
     */
    public function send(TextMessageQueueEntity $textMessageQueue);
}
