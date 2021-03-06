<?php

/*
 * This file is part of the Presque package.
 *
 * (c) Justin Rainbow <justin.rainbow@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Presque\Job;

use Presque\StatusInterface;

abstract class AbstractJob implements JobInterface
{
    protected $status;

    public function setStatus($status)
    {
        $this->status = $status;
    }

    public function getStatus()
    {
        return $this->status;
    }

    public function prepare()
    {
        $this->lastResult = $this->lastError = null;

        $this->setStatus(StatusInterface::RUNNING);
    }

    public function complete()
    {
    }

    /**
     * {@inheritDoc}
     */
    public function isSuccessful()
    {
        return StatusInterface::SUCCESS === $this->getStatus();
    }

    /**
     * {@inheritDoc}
     */
    public function isError()
    {
        return !$this->isActive() && !$this->isSuccessful();
    }

    /**
     * {@inheritDoc}
     */
    public function isActive()
    {
        return StatusInterface::RUNNING === $this->getStatus();
    }
}