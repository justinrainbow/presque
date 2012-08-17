<?php

namespace Presque\Tests\Functional;

use Presque\Events;
use Presque\Presque;
use Presque\Event\GetWorkerEvent;
use Presque\Job\Description;
use Symfony\Component\EventDispatcher\EventDispatcher;

class PresqueTest extends \PHPUnit_Framework_TestCase
{
    public function testHandlingAJobDescription()
    {
        $test = $this;
        $worker = function () {

        };

        $listener = function ($event) use ($worker) {
            $job = $event->getJob();

            if (isset($job['apples'])) {
                $event->setWorker($worker);
            }
        };

        $dispatcher = new EventDispatcher();
        $dispatcher->addListener(Events::WORK, $listener);
        $dispatcher->addListener(Events::WORK, function ($event) use ($test) {
            $message = "Second event listener should never have been called!";
            if (!$event->isPropagationStopped()) {
                $message .= " `\$event->stopPropagation()` was never called!";
            }
            $test->fail($message);
        });

        $description = new Description(array(
            'apples' => array('red', 'green')
        ));

        $presque = new Presque($dispatcher);
        $presque->handle($description, false);
    }
}