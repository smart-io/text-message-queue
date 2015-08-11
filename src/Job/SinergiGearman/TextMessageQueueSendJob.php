<?php

namespace Smart\TextMessageQueue\Job\SinergiGearman;

use GearmanJob;
use Sinergi\Gearman\JobInterface;
use Smart\TextMessageQueue\Job\TextMessageQueueSendJob as BaseJob;

class TextMessageQueueSendJob extends BaseJob implements JobInterface
{
    /**
     * @param GearmanJob|null $job
     * @return void
     */
    public function execute(GearmanJob $job = null)
    {

        if(!($job instanceof GearmanJob)){
            return;
        }

        parent::execute((int)unserialize($job->workload()));
    }
}
