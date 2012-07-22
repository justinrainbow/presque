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

class MonologLogAdapterTest extends TestCase
{
    protected $logger;
    protected $adapter;

    /**
     * @dataProvider getLogLevels
     */
    public function testLoggingMessages($level, $expectedLevel)
    {
        $this->logger
            ->expects($this->once())
            ->method('addRecord')
            ->with($this->equalTo($expectedLevel), $this->equalTo("My message"));

        $this->adapter->log("My message", $level);
    }

    public function getLogLevels()
    {
        return array(
            array(LOG_DEBUG,   Logger::DEBUG),
            array(LOG_INFO,    Logger::INFO),
            array(LOG_WARNING, Logger::WARNING),
            array(LOG_ERR,     Logger::ERROR),
            array(LOG_CRIT,    Logger::CRITICAL),
            array(LOG_ALERT,   Logger::ALERT),
        );
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