<?php

/*
 * This file is part of the Presque package.
 *
 * (c) Justin Rainbow <justin.rainbow@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Presque\Log;

interface LoggerAwareInterface
{
    /**
     * Sets an instance of the LoggerInterface for this object. If the `$logger`
     * is null, the object must remove any current LoggerInterface assigned to it.
     *
     * @param LoggerInterface $logger
     */
    function setLogger(LoggerInterface $logger = null);

    /**
     * Checks if a LoggerInterface is available.
     *
     * @return Boolean
     */
    function hasLogger();
}