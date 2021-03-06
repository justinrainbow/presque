<?php

/*
 * This file is part of the Presque package.
 *
 * (c) Justin Rainbow <justin.rainbow@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Presque\Queue;

use Presque\Storage\StorageInterface;
use Presque\Job\JobInterface;

interface QueueInterface
{
    function getName();

    function getStorage();

    function setStorage(StorageInterface $storage);

    function getTimeout();

    function enqueue(JobInterface $job);

    function reserve();
}