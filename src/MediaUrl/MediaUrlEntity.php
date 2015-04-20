<?php

namespace Smart\TextMessageQueue\MediaUrl;

use Smart\TextMessageQueue\TextMessageQueueEntity;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity
 * @ORM\Table(name="text_message_queue_media_url")
 */
class MediaUrlEntity
{
    /**
     * @ORM\Id
     * @ORM\Column(type="integer")
     * @ORM\GeneratedValue
     * @var int
     */
    private $id;

    /**
     * @ORM\ManyToOne(targetEntity="Smart\TextMessageQueue\TextMessageQueueEntity", inversedBy="mediaUrls")
     * @ORM\JoinColumn(name="text_message_queue_id", referencedColumnName="id", onDelete="CASCADE")
     * @var TextMessageQueueEntity
     */
    private $textMessageQueue;

    /**
     * @ORM\Column(type="string", length=255)
     * @var string
     */
    private $url;

    /**
     * @return int
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @param int $id
     *
     * @return $this
     */
    public function setId($id)
    {
        $this->id = $id;

        return $this;
    }

    /**
     * @return TextMessageQueueEntity
     */
    public function getTextMessageQueue()
    {
        return $this->textMessageQueue;
    }

    /**
     * @param TextMessageQueueEntity $textMessageQueue
     *
     * @return $this
     */
    public function setTextMessageQueue(
        TextMessageQueueEntity $textMessageQueue
    ) {
        $this->textMessageQueue = $textMessageQueue;

        return $this;
    }

    /**
     * @return string
     */
    public function getUrl()
    {
        return $this->url;
    }

    /**
     * @param string $url
     *
     * @return $this
     */
    public function setUrl($url)
    {
        $this->url = $url;

        return $this;
    }
}
