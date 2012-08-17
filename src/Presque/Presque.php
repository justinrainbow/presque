<?php

namespace Presque;

use Presque\Event\GetWorkerEvent;
use Presque\Job\DescriptionInterface;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;

class Presque
{
    private $dispatcher;

    public function __construct(EventDispatcherInterface $eventDispatcher)
    {
        $this->dispatcher = $eventDispatcher;
    }

    public function handle(DescriptionInterface $job, $catchException = true)
    {
        try {
            $this->handleRaw($job);
        } catch (\Exception $e) {
            if (true !== $catchException) {
                throw $e;
            }

            $this->handleException($e, $job);
        }
    }

    private function handleRaw(DescriptionInterface $job)
    {
        $event = new GetWorkerEvent($job);
        $this->dispatcher->dispatch(Events::WORK, $event);

        if ($event->hasWorker()) {
            $worker = $event->getWorker();
            $result = call_user_func($worker, $job);
        }
    }

    private function handleException(\Exception $exception, DescriptionInterface $job)
    {
    }
}