<?php

/*
 * This file is part of the Presque package.
 *
 * (c) Justin Rainbow <justin.rainbow@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Presque\Log;

use Monolog\Logger;

/**
 * Log adapter for Monolog
 *
 * @link https://github.com/Seldaek/monolog
 */
class MonologLogAdapter extends AbstractLogAdapter
{
    /**
     * {@inheritdoc}
     */
    public function __construct(Logger $logObject)
    {
        $this->log = $logObject;
    }
}