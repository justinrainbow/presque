<?php

namespace Presque\Tests\Mock\Giveaways;

use Presque\Events;
use Presque\Event\GetWorkerEvent;
use Presque\Event\PostWorkerEvent;
use Presque\Job\DescriptionInterface;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpFoundation\Response;

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
        $event->setWorker(array(&$this, 'work'));
    }

    public function verifyComplete(PostWorkerEvent $event)
    {
        $event->setResponse(new Response('OK', 200));
    }

    public function work(DescriptionInterface $data)
    {

    }
}
