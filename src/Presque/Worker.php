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

    public function __construct($id)
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

    public function isRunning()
    {
        return $this->getStatus() === StatusInterface::RUNNING;
    }

    public function isDying()
    {
        return $this->getStatus() === StatusInterface::DYING;
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

    }
}