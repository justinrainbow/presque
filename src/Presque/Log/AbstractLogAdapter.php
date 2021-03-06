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
abstract class AbstractLogAdapter implements LoggerInterface
{
    protected $log;

    /**
     * {@inheritDoc}
     */
    public function emerg($message, array $context = array())
    {
        if (null !== $this->log) {
            $this->log->emerg($message, $context);
        }
    }

    /**
     * {@inheritDoc}
     */
    public function alert($message, array $context = array())
    {
        if (null !== $this->log) {
            $this->log->alert($message, $context);
        }
    }

    /**
     * {@inheritDoc}
     */
    public function crit($message, array $context = array())
    {
        if (null !== $this->log) {
            $this->log->crit($message, $context);
        }
    }

    /**
     * {@inheritDoc}
     */
    public function err($message, array $context = array())
    {
        if (null !== $this->log) {
            $this->log->err($message, $context);
        }
    }

    /**
     * {@inheritDoc}
     */
    public function warn($message, array $context = array())
    {
        if (null !== $this->log) {
            $this->log->warn($message, $context);
        }
    }

    /**
     * {@inheritDoc}
     */
    public function notice($message, array $context = array())
    {
        if (null !== $this->log) {
            $this->log->notice($message, $context);
        }
    }

    /**
     * {@inheritDoc}
     */
    public function info($message, array $context = array())
    {
        if (null !== $this->log) {
            $this->log->info($message, $context);
        }
    }

    /**
     * {@inheritDoc}
     */
    public function debug($message, array $context = array())
    {
        if (null !== $this->log) {
            $this->log->debug($message, $context);
        }
    }
}