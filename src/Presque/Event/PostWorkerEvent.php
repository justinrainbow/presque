<?php

namespace Presque\Event;

class PostWorkerEvent extends AbstractJobEvent
{
    private $result;
    private $worker;

    public function __construct($result, $job, $worker)
    {
        $this->result = $result;
        $this->worker = $worker;

        parent::__construct($job);
    }

    public function getResult()
    {
        return $this->result;
    }

    public function getWorker()
    {
        return $this->worker;
    }
}