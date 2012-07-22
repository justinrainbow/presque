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

class Worker implements WorkerInterface
{
    private $id;
    private $queues;
    private $status;

    public function __construct($id = null)
    {
        $this->id = $id;
    }

    public function getId()
    {
        return $this->id;
    }

    public function addQueue(QueueInterface $queue)
    {
        $this->queues[] = $queue;
    }

    public function removeQueue(QueueInterface $queue)
    {
        if ($key = array_search($queue, $this->queues)) {
            unset($this->queues[$queue]);
        }
    }

    public function getQueues()
    {
        return $this->queues;
    }

    public function setEventDispatcher(EventDispatcherInterface $eventDispatcher = null)
    {
        $this->eventDispatcher = $eventDispatcher;
    }

    public function getEventDispatcher()
    {
        return $this->eventDispatcher;
    }

    public function hasEventDispatcher()
    {
        return null !== $this->eventDispatcher;
    }

    public function setStatus($status)
    {
        $this->status = $status;
    }

    public function getStatus()
    {
        return $this->status;
    }

    public function isRunning()
    {
        return $this->getStatus() === StatusInterface::RUNNING;
    }

    public function isDying()
    {
        return $this->getStatus() === StatusInterface::DYING;
    }

    public function start()
    {
        $this->setStatus(StatusInterface::RUNNING);

        $this->run();
    }

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

    protected function process(QueueInterface $queue)
    {
        $job = $queue->reserve();

        if (!$job instanceof JobInterface) {
            return;
        }

        $job->perform();

        if ($job->isSuccessful()) {
            return;
        }

        if ($job->isError()) {

        }
    }
}