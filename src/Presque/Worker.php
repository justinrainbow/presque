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
use Presque\Log\LoggerAwareInterface;
use Presque\Log\LoggerInterface;

class Worker implements WorkerInterface, LoggerAwareInterface
{
    private $id;
    private $queues;
    private $status;
    private $eventDispatcher;

    public function __construct($id = null)
    {
        if (null === $id) {
            $id = gethostname() . ':' . getmypid();
        }

        $this->id = $id;
        $this->queues = new \SplObjectStorage();
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
        $this->queues->attach($queue);
    }

    /**
     * {@inheritDoc}
     */
    public function removeQueue(QueueInterface $queue)
    {
        if ($this->queues->contains($queue)) {
            $this->queues->detach($queue);
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
    public function setLogger(LoggerInterface $logger = null)
    {
        $this->logger = $logger;
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

        $this->setStatus(StatusInterface::STOPPED);
    }

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
            foreach ($this->getQueues() as $queue) {
                $this->process($queue);

                if ($this->isDying() || !$this->isRunning()) {
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