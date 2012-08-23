<?php

namespace Presque\Job;

interface RetryableInterface
{
    /**
     * Determines the maximum number of attempts that can be
     * made to process this job.  The job will not completely
     * fail until this number has been exceeded.
     *
     * @return integer
     */
    public function getMaximumAttempts();
}