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
use Presque\Job\Job;

class Queue extends AbstractQueue
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

    /**
     * {@inheritDoc}
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * {@inheritDoc}
     */
    public function getTimeout()
    {
        return $this->timeout;
    }

    /**
     * {@inheritDoc}
     */
    public function getStorage()
    {
        return $this->storage;
    }

    /**
     * {@inheritDoc}
     */
    public function setStorage(StorageInterface $storage)
    {
        $this->storage = $storage;
    }

    /**
     * {@inheritDoc}
     */
    public function enqueue(JobInterface $job)
    {
        $this->storage->push($this->name, array(
            'class' => $job->getClass(),
            'args'  => $job->getArguments(),
        ));
    }

    /**
     * {@inheritDoc}
     */
    public function reserve()
    {
        $payload = $this->storage->pop($this->name, $this->getTimeout());

        if (!is_array($payload)) {
            return false;
        }

        return Job::recreate($payload);
    }
}