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
use Presque\Presque;
use Mockery as m;

class PresqueTest extends TestCase
{
    public function testInjectingEventDispatcher()
    {
        $workerFactory = $this->createWorkerFactoryMock();
        $queueFactory  = $this->createQueueFactoryMock();
        $jobFactory    = $this->createJobFactoryMock();
        $dispatcher    = $this->createEventDispatcher();
        $logger        = $this->createLoggerMock();

        $presque = new Presque($workerFactory, $queueFactory, $jobFactory);
        $presque->setEventDispatcher($dispatcher);

        $worker = $this->createEventDispatcherAwareMock();
        $worker
            ->shouldReceive('setEventDispatcher')->with($dispatcher)->once();

        $workerFactory
            ->shouldReceive('create')->with(null)->once()->andReturn($worker);

        $presque->createWorker();

        $presque->setEventDispatcher(null);
        $presque->setLogger($logger);

        $worker = $this->createLoggerAwareMock();
        $worker
            ->shouldReceive('setLogger')->with($logger)->once();

        $workerFactory
            ->shouldReceive('create')->with(null)->once()->andReturn($worker);

        $presque->createWorker();

        $presque->setLogger(null);
        $job = $this->createJobMock();

        $jobFactory
            ->shouldReceive('create')->with('someClass', array())->once()->andReturn($job);

        $presque->createJob('someClass');

        $queue = $this->createQueueMock();

        $queueFactory
            ->shouldReceive('create')->with('someClass')->once()->andReturn($queue);

        $presque->createQueue('someClass');
    }

    public function testCreatingJob()
    {
        $presque = new Presque();

        $this->assertInstanceOf('Presque\Job\JobInterface', $presque->createJob(
            'Presque\Tests\Jobs\SimpleJob', array('simple', 'jobs')
        ));
    }

    public function testCreatingQueue()
    {
        $presque = new Presque();

        $this->assertInstanceOf('Presque\Queue\QueueInterface', $presque->createQueue('queue'));
    }

    public function testCreatingWorker()
    {
        $presque = new Presque();

        $this->assertInstanceOf('Presque\Worker\WorkerInterface', $presque->createWorker());
    }
}