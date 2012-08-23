<?php

namespace Presque\Tests\Functional;

use Presque\Events;
use Presque\Presque;
use Presque\Event\GetWorkerEvent;
use Presque\Job\Description;
use Symfony\Component\EventDispatcher\EventDispatcher;
use Presque\Tests\Mock\Giveaways\Giveaway;
use Presque\Tests\Mock\Giveaways\EntryArchiver;
use Mockery as m;

class GiveawayProcessingTest extends \PHPUnit_Framework_TestCase
{
    public function testHandlingAJobDescription()
    {
        $archiver = new EntryArchiver();

        $dispatcher = new EventDispatcher();
        $dispatcher->addSubscriber($archiver);

        $giveaway = new Giveaway(array(
            'entries' => array(1, 3, 44, 5)
        ));

        $presque = new Presque($dispatcher);
        $result = $presque->handle($giveaway, false);

        $this->assertInstanceOf('Symfony\Component\HttpFoundation\Response', $result);
        $this->assertTrue($result->isSuccessful());
    }
}
