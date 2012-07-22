<?php

/*
 * This file is part of the Presque package.
 *
 * (c) Justin Rainbow <justin.rainbow@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Presque;

use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Presque\Event\JobEvent;
use Presque\Event\WorkerEvent;

class Worker implements WorkerInterface
{
    private $id;
    private $queues;
    private $status;
    private $eventDispatcher;

    public function __construct($id = null)
    {
        $this->id = $id;
    }

    /**
     * {@inheritDoc}
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * {@inheritDoc}
     */
    public function addQueue(QueueInterface $queue)
    {
        $this->queues[] = $queue;
    }

    /**
     * {@inheritDoc}
     */
    public function removeQueue(QueueInterface $queue)
    {
        if ($key = array_search($queue, $this->queues)) {
            unset($this->queues[$queue]);
        }
    }

    /**
     * {@inheritDoc}
     */
    public function getQueues()
    {
        return $this->queues;
    }

    /**
     * {@inheritDoc}
     */
    public function setEventDispatcher(EventDispatcherInterface $eventDispatcher = null)
    {
        $this->eventDispatcher = $eventDispatcher;
    }

    /**
     * {@inheritDoc}
     */
    public function hasEventDispatcher()
    {
        return null !== $this->eventDispatcher;
    }

    /**
     * {@inheritDoc}
     */
    public function setStatus($status)
    {
        $this->status = $status;
    }

    /**
     * {@inheritDoc}
     */
    public function getStatus()
    {
        return $this->status;
    }

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
        return $this->getStatus() === StatusInterface::DYING;
    }

    /**
     * {@inheritDoc}
     */
    public function start()
    {
        if ($this->hasEventDispatcher()) {
            $event = $this->eventDispatcher(Events::WORK_STARTED, new WorkerEvent($this));

            if ($event->isCanceled()) {
                return;
            }
        }

        $this->setStatus(StatusInterface::RUNNING);

        $this->run();
    }

    /**
     * {@inheritDoc}
     */
    public function run()
    {
        while ($this->isRunning()) {
            foreach ($this->getQueues() as $queue) {
                $this->process($queue);

                if (!$this->isRunning() || $this->isDying()) {
                    return;
                }
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

        if ($this->hasEventDispatcher()) {
            $event = $this->eventDispatcher->dispatch(Events::JOB_STARTED, new JobEvent($job, $queue, $this));

            if ($event->isCanceled()) {
                return;
            }

            $job = $event->getJob();
        }

        $job->perform();

        if ($job->isSuccessful()) {
            return;
        }

        if ($job->isError()) {

        }
    }
}