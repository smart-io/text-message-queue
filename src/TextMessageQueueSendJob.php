<?php

namespace Smart\TextMessageQueue;

use Doctrine\ORM\EntityManager;
use GearmanJob;
use Psr\Log\LoggerInterface;
use Smart\TextMessageQueue\Driver\DriverInterface;
use Sinergi\Gearman\JobInterface;
use Doctrine\ORM\EntityRepository;

class TextMessageQueueSendJob implements JobInterface
{
    const JOB_NAME = 'textmessagequeue:send';
    const TIMEOUT = 60;

    /**
     * @var DriverInterface
     */
    private $driver;

    /**
     * @var EntityManager
     */
    private $entityManager;

    /**
     * @var TextMessageQueueRepository
     */
    private $textMessageQueueRepository;

    /**
     * @var LoggerInterface
     */
    private $textMessageQueueLogger;

    /**
     * @param DriverInterface                             $driver
     * @param EntityManager                               $entityManager
     * @param TextMessageQueueRepository|EntityRepository $textMessageQueueRepository
     * @param null|LoggerInterface                        $textMessageQueueLogger
     */
    public function __construct(
        DriverInterface $driver,
        EntityManager $entityManager,
        TextMessageQueueRepository $textMessageQueueRepository,
        LoggerInterface $textMessageQueueLogger = null
    ) {
        $this->driver = $driver;
        $this->entityManager = $entityManager;
        $this->textMessageQueueRepository = $textMessageQueueRepository;
        $this->textMessageQueueLogger = $textMessageQueueLogger;
    }

    /**
     * @return string
     */
    public function getName()
    {
        return self::JOB_NAME;
    }

    /**
     * @param GearmanJob|null $job
     *
     * @return mixed
     */
    public function execute(GearmanJob $job = null)
    {

        $this->textMessageQueueLogger->info('Text message queue started');

        $this->entityManager->getConnection()->close();
        $this->entityManager->getConnection()->connect();

        $timeStart = time();
        $this->entityManager->flush();
        $this->entityManager->clear();

        $messages = $this->textMessageQueueRepository->findAllUnlocked();
        foreach ($messages as $message) {
            $this->entityManager->refresh($message);
            $message->lock();
            $this->entityManager->persist($message);
        }

        $this->entityManager->flush();

        foreach ($messages as $message) {
            if (time() > ($timeStart + self::TIMEOUT)) {
                break;
            }

            $textMessageSender = (new TextMessageQueueSender($this->driver,
                $this->textMessageQueueLogger));

            if ($textMessageSender->send($message)) {
                $this->entityManager->getConnection()->close();
                $this->entityManager->getConnection()->connect();

                $this->entityManager->remove($message);
                $this->entityManager->flush($message);
            }
        }

        $this->entityManager->getConnection()->close();
    }
}
