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
        $this->log->emerg($message, $context);
    }

    /**
     * {@inheritDoc}
     */
    public function alert($message, array $context = array())
    {
        $this->log->alert($message, $context);
    }

    /**
     * {@inheritDoc}
     */
    public function crit($message, array $context = array())
    {
        $this->log->crit($message, $context);
    }

    /**
     * {@inheritDoc}
     */
    public function err($message, array $context = array())
    {
        $this->log->err($message, $context);
    }

    /**
     * {@inheritDoc}
     */
    public function warn($message, array $context = array())
    {
        $this->log->warn($message, $context);
    }

    /**
     * {@inheritDoc}
     */
    public function notice($message, array $context = array())
    {
        $this->log->notice($message, $context);
    }

    /**
     * {@inheritDoc}
     */
    public function info($message, array $context = array())
    {
        $this->log->info($message, $context);
    }

    /**
     * {@inheritDoc}
     */
    public function debug($message, array $context = array())
    {
        $this->log->debug($message, $context);
    }
}