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

use Presque\JobInterface;
use Presque\QueueInterface;
use Presque\WorkerInterface;

class JobEvent extends Event
{
    private $worker;
    private $canceled;

    public function __construct(JobInterface $job, QueueInterface $queue, WorkerInterface $worker)
    {
        $this->job      = $job;
        $this->queue    = $queue;
        $this->worker   = $worker;
        $this->canceled = false;
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

    public function cancel()
    {
        $this->canceled = true;
    }

    public function isCanceled()
    {
        return $this->canceled;
    }
}