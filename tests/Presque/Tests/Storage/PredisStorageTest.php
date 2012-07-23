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
use Presque\Storage\PredisStorage;
use Mockery as m;

class PredisStorageTest extends TestCase
{
    protected $predis;
    protected $storage;

    public function testPushingDataOntoList()
    {
        $payload = array(
            'class' => 'test',
            'args'  => array()
        );

        $this->predis
            ->shouldReceive('rpush')
            ->with('list', json_encode($payload))
            ->once();

        $this->storage->push('list', $payload);
    }

    public function testPopingDataFromList()
    {
        $payload = array(
            'class' => 'test',
            'args'  => array()
        );

        $this->predis
            ->shouldReceive('blpop')->with('list', 5)->once()->andReturn(array('list', json_encode($payload)))->ordered('pop')
            ->shouldReceive('blpop')->with('presque:list', 5)->once()->andReturn(array('presque:list', json_encode($payload)))->ordered('pop')
            ->shouldReceive('blpop')->with('presque:list', 5)->once()->andReturn(false)->ordered('pop')
            ->shouldReceive('blpop')->with('presque:list', 5)->once()->andReturn(array())->ordered('pop');

        $result = $this->storage->pop('list', 5);
        $this->assertTrue(is_array($result));
        $this->assertArrayHasKey('class', $result);
        $this->assertArrayHasKey('args', $result);
        $this->assertEquals('test', $result['class']);

        $this->storage->setPrefix('presque');
        $this->assertEquals('presque', $this->storage->getPrefix());
        $result = $this->storage->pop('list', 5);
        $this->assertTrue(is_array($result));
        $this->assertArrayHasKey('class', $result);
        $this->assertArrayHasKey('args', $result);
        $this->assertEquals('test', $result['class']);

        $this->assertFalse($this->storage->pop('list', 5));
        $this->assertFalse($this->storage->pop('list', 5));
    }

    protected function setUp()
    {
        $this->predis = $this->createPredisMock();
        $this->storage = new PredisStorage($this->predis);
    }

    protected function createPredisMock()
    {
        return m::mock('Predis\Client');
    }
}