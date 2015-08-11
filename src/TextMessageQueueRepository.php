<?php

namespace Smart\TextMessageQueue;

use DateInterval;
use DateTime;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\EntityRepository;

class TextMessageQueueRepository extends EntityRepository
{
    /**
     * @param int $id
     * @return TextMessageQueueEntity|null
     */
    public function findOneUnlockedById($id)
    {

        return $this->createQueryBuilder('e')
            ->where('e.isLocked = 0')
            ->andWhere('e.id = '.$id)
            ->orWhere('e.lockedDatetime < :lockTimeExpiration')
            ->setParameter('lockTimeExpiration', $this->getLockTimeExpiration())
            ->getQuery()
            ->getOneOrNullResult();
    }

    /**
     * @return ArrayCollection
     */
    public function findAllUnlocked()
    {

        return $this->createQueryBuilder('e')
            ->where('e.isLocked = 0')
            ->orWhere('e.lockedDatetime < :lockTimeExpiration')
            ->setParameter('lockTimeExpiration', $this->getLockTimeExpiration())
            ->getQuery()
            ->getResult();
    }

    /**
     * @return DateTime
     */
    private function getLockTimeExpiration()
    {

        return (new DateTime)->sub(
            new DateInterval(TextMessageQueueEntity::LOCK_TIME)
        );
    }
}
