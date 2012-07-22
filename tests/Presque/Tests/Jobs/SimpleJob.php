<?php

/*
 * This file is part of the Presque package.
 *
 * (c) Justin Rainbow <justin.rainbow@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Presque\Tests\Jobs;

class SimpleJob
{
    public function perform($arg1, $arg2)
    {
        if ($arg1 !== 'simple') {
            throw new \RuntimeException('$arg1 is expected to be "simple"');
        }

        if ($arg2 !== 'job') {
            throw new \RuntimeException('$arg2 is expected to be "job"');
        }

        return true;
    }
}