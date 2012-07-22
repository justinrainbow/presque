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

use Presque\StatusInterface;

abstract class AbstractJob implements JobInterface
{
    public function isSuccessful()
    {
        return StatusInterface::SUCCESS === $this->getStatus();
    }

    public function isError()
    {
        return StatusInterface::FAILED === $this->getStatus();
    }

    public function isActive()
    {
        return StatusInterface::RUNNING === $this->getStatus();
    }

    public function isTrackable()
    {

    }

    public function getMaxAttempts()
    {

    }
}