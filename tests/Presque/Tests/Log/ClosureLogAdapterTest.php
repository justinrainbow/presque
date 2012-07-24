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
use Presque\Log\ClosureLogAdapter;
use Monolog\Logger;

class ClosureLogAdapterTest extends AbstractLogAdapterTest
{
    protected $logger;
    protected $adapter;

    /**
     * @dataProvider getLogLevels
     */
    public function testLoggingMessages($level)
    {
        $test    = $this;
        $adapter = new ClosureLogAdapter(function ($message, $type) use (&$test, $level) {
            $test->assertEquals("My message", $message);
            $test->assertEquals($level, $type);
        });

        $adapter->{$level}("My message");
    }
}