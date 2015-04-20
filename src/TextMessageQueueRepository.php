<?php

namespace Smart\TextMessageQueue;

use DateInterval;
use DateTime;
use Doctrine\ORM\EntityRepository;

class TextMessageQueueRepository extends EntityRepository
{
    /**
     * @return TextMessageQueueEntity[]
     */
    public function findAllUnlocked()
    {
        $twoMinutesAgo
            = (new DateTime)->sub(new DateInterval(TextMessageQueueEntity::LOCK_TIME));

        return $this->createQueryBuilder('e')
            ->where('e.isLocked = 0')
            ->orWhere('e.lockedDatetime < :twoMintuesAgo')
            ->setParameter('twoMintuesAgo', $twoMinutesAgo)
            ->getQuery()
            ->getResult();
    }
}
