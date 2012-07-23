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

    protected function createEventDispatcherAwareMock()
    {
        return m::mock('Presque\Event\EventDispatcherAwareInterface');
    }

    protected function createLoggerAwareMock()
    {
        return m::mock('Presque\Log\LoggerAwareInterface');
    }

    protected function createWorkerMock()
    {
        return m::mock('Presque\Worker\WorkerInterface');
    }

    protected function createQueueMock()
    {
        return m::mock('Presque\Queue\QueueInterface');
    }

    protected function createJobMock()
    {
        return m::mock('Presque\Job\Job');
    }

    protected function createWorkerFactoryMock()
    {
        return m::mock('Presque\Worker\WorkerFactoryInterface');
    }

    protected function createQueueFactoryMock()
    {
        return m::mock('Presque\Queue\QueueFactoryInterface');
    }

    protected function createJobFactoryMock()
    {
        return m::mock('Presque\Job\JobFactoryInterface');
    }
}