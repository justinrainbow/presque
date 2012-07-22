<?php

/*
 * This file is part of the Presque package.
 *
 * (c) Justin Rainbow <justin.rainbow@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Presque;

interface StatusInterface
{
    CONST SUCCESS = 1;
    CONST FAILED  = 2;
    CONST RUNNING = 4;
    CONST EXPIRED = 7;
    CONST DYING   = 8;
    CONST STOPPING = 13;
    CONST STOPPED  = 14;
}