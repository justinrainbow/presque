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
use Presque\Storage\StorageFactory;
use Mockery as m;

class StorageFactoryTest extends TestCase
{
    /**
     * @expectedException InvalidArgumentException
     */
    public function testFailingToCreateStorage()
    {
        StorageFactory::create('tcp://:6379');
    }

    public function testCreatingStorageWithPrefix()
    {
        try {
            $storage = StorageFactory::create('tcp://localhost:6379', 'test');

            $this->assertInstanceOf('Presque\Storage\StorageInterface', $storage);
            $this->assertEquals('test', $storage->getPrefix());
        } catch (\RedisException $e) {
            // this is OK - redis might not be running!
        }
    }

    public function testCreatingStorage()
    {
        try {
            $storage = StorageFactory::create('tcp://localhost:6379');

            $this->assertInstanceOf('Presque\Storage\StorageInterface', $storage);
        } catch (\RedisException $e) {
            // this is OK - redis might not be running!
        }
    }
}