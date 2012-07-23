<?php

/*
 * This file is part of the Presque package.
 *
 * (c) Justin Rainbow <justin.rainbow@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Presque\EventListener;

use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Presque\Events;
use Presque\Event\JobEvent;

class JobRetryListener implements EventSubscriberInterface
{
    public static function getSubscribedEvents()
    {
        return array(
            Events::JOB_FINISHED => 'onJobFinished'
        );
    }

    public function onJobFinished(JobEvent $event)
    {
        $job = $event->getJob();

        if ($job->isError()) {
            $event->getQueue()->enqueue($job);
        }
    }
}