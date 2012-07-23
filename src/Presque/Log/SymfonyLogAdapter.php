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

use Symfony\Component\HttpKernel\Log\LoggerInterface as SymfonyLoggerInterface;

/**
 * Symfony HttpKernel LoggerInterface adapter
 */
class SymfonyLogAdapter extends AbstractLogAdapter
{
    /**
     * {@inheritdoc}
     */
    public function __construct(SymfonyLoggerInterface $logObject)
    {
        $this->log = $logObject;
    }
}