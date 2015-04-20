<?php

namespace Smart\TextMessageQueue;

use Doctrine\ORM\EntityManager;
use Sinergi\Gearman\Dispatcher;
use Smart\TextMessageQueue\MediaUrl\MediaUrlEntity;

abstract class TextMessage
{
    /**
     * @var array
     */
    protected $to = [];

    /**
     * @var string
     */
    protected $fromNumber;

    /**
     * @var string
     */
    protected $body;

    /**
     * @var array
     */
    protected $mediaUrls = [];

    /**
     * @var EntityManager
     */
    protected $entityManager;

    /**
     * @var Dispatcher
     */
    protected $dispatcher;

    /**
     * @param EntityManager $entityManager
     * @param Dispatcher    $dispatcher
     */
    public function __construct(
        EntityManager $entityManager,
        Dispatcher $dispatcher
    ) {
        $this->entityManager = $entityManager;
        $this->dispatcher = $dispatcher;
    }

    /**
     * @return bool
     */
    public function create()
    {

        $textMessage = (new TextMessageQueueEntity())
            ->setTo($this->getTo())
            ->setFromNumber($this->getFromNumber())
            ->setBody($this->getBody());

        foreach ($this->getMediaUrls() as $mediaUrl) {

            $textMessage->getMediaUrls()->add(
                (new MediaUrlEntity())
                    ->setTextMessageQueue($textMessage)
                    ->setUrl($mediaUrl)
            );
        }

        $this->entityManager->persist($textMessage);
        $this->entityManager->flush($textMessage);

        $this->dispatcher->background(TextMessageQueueSendJob::JOB_NAME, null,
            null,
            TextMessageQueueSendJob::JOB_NAME);

        return true;
    }

    /**
     * @return array
     */
    public function getTo()
    {
        return $this->to;
    }

    /**
     * @param string $phoneNumber
     *
     * @return $this
     */
    public function addTo($phoneNumber)
    {
        $this->to[] = $phoneNumber;

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

    /**
     * @return string
     */
    public function getBody()
    {
        return $this->body;
    }

    /**
     * @param string $body
     *
     * @return $this
     */
    public function setBody($body)
    {
        $this->body = $body;

        return $this;
    }

    /**
     * @return array
     */
    public function getMediaUrls()
    {
        return $this->mediaUrls;
    }

    /**
     * @param string $url
     *
     * @return $this
     */
    public function addMediaUrls($url)
    {
        $this->mediaUrls[] = $url;

        return $this;
    }
}
