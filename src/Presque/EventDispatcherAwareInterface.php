<?php

/*
 * This file is part of the Presque package.
 *
 * (c) Justin Rainbow <justin.rainbow@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Presque;

use Symfony\Component\EventDispatcher\EventDispatcherInterface;

/**
 * Provides methods to use with the Symfony EventDispatcher component.
 *
 * @author Justin Rainbow <justin.rainbow@gmail.com>
 */
interface EventDispatcherAwareInterface
{
    /**
     * Sets an instance of the EventDispatcherInterface for this object.
     * If the `$eventDispatcher` is null, the object must remove any
     * current EventDispatcherInterface assigned to it.
     *
     * @param EventDispatcherInterface $eventDispatcher
     */
    function setEventDispatcher(EventDispatcherInterface $eventDispatcher = null);

    /**
     * Returns an instance of the EventDispatcher
     *
     * @return EventDispatcherInterface
     */
    function getEventDispatcher();

    /**
     * Checks if there is an EventDispatcher available to the current object.
     *
     * @return Boolean
     */
    function hasEventDispatcher();
}