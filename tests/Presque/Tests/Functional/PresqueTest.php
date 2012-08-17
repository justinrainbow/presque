<?php

namespace Presque\Tests\Functional;

use Presque\Events;
use Presque\Presque;
use Presque\Event\GetWorkerEvent;
use Presque\Job\Description;
use Symfony\Component\EventDispatcher\EventDispatcher;
use Mockery as m;

class PresqueTest extends \PHPUnit_Framework_TestCase
{
    public function testHandlingAJobDescription()
    {
        $test = $this;
        $worker = function () {

        };

        $workEventListeners = array(
            function (GetWorkerEvent $event) use ($worker) {
                $job = $event->getJob();

                if (isset($job['apples'])) {
                    $event->setWorker($worker);
                }
            },
            function (GetWorkerEvent $event) use ($test) {
                $message = "Second event listener should never have been called!";
                if (!$event->isPropagationStopped()) {
                    $message .= " `\$event->stopPropagation()` was never called!";
                }
                $test->fail($message);
            }
        );

        $finishWork = m::mock('stdClass');
        $finishWork
            ->shouldReceive('finishWork')->once();

        $postWorkEventListeners = array(
            array(&$finishWork, 'finishWork')
        );

        $dispatcher = $this->createEventDispatcher(array(
            Events::WORK   => $workEventListeners,
            Events::RESULT => $postWorkEventListeners
        ));

        $description = new Description(array(
            'apples' => array('red', 'green')
        ));

        $presque = new Presque($dispatcher);
        $presque->handle($description, false);
    }

    protected function createEventDispatcher(array $listeners)
    {
        $dispatcher = new EventDispatcher();
        foreach ($listeners as $eventName => $listener) {
            foreach ($listener as $callback) {
                $dispatcher->addListener($eventName, $callback);
            }
        }

        return $dispatcher;
    }
}