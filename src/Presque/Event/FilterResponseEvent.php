<?php

namespace Presque\Event;

use Symfony\Component\HttpFoundation\Response;

class FilterResponseEvent extends AbstractJobEvent
{
    private $response;

    public function __construct($job, Response $response)
    {
        $this->response = $response;

        parent::__construct($job);
    }

    public function getResponse()
    {
        return $this->response;
    }

    public function setResponse(Response $response)
    {
        $this->response = $response;
    }
}
