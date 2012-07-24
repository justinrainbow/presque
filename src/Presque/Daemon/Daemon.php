<?php

/*
 * This file is part of the Presque package.
 *
 * (c) Justin Rainbow <justin.rainbow@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Presque\Daemon;

class Daemon extends AbstractDaemon
{
    protected function doLoop()
    {
        $this->logInfo("Starting work");

        $this->worker->run();

        $this->logInfo("Ending work");
    }
}