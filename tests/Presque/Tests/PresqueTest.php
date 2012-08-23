<?php

namespace Presque\Tests;

use Presque\Presque;
use Presque\Events;
use Presque\Event\GetWorkerEvent;
use Presque\Job\Description;
use Symfony\Component\EventDispatcher\EventDispatcher;
use Mockery as m;

class PresqueTest extends \PHPUnit_Framework_TestCase
{
    public function testHandlingJobDescription()
    {
        $dispatcher = $this->mockEventDispatcher();

        $presque = new Presque($dispatcher);

        $callback = m::on(function ($event) {
            if (!$event instanceof GetWorkerEvent) {
                throw new \InvalidArgumentException('Expected a Presque\Event\GetWorkerEvent but got '.get_class($event));
            }

            return true;
        });

        $dispatcher
            ->shouldReceive('dispatch')->with(Events::REQUEST, $callback)->once()->ordered();

        $presque->handle(new Description());
    }

    public function testUnhandledWorkRequest()
    {
        $this->setExpectedException('Presque\Exception\WorkerNotFoundException');

        $presque = new Presque(new EventDispatcher());
        $presque->handle(new Description(), false);
    }

    public function testWorkerFailsToCreateResponse()
    {
        $this->setExpectedException('LogicException', 'The worker must return a Response object (null given)');

        $dispatcher = new EventDispatcher();
        $dispatcher->addListener(Events::REQUEST, function ($event) {
            $event->setWorker(function () {});
        });
        $presque = new Presque($dispatcher);
        $presque->handle(new Description(), false);
    }

    protected function mockEventDispatcher()
    {
        return m::mock('Symfony\Component\EventDispatcher\EventDispatcherInterface');
    }

    protected function tearDown()
    {
        m::close();
    }
}
