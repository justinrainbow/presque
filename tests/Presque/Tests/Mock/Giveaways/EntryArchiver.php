<?php

namespace Presque\Tests\Mock\Giveaways;

use Presque\Events;
use Presque\Event\GetWorkerEvent;
use Presque\Event\PostWorkerEvent;
use Presque\Job\DescriptionInterface;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

class EntryArchiver implements EventSubscriberInterface
{
    public static function getSubscribedEvents()
    {
        return array(
            Events::WORK   => 'onWorkEvent',
            Events::RESULT => 'verifyComplete'
        );
    }

    public function onWorkEvent(GetWorkerEvent $event)
    {
    }

    public function verifyComplete(PostWorkerEvent $event)
    {
    }
}