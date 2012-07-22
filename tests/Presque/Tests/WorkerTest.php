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

use Presque\Worker;
use Presque\Events;
use Symfony\Component\EventDispatcher\EventDispatcher;
use Mockery as m;

class WorkerTest extends TestCase
{
    public function testWorkerRunningWithEventDispatcher()
    {
        $eventDispatcher = $this->createEventDispatcher();
        $queue           = $this->createQueueMock();
        $job             = $this->createJobMock();

        $worker = new Worker();
        $worker->addQueue($queue);
        $worker->setEventDispatcher($eventDispatcher);

        $this->assertEquals(gethostname() . ':' . getmypid(), $worker->getId());

        $test = $this;

        $eventDispatcher->addListener(Events::WORK_STARTED, function ($event) use (&$test) {
            $test->assertTrue($event->getWorker() instanceof \Presque\WorkerInterface);
        });

        $eventDispatcher->addListener(Events::JOB_STARTED, function ($event) use (&$test) {
            $event->getWorker()->stop();
        });

        $job
            ->shouldReceive('perform')
            ->shouldReceive('isSuccessful')
            ->shouldReceive('isError');

        $queue
            ->shouldReceive('reserve')->once()->andReturn($job);

        $worker->start();

        $this->assertCount(1, $worker->getQueues());
        $worker->removeQueue($queue);
        $this->assertCount(0, $worker->getQueues());
    }

    public function testCancelingAWorkerFromStarting()
    {
        $eventDispatcher = $this->createEventDispatcher();

        $worker = new Worker();
        $worker->setEventDispatcher($eventDispatcher);

        $eventDispatcher->addListener(Events::WORK_STARTED, function ($event) {
            $event->cancel();
        });

        $worker->start();
    }

    public function testCancelingAJobFromRunning()
    {
        $eventDispatcher = $this->createEventDispatcher();
        $queue           = $this->createQueueMock();
        $job             = $this->createJobMock();

        $worker = new Worker();
        $worker->addQueue($queue);
        $worker->setEventDispatcher($eventDispatcher);

        $eventDispatcher->addListener(Events::JOB_STARTED, function ($event) {
            $event->cancel();

            // need to stop the worker... or the test will just keep going
            $event->getWorker()->stop();
        });

        $queue
            ->shouldReceive('reserve')->once()->andReturn($job);

        $worker->start();
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