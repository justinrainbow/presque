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

class InvalidJob
{
    protected function perform()
    {
        throw new \RuntimeException('The InvalidJob should never be performed!');
    }
}