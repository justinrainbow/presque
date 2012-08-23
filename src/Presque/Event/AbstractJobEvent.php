<?php

namespace Presque\Event;

use Presque\Job\DescriptionInterface as JobDescriptionInterface;
use Symfony\Component\EventDispatcher\Event as AbstractEvent;

abstract class AbstractJobEvent extends AbstractEvent
{
    private $job;

    public function __construct(JobDescriptionInterface $description = null)
    {
        $this->job = $description;
    }

    public function replaceJob(JobDescriptionInterface $description)
    {
        $this->job = $description;
    }

    public function getJob()
    {
        return $this->job;
    }
}