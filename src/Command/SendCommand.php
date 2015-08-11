<?php

namespace Smart\TextMessageQueue\Command;

use Doctrine\ORM\EntityManager;
use Smart\TextMessageQueue\Dispatcher\DispatcherInterface;
use Smart\TextMessageQueue\Job\TextMessageQueueSendJob;
use Smart\TextMessageQueue\TextMessageQueueEntity;
use Smart\TextMessageQueue\TextMessageQueueRepository;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class SendCommand extends Command
{
    const COMMAND_NAME = 'textmessagequeue:send';

    /**
     * @var EntityManager
     */
    private $entityManager;

    /**
     * @var DispatcherInterface
     */
    private $dispatcher;

    /**
     * @param EntityManager $entityManager
     * @param DispatcherInterface    $dispatcher
     */
    public function __construct(
        EntityManager $entityManager,
        DispatcherInterface $dispatcher
    ) {

        $this->setEntityManager($entityManager);
        $this->setDispatcher($dispatcher);

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
            . ' text messages to worker: ');

        foreach ($pendingMessages as $pendingMessage) {

            /** @var TextMessageQueueEntity $pendingMessage */

            $this->getDispatcher()->dispatch(TextMessageQueueSendJob::JOB_NAME, $pendingMessage->getId());
        }
        $output->write('[ <fg=green>DONE</fg=green> ]', true);
    }

    /**
     * @return DispatcherInterface
     */
    public function getDispatcher()
    {
        return $this->dispatcher;
    }

    /**
     * @param DispatcherInterface $dispatcher
     *
     * @return $this
     */
    public function setDispatcher(DispatcherInterface $dispatcher)
    {
        $this->dispatcher = $dispatcher;

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
