<?php

/*
 * This file is part of the Presque package.
 *
 * (c) Justin Rainbow <justin.rainbow@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Presque\Tests\Storage;

use Presque\Tests\TestCase;
use Presque\Storage\PhpredisStorage;
use Mockery as m;

class PhpredisStorageTest extends TestCase
{
    protected $redis;
    protected $storage;

    public function testPushingDataOntoList()
    {
        $payload = array(
            'class' => 'test',
            'args'  => array()
        );

        $this->redis
            ->expects($this->once())
            ->method('lPush')
            ->with($this->equalTo('list'), $this->equalTo(json_encode($payload)));

        $this->storage->push('list', $payload);
    }

    public function testPopingDataFromList()
    {
        $payload = array(
            'class' => 'test',
            'args'  => array()
        );

        $this->redis
            ->expects($this->exactly(3))
            ->method('blPop')
            ->with($this->equalTo('list'), $this->equalTo(5))
            ->will($this->onConsecutiveCalls(
                array('list', json_encode($payload)),
                array(),
                false
            ));

        $result = $this->storage->pop('list', 5);
        $this->assertTrue(is_array($result));
        $this->assertArrayHasKey('class', $result);
        $this->assertArrayHasKey('args', $result);
        $this->assertEquals('test', $result['class']);

        $this->assertFalse($this->storage->pop('list', 5));
        $this->assertFalse($this->storage->pop('list', 5));
    }

    public function testPrefixingRedisData()
    {
        $this->assertEquals(null, $this->storage->getPrefix());

        $this->redis
            ->expects($this->once())
            ->method('setOption')
            ->with($this->equalTo(\Redis::OPT_PREFIX), $this->equalTo('presque:'));

        $this->storage->setPrefix('presque');
    }

    protected function setUp()
    {
        if (!extension_loaded('redis')) {
            $this->markTestSkipped('The phpredis extension is not loaded.');
        } else {
            $this->redis = $this->createRedisMock();
            $this->storage = new PhpredisStorage($this->redis);
        }
    }

    protected function createRedisMock()
    {
        return $this->getMock('Redis');
    }
}