<?php

namespace Presque;

use Presque\Exception\InvalidWorkerException;
use Presque\Exception\WorkerNotFoundException;
use Presque\Event\FilterResponseEvent;
use Presque\Event\GetWorkerEvent;
use Presque\Event\PostWorkerEvent;
use Presque\Job\DescriptionInterface;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Symfony\Component\HttpFoundation\Response;

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
            return $this->handleRaw($job);
        } catch (\Exception $e) {
            if (true !== $catchException) {
                throw $e;
            }

            return $this->handleException($e, $job);
        }
    }

    private function handleRaw(DescriptionInterface $job)
    {
        $event = new GetWorkerEvent($job);
        $this->dispatcher->dispatch(Events::WORK, $event);

        if (!$event->hasWorker()) {
            throw new WorkerNotFoundException(
                'Unable to find a worker to process this job',
                $job
            );
        }

        $worker = $event->getWorker();
        $result = call_user_func($worker, $job);

        if ($result instanceof Response) {
            return $this->filterResponse($result, $job);
        }

        $event = new PostWorkerEvent($result, $job, $worker);
        $this->dispatcher->dispatch(Events::RESULT, $event);

        if ($event->hasResponse()) {
            $result = $event->getResponse();
        }

        if (!$result instanceof Response) {
            $msg = sprintf('The worker must return a Response object (%s given)', Util::dump($result));

            throw new \LogicException($msg);
        }

        return $this->filterResponse($result, $job);
    }

    /**
     * Filters a response object.
     *
     * @param Response $response A Response instance
     * @param DescriptionInterface $job Describes the work needed to be done
     *
     * @return Response The filtered Response instance
     *
     * @throws \RuntimeException if the passed object is not a Response instance
     */
    private function filterResponse(Response $response, DescriptionInterface $job)
    {
        $event = new FilterResponseEvent($job, $response);

        $this->dispatcher->dispatch(Events::RESPONSE, $event);

        return $event->getResponse();
    }

    private function handleException(\Exception $exception, DescriptionInterface $job)
    {
        return new Response($exception->getMessage(), 500);
    }
}
