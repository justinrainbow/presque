<?php

/*
 * This file is part of the Presque package.
 *
 * (c) Justin Rainbow <justin.rainbow@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Presque\Worker;

use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Presque\Event\JobEvent;
use Presque\Event\WorkerEvent;
use Presque\Log\LoggerAwareInterface;
use Presque\Log\LoggerInterface;
use Presque\Queue\QueueInterface;
use Presque\Job\JobInterface;
use Presque\StatusInterface;
use Presque\Events;

class Worker extends AbstractWorker
{
    /**
     * {@inheritDoc}
     */
    public function isRunning()
    {
        return $this->getStatus() === StatusInterface::RUNNING;
    }

    /**
     * {@inheritDoc}
     */
    public function isDying()
    {
        return $this->getStatus() === StatusInterface::DYING || $this->getStatus() === StatusInterface::STOPPING;
    }

    /**
     * {@inheritDoc}
     */
    public function start()
    {
        if ($this->hasEventDispatcher()) {
            $event = $this->eventDispatcher->dispatch(Events::WORK_STARTED, new WorkerEvent($this));

            if ($event->isCanceled()) {
                return;
            }
        }

        $this->setStatus(StatusInterface::RUNNING);

        $this->run();

        if ($this->hasEventDispatcher()) {
            $this->eventDispatcher->dispatch(Events::WORK_STOPPED, new WorkerEvent($this));
        }

        $this->setStatus(StatusInterface::STOPPED);
    }

    /**
     * Starts the shutdown process for this worker
     */
    public function stop()
    {
        $this->setStatus(StatusInterface::STOPPING);
    }

    /**
     * {@inheritDoc}
     */
    public function run()
    {
        while ($this->isRunning()) {
            $this->runLoop();
        }
    }

    /**
     * Runs a single pass through all the registered queues.
     *
     * Each QueueInterface is processed in the order it was added.
     */
    public function runLoop()
    {
        foreach ($this->getQueues() as $queue) {
            $this->process($queue);

            if ($this->isDying() || !$this->isRunning()) {
                break;
            }
        }
    }

    /**
     * Checks and executes a new Job for the given `$queue`
     *
     * @param QueueInterface $queue
     */
    protected function process(QueueInterface $queue)
    {
        $job = $queue->reserve();

        if (!$job instanceof JobInterface) {
            return;
        }

        if ($this->hasLogger()) {
            $this->logger->log(
                sprintf('Starting job "%s"', $job),
                LOG_DEBUG
            );
        }

        $job->prepare();

        if ($this->hasEventDispatcher()) {
            $event = $this->eventDispatcher->dispatch(Events::JOB_STARTED, new JobEvent($job, $queue, $this));

            if ($event->isCanceled()) {
                return;
            }

            $job = $event->getJob();
        }

        $job->perform();

        if ($this->hasEventDispatcher()) {
            $this->eventDispatcher->dispatch(Events::JOB_FINISHED, new JobEvent($job, $queue, $this));
        }

        $job->complete();
    }
}