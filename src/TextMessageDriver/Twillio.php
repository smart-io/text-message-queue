<?php

namespace Smart\TextMessageQueue\TextMessageDriver;

use Exception;
use Services_Twilio;
use Smart\TextMessageQueue\TextMessageQueueEntity;

class Twillio implements TextMessageDriverInterface
{
    /**
     * @var string
     */
    private $accountId;

    /**
     * @var string
     */
    private $authToken;

    /**
     * @var string
     */
    private $fromNumber;

    /**
     * @param string $acountId
     * @param string $authToken
     * @param string $fromNumber
     */
    public function __construct($acountId, $authToken, $fromNumber)
    {

        $this->setAccountId($acountId);
        $this->setAuthToken($authToken);
        $this->setFromNumber($fromNumber);
    }

    /**
     * @param TextMessageQueueEntity $textMessageQueue
     *
     * @return bool
     * @throws Exception
     */
    public function send(TextMessageQueueEntity $textMessageQueue)
    {

        $client = new Services_Twilio($this->getAccountId(),
            $this->getAuthToken());

        foreach ($textMessageQueue->getTo() as $recipientNumber) {

            $message = $client->account->messages->create(array(
                "From" => $this->getFromNumber(),
                "To" => $recipientNumber,
                "Body" => $textMessageQueue->getBody(),
            ));
        }

        return !empty($message) && !empty($message->sid);
    }

    /**
     * @return string
     */
    public function getAccountId()
    {
        return $this->accountId;
    }

    /**
     * @param string $accountId
     *
     * @return $this
     */
    public function setAccountId($accountId)
    {
        $this->accountId = $accountId;

        return $this;
    }

    /**
     * @return string
     */
    public function getAuthToken()
    {
        return $this->authToken;
    }

    /**
     * @param string $authToken
     *
     * @return $this
     */
    public function setAuthToken($authToken)
    {
        $this->authToken = $authToken;

        return $this;
    }

    /**
     * @return string
     */
    public function getFromNumber()
    {
        return $this->fromNumber;
    }

    /**
     * @param string $fromNumber
     *
     * @return $this
     */
    public function setFromNumber($fromNumber)
    {
        $this->fromNumber = $fromNumber;

        return $this;
    }
}
