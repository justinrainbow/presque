<?php

namespace Presque\Tests\Mock\Giveaways;

use Presque\Job\Description;
use Presque\Job\RetryableInterface;

class Giveaway extends Description implements RetryableInterface
{
    /**
     * {@inheritDoc}
     */
    public function getMaximumAttempts()
    {
        return 2;
    }
}