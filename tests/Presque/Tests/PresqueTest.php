<?php

namespace Presque\Tests;

use Presque\Presque;
use Presque\Events;
use Presque\Event\GetWorkerEvent;
use Presque\Job\Description;
use Mockery as m;

class PresqueTest extends \PHPUnit_Framework_TestCase
{
    public function testHandlingJobDescription()
    {
        $dispatcher = $this->mockEventDispatcher();

        $presque = new Presque($dispatcher);

        $workEventValidator = m::on(function ($event) {
            if (!$event instanceof GetWorkerEvent) {
                throw new \InvalidArgumentException('Expected a Presque\Event\GetWorkerEvent but got '.get_class($event));
            }

            return true;
        });

        $dispatcher
            ->shouldReceive('dispatch')->with(Events::WORK, $workEventValidator)->once()->ordered();

        $presque->handle(new Description());
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