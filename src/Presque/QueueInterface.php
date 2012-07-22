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

use Presque\Storage\StorageInterface;

interface QueueInterface
{
    function getName();

    function getStorage();

    function setStorage(StorageInterface $storage);

    function getTimeout();

    function enqueue(JobInterface $job);

    function reserve();
}