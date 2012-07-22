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

class Queue implements QueueInterface
{
    protected $name;
    protected $storage;

    public function __construct($name, StorageInterface $storage = null)
    {
        $this->name    = $name;
        $this->storage = $storage;
    }

    public function getName()
    {
        return $this->name;
    }

    public function getStorage()
    {
        return $this->storage;
    }

    public function setStorage(StorageInterface $storage)
    {
        $this->storage = $storage;
    }

    public function getWaitTime()
    {

    }

    public function enqueue(JobInterface $job)
    {
        $this->storage->push($this->name, array(
            'class' => $job->getClass(),
            'args'  => $job->getArguments(),
        ));
    }

    public function dequeue($waitFor = null)
    {

    }
}