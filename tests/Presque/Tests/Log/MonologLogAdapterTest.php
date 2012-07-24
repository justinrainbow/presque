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

use Presque\Tests\TestCase;
use Presque\Log\MonologLogAdapter;
use Monolog\Logger;

class MonologLogAdapterTest extends AbstractLogAdapterTest
{
    protected $logger;
    protected $adapter;

    /**
     * @dataProvider getLogLevels
     */
    public function testLoggingMessages($level)
    {
        $this->logger
            ->expects($this->once())
            ->method($level)
            ->with($this->equalTo("My message"));

        $this->adapter->{$level}("My message");
    }

    protected function setUp()
    {
        $this->logger = $this->createMonologMock();
        $this->adapter = new MonologLogAdapter($this->logger);
    }

    protected function createMonologMock()
    {
        return $this->getMockBuilder('Monolog\Logger')
            ->disableOriginalConstructor()
            ->getMock();
    }
}