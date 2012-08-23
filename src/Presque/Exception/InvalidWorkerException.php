<?php

namespace Presque\Exception;

class InvalidWorkerException extends \InvalidArgumentException
{
    private $job;

    public function __construct($msg = null, $job = null)
    {
        $this->job = $job;

        parent::__construct($msg);
    }
}
