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
use Presque\Event\EventDispatcherAwareInterface;
use Presque\Log\LoggerAwareInterface;
use Presque\Log\LoggerInterface;
use Presque\Queue\QueueInterface;
use Presque\Job\JobInterface;
use Presque\StatusInterface;
use Presque\Events;

abstract class AbstractWorker implements WorkerInterface, LoggerAwareInterface, EventDispatcherAwareInterface
{
    protected $id;
    protected $status;
    protected $queues;
    protected $dispatcher;
    protected $logger;

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
    public function setEventDispatcher(EventDispatcherInterface $dispatcher = null)
    {
        $this->dispatcher = $dispatcher;
    }

    /**
     * {@inheritDoc}
     */
    public function hasEventDispatcher()
    {
        return null !== $this->dispatcher;
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
    public function hasLogger()
    {
        return null !== $this->logger;
    }

    /**
     * Wrapper for dispatching events.  The provided `$event` will be returned
     * immediately if there is no EventDispatcher available.
     *
     * If there is an EventDispatcher, the `dispatch` event will be called, and
     * the `$event` will still be returned.
     *
     * @param string $name  Event name to dispatch
     * @param mixed  $event Event payload
     *
     * @return mixed $event
     */
    protected function dispatchEvent($name, $event)
    {
        if (!$this->hasEventDispatcher()) {
            return $event;
        }

        return $this->dispatcher->dispatch($name, $event);
    }
}