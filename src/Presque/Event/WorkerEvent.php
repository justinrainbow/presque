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

use Presque\WorkerInterface;

class WorkerEvent extends Event
{
    private $worker;
    private $canceled;

    public function __construct(WorkerInterface $worker)
    {
        $this->worker = $worker;
        $this->canceled = false;
    }

    public function getWorker()
    {
        return $this->worker;
    }

    public function cancel()
    {
        $this->canceled = true;
    }

    public function isCanceled()
    {
        return $this->canceled;
    }
}