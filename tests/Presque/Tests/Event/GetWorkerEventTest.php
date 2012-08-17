<?php

namespace Presque\Tests\Event;

use Presque\Event\GetWorkerEvent;

class GetWorkerEventTest extends \PHPUnit_Framework_TestCase
{
    public function testExceptionThrownWithInvalidWorker()
    {
        $this->setExpectedException('Presque\Exception\InvalidWorkerException');

        $event = new GetWorkerEvent();
        $event->setWorker(null);
    }

    /**
     * @dataProvider getValidWorkers
     */
    public function testValidWorkers($worker)
    {
        $event = new GetWorkerEvent();
        $event->setWorker($worker);

        $this->assertTrue($event->hasWorker());
        $this->assertTrue(is_callable($event->getWorker()));
        $this->assertTrue($event->isPropagationStopped());
    }

    public function testModifyingJobDescription()
    {

    }

    public function getValidWorkers()
    {
        return array(
            array(function(){}),
            array(array(new \Presque\Tests\Mock\NoopWorker(), 'execute')),
            array(array('Presque\Tests\Mock\NoopWorker', 'staticMethodsWork'))
        );
    }
}