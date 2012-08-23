<?php

namespace Presque\Event;

class FilterResponseEvent extends AbstractJobEvent
{
    private $response;

    public function __construct($job, $response)
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

        $this->stopPropagation();
    }
}
