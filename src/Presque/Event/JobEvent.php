<?php

/*
 * This file is part of the Presque package.
 *
 * (c) Justin Rainbow <justin.rainbow@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Presque\Event;

use Presque\Job\JobInterface;
use Presque\Queue\QueueInterface;
use Presque\Worker\WorkerInterface;

class JobEvent extends Event
{
    private $worker;

    public function __construct(JobInterface $job, QueueInterface $queue, WorkerInterface $worker)
    {
        $this->job    = $job;
        $this->queue  = $queue;
        $this->worker = $worker;
    }

    public function getJob()
    {
        return $this->job;
    }

    public function setJob(JobInterface $job)
    {
        $this->job = $job;

        return $this;
    }

    public function getQueue()
    {
        return $this->queue;
    }

    public function getWorker()
    {
        return $this->worker;
    }
}