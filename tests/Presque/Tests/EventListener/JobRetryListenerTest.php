<?php

/*
 * This file is part of the Presque package.
 *
 * (c) Justin Rainbow <justin.rainbow@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Presque\Tests\EventListener;

use Presque\Tests\TestCase;
use Presque\Worker\Worker;
use Presque\Events;
use Presque\Event\JobEvent;
use Presque\EventListener\JobRetryListener;
use Mockery as m;

class JobRetryListenerTest extends TestCase
{
    public function testFailedJobIsAddedBackToQueue()
    {
        $worker = $this->createWorkerMock();

        $job = $this->createJobMock();
        $job
            ->shouldReceive('isError')->once()->andReturn(true);

        $queue = $this->createQueueMock();
        $queue
            ->shouldReceive('enqueue')->once()->with($job);

        $event = new JobEvent($job, $queue, $worker);

        $listener = new JobRetryListener();
        $listener->onJobFinished($event);
    }
}