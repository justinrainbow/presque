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

abstract class AbstractLogAdapterTest extends TestCase
{
	public function getLogLevels()
    {
        return array(
            array('debug'),
            array('info'),
            array('warn'),
            array('err'),
            array('crit'),
            array('emerg'),
            array('notice'),
            array('alert'),
        );
    }
}