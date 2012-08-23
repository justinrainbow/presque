<?php

namespace Presque\Tests\Job;

use Presque\Job\Description;

class DescriptionTest extends \PHPUnit_Framework_TestCase
{
    public function testInstantiatingDescription()
    {
        $description = new Description();
        $this->assertInstanceOf('Presque\Job\Description', $description);
        $this->assertInstanceOf('Presque\Job\DescriptionInterface', $description);
    }

    public function testArrayAccessDescription()
    {
        $description = new Description(array('water' => 'blue'));

        $this->assertTrue(isset($description['water']));
        $this->assertEquals('blue', $description['water']);

        $description[] = 'green';
        $this->assertCount(2, $description);
        $this->assertEquals('green', $description[0]);

        unset($description[0]);
        $this->assertCount(1, $description);

        $description['water'] = 'green';
        $this->assertEquals('green', $description['water']);
        $this->assertCount(1, $description);
    }
}
