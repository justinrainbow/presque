<?php

/*
 * This file is part of the Presque package.
 *
 * (c) Justin Rainbow <justin.rainbow@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Presque\Event;

use Symfony\Component\EventDispatcher\Event as SymfonyEvent;

class Event extends SymfonyEvent
{
    protected $defaultPrevented = false;

    public function halt()
    {
        $this->stopEvent();
    }

    public function stopEvent()
    {
        $this->stopPropagation();
        $this->preventDefault();
    }

    public function preventDefault()
    {
        $this->defaultPrevented = true;
    }

    public function isCanceled()
    {
        return $this->defaultPrevented === true;
    }
}