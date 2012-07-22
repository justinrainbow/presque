<?php

/*
 * This file is part of the Presque package.
 *
 * (c) Justin Rainbow <justin.rainbow@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Presque\Tests;

use Presque\Queue;

class QueueTest extends TestCase
{
    public function testAddingJobsToQueue()
    {
        $job = $this->getMock('Presque\JobInterface');
        $storage = $this->getMock('Presque\Storage\StorageInterface');

        $queue = new Queue('queue');
        $queue->setStorage($storage);

        $expectedPayload = array(
            'class' => 'anything',
            'args'  => array()
        );

        $job
            ->expects($this->once())
            ->method('getClass')
            ->will($this->returnValue('anything'));

        $job
            ->expects($this->once())
            ->method('getArguments')
            ->will($this->returnValue(array()));

        $storage
            ->expects($this->once())
            ->method('push')
            ->with($this->equalTo('queue'), $this->equalTo($expectedPayload));

        $queue->enqueue($job);

    }
}