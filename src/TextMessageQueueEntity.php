<?php

namespace Smart\TextMessageQueue;

use DateTime;
use Doctrine\Common\Collections\ArrayCollection;
use Smart\TextMessageQueue\MediaUrl\MediaUrlEntity;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass="Smart\TextMessageQueue\TextMessageQueueRepository")
 * @ORM\Table(name="text_message_queue")
 */
class TextMessageQueueEntity
{
    const LOCK_TIME = 'PT120S';

    /**
     * @ORM\Column(type="datetime", name="created_datetime")
     * @var DateTime
     */
    private $createdDatetime = null;

    /**
     * @ORM\Column(type="boolean", name="is_locked")
     * @var bool
     */
    private $isLocked = false;

    /**
     * @ORM\Column(type="datetime", name="locked_datetime", nullable=true)
     * @var DateTime|null
     */
    private $lockedDatetime = null;

    /**
     * @ORM\Id
     * @ORM\Column(type="integer")
     * @ORM\GeneratedValue
     * @var int
     */
    private $id;

    /**
     * @ORM\Column(type="array", name="`to`")
     * @var array
     */
    private $to = null;

    /**
     * @ORM\Column(type="text")
     * @var string
     */
    private $body;

    /**
     * @ORM\OneToMany(
     *   targetEntity="Smart\TextMessageQueue\MediaUrl\MediaUrlEntity",
     *   mappedBy="textMessageQueue",
     *   cascade={"persist"}
     * )
     * @var ArrayCollection
     **/
    private $mediaUrls;

    public function __construct()
    {
        $this->setCreatedDatetime(new DateTime('now'));
        $this->setMediaUrls(new ArrayCollection());
    }

    public function lock()
    {
        $this->setIsLocked(true);
        $this->setLockedDatetime(new DateTime('now'));
    }

    /**
     * @return DateTime
     */
    public function getCreatedDatetime()
    {
        return $this->createdDatetime;
    }

    /**
     * @param DateTime $createdDatetime
     *
     * @return $this
     */
    public function setCreatedDatetime(DateTime $createdDatetime)
    {
        $this->createdDatetime = $createdDatetime;

        return $this;
    }

    /**
     * @return boolean
     */
    public function isLocked()
    {
        return $this->isLocked;
    }

    /**
     * @param $isLocked
     *
     * @return $this
     */
    public function setIsLocked($isLocked)
    {
        $this->isLocked = $isLocked;

        return $this;
    }

    /**
     * @return DateTime|null
     */
    public function getLockedDatetime()
    {
        return $this->lockedDatetime;
    }

    /**
     * @param DateTime|null $lockedDatetime
     *
     * @return $this
     */
    public function setLockedDatetime($lockedDatetime)
    {
        $this->lockedDatetime = $lockedDatetime;

        return $this;
    }

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
     * @return array
     */
    public function getTo()
    {
        return $this->to;
    }

    /**
     * @param array $to
     *
     * @return $this
     */
    public function setTo($to)
    {
        $this->to = $to;

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
     * @return ArrayCollection
     */
    public function getMediaUrls()
    {
        return $this->mediaUrls;
    }

    /**
     * @param ArrayCollection $mediaUrls
     *
     * @return $this
     */
    public function setMediaUrls($mediaUrls)
    {
        $this->mediaUrls = $mediaUrls;

        return $this;
    }
}
