<?php

namespace Smart\TextMessageQueue;

use Doctrine\ORM\EntityManager;
use Smart\TextMessageQueue\Job\TextMessageQueueSendJob;
use Smart\TextMessageQueue\MediaUrl\MediaUrlEntity;
use Smart\TextMessageQueue\Worker\WorkerDriverInterface;

abstract class TextMessage
{
    /**
     * @var array
     */
    protected $to = [];

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
     * @var WorkerDriverInterface
     */
    protected $worker;

    /**
     * @param EntityManager $entityManager
     * @param WorkerDriverInterface    $worker
     */
    public function __construct(
        EntityManager $entityManager,
        WorkerDriverInterface $worker
    ) {
        $this->entityManager = $entityManager;
        $this->worker = $worker;
    }

    /**
     * @return bool
     */
    public function create()
    {

        $textMessage = (new TextMessageQueueEntity())
            ->setTo($this->getTo())
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

        $this->worker->execute(TextMessageQueueSendJob::JOB_NAME, $textMessage->getId());

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
