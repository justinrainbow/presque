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

use Presque\Tests\TestCase;
use Presque\Worker\Worker;
use Presque\Events;
use Presque\Job\Job;
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
            $test->assertTrue($event->getWorker() instanceof \Presque\Worker\WorkerInterface);
        });

        $eventDispatcher->addListener(Events::JOB_STARTED, function ($event) use (&$test) {
            $event->getWorker()->stop();
        });

        $job
            ->shouldReceive('prepare')->ordered('lifecycle')
            ->shouldReceive('perform')->ordered('lifecycle')
            ->shouldReceive('complete')->ordered('lifecycle')
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
        $logger          = $this->createLoggerMock();

        $worker = new Worker();
        $worker->setEventDispatcher($eventDispatcher);
        $worker->setLogger($logger);

        $eventDispatcher->addListener(Events::WORK_STARTED, function ($event) {
            $event->halt();
        });

        $worker->start();
    }

    public function testCancelingAJobFromRunning()
    {
        $eventDispatcher = $this->createEventDispatcher();
        $queue           = $this->createQueueMock();
        $job             = Job::create('Presque\Tests\Jobs\SimpleJob', array('simple', 'job'));

        $worker = new Worker();
        $worker->addQueue($queue);
        $worker->setEventDispatcher($eventDispatcher);

        $test = $this;

        $eventDispatcher->addListener(Events::JOB_STARTED, function ($event) use ($test) {
            $event->halt();

            $test->assertTrue($event->getJob()->isActive());

            // need to stop the worker... or the test will just keep going
            $event->getWorker()->stop();
        });

        $queue
            ->shouldReceive('reserve')->once()->andReturn($job);

        $worker->start();
    }

    public function testReplacingAJobInEvent()
    {
        $eventDispatcher = $this->createEventDispatcher();
        $queue           = $this->createQueueMock();
        $job             = Job::create('Presque\Tests\Jobs\SimpleJob', array('simple', 'job'));
        $secondJob       = Job::create('Presque\Tests\Jobs\SimpleJob', array('failed', 'job'));

        $worker = new Worker();
        $worker->addQueue($queue);
        $worker->setEventDispatcher($eventDispatcher);

        $test = $this;

        $eventDispatcher->addListener(Events::JOB_STARTED, function ($event) use (&$secondJob) {
            $event->setJob($secondJob);
        });

        $eventDispatcher->addListener(Events::JOB_FINISHED, function ($event) use ($test) {
            $test->assertInstanceOf('Presque\Queue\QueueInterface', $event->getQueue());
            $test->assertTrue($event->getJob()->isError());
        });

        $queue
            ->shouldReceive('reserve')->once()->andReturn($job);

        $worker->runLoop();
    }

    public function testDoingNothingInALoop()
    {
        $queue = $this->createQueueMock();

        $worker = new Worker();
        $worker->addQueue($queue);

        $queue
            ->shouldReceive('reserve')->once()->andReturn(null);

        $worker->runLoop();
    }

    public function testLoggingJobActivity()
    {
        $queue  = $this->createQueueMock();
        $job    = Job::create('Presque\Tests\Jobs\SimpleJob', array('simple', 'job'));
        $logger = $this->createLoggerMock();

        $worker = new Worker();
        $worker->addQueue($queue);
        $worker->setLogger($logger);

        $logger
            ->shouldReceive('debug')->once()->with('Starting job "Presque\Tests\Jobs\SimpleJob"');

        $queue
            ->shouldReceive('reserve')->once()->andReturn($job);

        $worker->runLoop();
    }
}