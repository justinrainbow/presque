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
 * @link https://github.com/guzzle/guzzle/blob/master/src/Guzzle/Common/Log/MonologLogAdapter.php
 * @link https://github.com/Seldaek/monolog
 */
class MonologLogAdapter implements LoggerInterface
{
    /**
     * syslog to Monolog mappings
     */
    private static $mapping = array(
        LOG_DEBUG   => Logger::DEBUG,
        LOG_INFO    => Logger::INFO,
        LOG_WARNING => Logger::WARNING,
        LOG_ERR     => Logger::ERROR,
        LOG_CRIT    => Logger::CRITICAL,
        LOG_ALERT   => Logger::ALERT
    );

    /**
     * {@inheritdoc}
     */
    public function __construct(Logger $logObject)
    {
        $this->log = $logObject;
    }

    /**
     * {@inheritdoc}
     */
    public function log($message, $priority = LOG_INFO, $extras = null)
    {
        $this->log->addRecord(self::$mapping[$priority], $message);
    }
}