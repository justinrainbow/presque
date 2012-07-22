<?php

/*
 * This file is part of the Presque package.
 *
 * (c) Justin Rainbow <justin.rainbow@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Presque\Tests;

use Symfony\Component\EventDispatcher\EventDispatcher;
use Mockery as m;

abstract class TestCase extends \PHPUnit_Framework_TestCase
{
    protected function createLoggerMock()
    {
        return m::mock('Presque\Log\LoggerInterface');
    }

    protected function createEventDispatcher()
    {
        return new EventDispatcher();
    }

    protected function createQueueMock()
    {
        return m::mock('Presque\QueueInterface');
    }

    protected function createJobMock()
    {
        return m::mock('Presque\Job');
    }
}