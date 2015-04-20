<?php

namespace Smart\TextMessageQueue;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Sinergi\Gearman\Dispatcher;

class TextMessageQueueSendCommand extends Command
{
    const COMMAND_NAME = 'textmessagequeue:send';

    /**
     * @var Dispatcher
     */
    private $gearmanDispatcher;

    public function __construct(Dispatcher $gearmanDispatcher)
    {
        $this->gearmanDispatcher = $gearmanDispatcher;
        parent::__construct();
    }

    protected function configure()
    {
        $this->setName(self::COMMAND_NAME)
            ->setDescription('This sends the text message queue');
    }

    /**
     * @param InputInterface  $input
     * @param OutputInterface $output
     *
     * @return void
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $output->write('Sending text message queue to gearman: ');
        $this->getGearmanDispatcher()
            ->execute(TextMessageQueueSendJob::JOB_NAME, null, null,
                TextMessageQueueSendJob::JOB_NAME);
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
}
