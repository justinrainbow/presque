<?php

namespace Presque\Tests\Event;

use Presque\Event\PostWorkerEvent;
use Presque\Job\Description;

class PostWorkerEventTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @expectedException PHPUnit_Framework_Error
     * @expectedExceptionMessage Missing argument 1 for Presque\Event\PostWorkerEvent::__construct()
     */
    public function testFailedInstantiatingPostWorkerEvent()
    {
        $this->assertInstanceOf('Presque\Event\PostWorkerEvent', new PostWorkerEvent());
    }

    public function testInstantiatingPostWorkerEvent()
    {
        $result = null;
        $worker = function(){};
        $job    = new Description();

        $event = new PostWorkerEvent($result, $job, $worker);

        $this->assertInstanceOf('Presque\Event\PostWorkerEvent', $event);
        $this->assertInstanceOf('Presque\Job\DescriptionInterface', $event->getJob());
        $this->assertEquals(null, $event->getResult());
        $this->assertTrue(is_callable($event->getWorker()));
    }
}