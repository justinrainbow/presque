<?php

namespace Presque\Tests\Job;

use Presque\Job\Description;

class DescriptionTest extends \PHPUnit_Framework_TestCase
{
    public function testInstantiatingDescription()
    {
        $this->assertInstanceOf('Presque\Job\Description', new Description());
    }
}