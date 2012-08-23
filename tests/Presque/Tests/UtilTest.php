<?php

namespace Presque\Tests;

use Presque\Util;

class UtilTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @dataProvider getTestData
     */
    public function testDumpingData($expected, $input)
    {
        $this->assertEquals($expected, Util::dump($input));
    }

    public function testDumpingResource()
    {
        $resource = fopen(__FILE__, 'r');

        $this->assertEquals('Resource(stream)', Util::dump($resource));

        fclose($resource);
    }

    public function getTestData()
    {
        return array(
            array('null', null),
            array('Array()', array()),
            array('Array(0 => null)', array(null)),
            array('Array(0 => blue)', array('blue')),
            array('green', 'green'),
            array('true', true),
            array('false', 'false'),
            array('false', false),
            array('Object(Presque\Job\Description)', new \Presque\Job\Description()),
        );
    }
}
