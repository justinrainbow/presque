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
    protected $timeout;

    public function __construct($name, StorageInterface $storage = null, $timeout = 10)
    {
        $this->name    = $name;
        $this->storage = $storage;
        $this->timeout = $timeout;
    }

    public function getName()
    {
        return $this->name;
    }

    public function getTimeout()
    {
        return $this->timeout;
    }

    public function getStorage()
    {
        return $this->storage;
    }

    public function setStorage(StorageInterface $storage)
    {
        $this->storage = $storage;
    }

    public function enqueue(JobInterface $job)
    {
        $this->storage->push($this->name, array(
            'class' => $job->getClass(),
            'args'  => $job->getArguments(),
        ));
    }

    public function reserve()
    {
        $payload = $this->storage->pop($this->name, $this->getTimeout());

        if (!is_array($payload)) {
            return false;
        }

        return Job::create($payload['class'], $payload['args']);
    }
}