<?php

namespace Presque\Tests\Event;

use Presque\Event\FilterResponseEvent;
use Presque\Job\Description;
use Symfony\Component\HttpFoundation\Response;

class FilterResponseEventTest extends \PHPUnit_Framework_TestCase
{
    public function testSettingResponseStopsEvent()
    {
        $event = new FilterResponseEvent(new Description(), new Response('FAIL'));

        $this->assertInstanceOf('Symfony\Component\HttpFoundation\Response', $event->getResponse());
        $this->assertEquals('FAIL', $event->getResponse()->getContent());

        $event->setResponse(new Response('OK'));

        $this->assertInstanceOf('Symfony\Component\HttpFoundation\Response', $event->getResponse());
        $this->assertEquals('OK', $event->getResponse()->getContent());
        $this->assertFalse($event->isPropagationStopped(), 'Event should not be stopped after a Response is set');
    }
}
