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

interface WorkerInterface extends EventDispatcherAwareInterface
{
    function start();

    function pause();

    function stop();

    function abort();
}