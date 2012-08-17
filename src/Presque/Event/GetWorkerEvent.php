<?php

namespace Presque\Event;

use Presque\Exception\InvalidWorkerException;
use Symfony\Component\EventDispatcher\Event as AbstractEvent;

class GetWorkerEvent extends AbstractJobEvent
{
    private $worker;

    public function setWorker($callable)
    {
        if (!is_callable($callable)) {
            throw new InvalidWorkerException();
        }

        $this->worker = $callable;

        $this->stopPropagation();
    }

    public function hasWorker()
    {
        return null !== $this->worker;
    }

    public function getWorker()
    {
        return $this->worker;
    }
}