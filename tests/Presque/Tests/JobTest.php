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

use Presque\Job;

class JobTest extends TestCase
{
    /**
     * @expectedException Presque\Exception\InvalidArgumentException
     */
    public function testCreatingAnInvalidJob()
    {
        Job::create('NonExistant\Class', array(1, 2, 3));
    }

    /**
     * @expectedException Presque\Exception\InvalidArgumentException
     */
    public function testCreatingASimpleJobWithNoArguments()
    {
        Job::create('Presque\Tests\Jobs\SimpleJob');
    }

    /**
     * @expectedException Presque\Exception\InvalidArgumentException
     */
    public function testCreatingASimpleJobWithTooFewArguments()
    {
        Job::create('Presque\Tests\Jobs\SimpleJob', array(1));
    }

    /**
     * @expectedException Presque\Exception\InvalidArgumentException
     */
    public function testCreatingAJobWithInvalidPerformMethod()
    {
        Job::create('Presque\Tests\Jobs\InvalidJob');
    }

    /**
     * @expectedException Presque\Exception\InvalidArgumentException
     */
    public function testCreatingAJobWithPrivatePerformMethod()
    {
        Job::create('Presque\Tests\Jobs\PrivateJob');
    }

    /**
     * @expectedException Presque\Exception\InvalidArgumentException
     */
    public function testCreatingAnIncompleteJob()
    {
        Job::create('Presque\Tests\Jobs\IncompleteJob');
    }

    public function testCreatingASimpleJob()
    {
        $job = Job::create('Presque\Tests\Jobs\SimpleJob', array('simple', 'jobs'));

        try {
            $job->perform();
        } catch (\RuntimeException $e) {
            $this->assertEquals('$arg2 is expected to be "job"', $e->getMessage());
        }

        $job = Job::create('Presque\Tests\Jobs\SimpleJob', array('simplest', 'jobs'));

        try {
            $job->perform();
        } catch (\RuntimeException $e) {
            $this->assertEquals('$arg1 is expected to be "simple"', $e->getMessage());
        }

        $job = Job::create('Presque\Tests\Jobs\SimpleJob', array('simple', 'job'));
        $this->assertTrue($job->perform()->isSuccessful());
    }

}