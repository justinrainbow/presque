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

/**
 * Abstract log adapter for Presque
 */
class ClosureLogAdapter implements LoggerInterface
{
    protected $log;

    public function __construct($log = null)
    {
        if (null !== $log && !is_callable($log)) {
            throw new \InvalidArgumentException(
                'The ClosureLogAdapter must be provided a valid callable.'
            );
        }

        $this->log = $log;
    }

    /**
     * {@inheritDoc}
     */
    public function emerg($message, array $context = array())
    {
        if (null !== $this->log) {
            call_user_func($this->log, $message, 'emerg', $context);
        }
    }

    /**
     * {@inheritDoc}
     */
    public function alert($message, array $context = array())
    {
        if (null !== $this->log) {
            call_user_func($this->log, $message, 'alert', $context);
        }
    }

    /**
     * {@inheritDoc}
     */
    public function crit($message, array $context = array())
    {
        if (null !== $this->log) {
            call_user_func($this->log, $message, 'crit', $context);
        }
    }

    /**
     * {@inheritDoc}
     */
    public function err($message, array $context = array())
    {
        if (null !== $this->log) {
            call_user_func($this->log, $message, 'err', $context);
        }
    }

    /**
     * {@inheritDoc}
     */
    public function warn($message, array $context = array())
    {
        if (null !== $this->log) {
            call_user_func($this->log, $message, 'warn', $context);
        }
    }

    /**
     * {@inheritDoc}
     */
    public function notice($message, array $context = array())
    {
        if (null !== $this->log) {
            call_user_func($this->log, $message, 'notice', $context);
        }
    }

    /**
     * {@inheritDoc}
     */
    public function info($message, array $context = array())
    {
        if (null !== $this->log) {
            call_user_func($this->log, $message, 'info', $context);
        }
    }

    /**
     * {@inheritDoc}
     */
    public function debug($message, array $context = array())
    {
        if (null !== $this->log) {
            call_user_func($this->log, $message, 'debug', $context);
        }
    }
}