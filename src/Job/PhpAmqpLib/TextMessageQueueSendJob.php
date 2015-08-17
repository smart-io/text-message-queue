<?php

namespace Smart\TextMessageQueue\Job\PhpAmqpLib;

use PhpAmqpLib\Message\AMQPMessage;
use Smart\TextMessageQueue\Job\TextMessageQueueSendJob as BaseJob;

class TextMessageQueueSendJob extends BaseJob
{

    /**
     * @param AMQPMessage|null $message
     */
    public function execute(AMQPMessage $message = null)
    {

        if (!($message instanceof AMQPMessage)) {
            return;
        }

        parent::executeJob((int)$message->body);
    }
}
