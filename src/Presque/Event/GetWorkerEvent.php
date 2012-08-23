<?php

namespace Presque\Event;

use Presque\Exception\InvalidWorkerException;
use Symfony\Component\EventDispatcher\Event as AbstractEvent;
use Presque\Util;

class GetWorkerEvent extends AbstractJobEvent
{
    private $worker;

    public function setWorker($callable)
    {
        if (!is_callable($callable)) {
            throw new \InvalidArgumentException(sprintf(
                'Invalid callable provided (got %s)',
                Util::dump($callable)
            ));
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
