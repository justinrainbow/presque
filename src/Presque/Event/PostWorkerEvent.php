<?php

namespace Presque\Event;

use Symfony\Component\HttpFoundation\Response;

class PostWorkerEvent extends AbstractJobEvent
{
    private $response;
    private $worker;

    public function __construct($response, $job, $worker)
    {
        $this->response = $response;
        $this->worker   = $worker;

        parent::__construct($job);
    }

    public function getResponse()
    {
        return $this->response;
    }

    public function setResponse(Response $response)
    {
        $this->response = $response;

        $this->stopPropagation();
    }

    public function hasResponse()
    {
        return null !== $this->response;
    }

    public function getWorker()
    {
        return $this->worker;
    }
}
