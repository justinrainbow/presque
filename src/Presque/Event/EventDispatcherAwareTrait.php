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

use Symfony\Component\EventDispatcher\EventDispatcherInterface;

/**
 * Provides methods to use with the Symfony EventDispatcher component.
 *
 * @author Justin Rainbow <justin.rainbow@gmail.com>
 */
trait EventDispatcherAwareTrait
{
    protected $dispatcher;

    /**
     * Sets an instance of the EventDispatcherInterface for this object.
     * If the `$dispatcher` is null, the object must remove any
     * current EventDispatcherInterface assigned to it.
     *
     * @param EventDispatcherInterface $dispatcher
     */
    public function setEventDispatcher(EventDispatcherInterface $dispatcher = null)
    {
        $this->dispatcher = $dispatcher;
    }

    /**
     * Checks if there is an EventDispatcher available to the current object.
     *
     * @return Boolean
     */
    public function hasEventDispatcher()
    {
        return null !== $this->dispatcher;
    }
}