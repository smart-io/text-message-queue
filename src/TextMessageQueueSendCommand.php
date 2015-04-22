<?php

namespace Smart\TextMessageQueue;

use Doctrine\ORM\EntityManager;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Sinergi\Gearman\Dispatcher;

class TextMessageQueueSendCommand extends Command
{
    const COMMAND_NAME = 'textmessagequeue:sendPending';

    /**
     * @var EntityManager
     */
    private $entityManager;

    /**
     * @var Dispatcher
     */
    private $gearmanDispatcher;

    /**
     * @param EntityManager $entityManager
     * @param Dispatcher    $gearmanDispatcher
     */
    public function __construct(
        EntityManager $entityManager,
        Dispatcher $gearmanDispatcher
    ) {

        $this->setEntityManager($entityManager);
        $this->setGearmanDispatcher($gearmanDispatcher);

        parent::__construct();
    }

    protected function configure()
    {
        $this->setName(self::COMMAND_NAME)
            ->setDescription('This sends all pending text messages queue');
    }

    /**
     * @param InputInterface  $input
     * @param OutputInterface $output
     *
     * @return void
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $textMessageRepository = $this->getEntityManager()
            ->getRepository(TextMessageQueueEntity::class);

        /** @var TextMessageQueueRepository $textMessageRepository */

        $pendingMessages = $textMessageRepository->findAllUnlocked();

        if (empty($pendingMessages)) {

            $output->write('No pending messages...');

            return;
        }

        $output->write('Sending ' . count($pendingMessages)
            . ' text messages to gearman: ');

        foreach ($pendingMessages as $pendingMessage) {

            /** @var TextMessageQueueEntity $pendingMessage */

            $this->getGearmanDispatcher()->execute(
                TextMessageQueueSendJob::JOB_NAME, $pendingMessage->getId(),
                null, TextMessageQueueSendJob::JOB_NAME
            );
        }

        $output->write('[ <fg=green>DONE</fg=green> ]', true);
    }

    /**
     * @return Dispatcher
     */
    public function getGearmanDispatcher()
    {
        return $this->gearmanDispatcher;
    }

    /**
     * @param Dispatcher $gearmanDispatcher
     *
     * @return $this
     */
    public function setGearmanDispatcher(Dispatcher $gearmanDispatcher)
    {
        $this->gearmanDispatcher = $gearmanDispatcher;

        return $this;
    }

    /**
     * @return EntityManager
     */
    public function getEntityManager()
    {
        return $this->entityManager;
    }

    /**
     * @param EntityManager $entityManager
     *
     * @return $this
     */
    public function setEntityManager(EntityManager $entityManager)
    {
        $this->entityManager = $entityManager;

        return $this;
    }
}
