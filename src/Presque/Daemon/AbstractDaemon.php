<?php

/*
 * This file is part of the Presque package.
 *
 * (c) Justin Rainbow <justin.rainbow@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Presque\Daemon;


use Presque\Log\LoggerInterface;
use Presque\Log\LoggerAwareInterface;
use Presque\Log\ClosureLogAdapter;
use Presque\Process\ForkableWorker;

abstract class AbstractDaemon
{
    protected $shutdown    = false;
    protected $paused      = false;
    protected $logger      = null;
    protected $pauseTime   = 5;
    protected $traceMemory = true;
    protected $pid         = null;
    protected $worker;

    public function __construct(DaemonWorkerInterface $worker = null)
    {
        if (null !== $worker) {
            $this->setWorker($worker);
        }
    }

    abstract protected function doLoop();

    public function run()
    {
        $this->startup();

        declare(ticks = 1);

        while ($this->isRunning()) {
            if ($this->isPaused()) {
                $this->sleep($this->pauseTime);

                continue;
            }

            $this->doLoop();
        }
    }


    public function setWorker(DaemonWorkerInterface $worker)
    {
        $worker->setDaemon($this);

        $this->worker = $worker;
    }

    public function detach()
    {
        $pid = pcntl_fork();
        if (-1 === $pid) {
            throw new \RuntimeException(
                'Unable to fork daemon process.'
            );
        }

        if (0 < $pid) {
            fclose(STDIN);
            fclose(STDOUT);
            exit(0);
        }
    }

    /**
     * {@inheritDoc}
     */
    public function setLogger(LoggerInterface $logger = null)
    {
        $this->logger = $logger;
    }

    /**
     * {@inheritDoc}
     */
    public function hasLogger()
    {
        return null !== $this->logger;
    }

    public function isAlive()
    {
        if (!posix_isatty(STDOUT)) {
            posix_kill(posix_getppid(), SIGUSR1);
        }

        if ($this->traceMemory) {
            $memuse = number_format(memory_get_usage() / 1024, 1);
            $daemon = get_class($this);

            $this->logInfo("<RAMS> {$daemon} Memory Usage: {$memuse} KB");
        }
    }

    public function sleep($duration)
    {
        $this->isAlive();
        while ($duration > 0) {
            sleep(min($duration, 60));
            $duration -= 60;

            $this->isAlive();
        }
    }

    public function isRunning()
    {
        return $this->isActive() && $this->shutdown === false;
    }

    public function isPaused()
    {
        return $this->paused === true;
    }

    public function pause()
    {
        $this->logInfo('Pausing job');

        $this->paused = true;
    }

    public function resume()
    {
        $this->logInfo('Resuming job');

        $this->paused = false;
    }

    /**
     * Schedule a worker for shutdown. Will finish processing the current job
     * and when the timeout interval is reached, the worker will shut down.
     */
    public function shutdown()
    {
        $this->shutdown = true;

        $this->logInfo('Starting shutdown of process: ' . getmypid());
    }

    /**
     * Force an immediate shutdown of the worker, killing any child jobs
     * currently running.
     */
    public function abort()
    {
        $this->shutdown();
        $this->worker->abort();
    }

    /**
     * Kill a forked child job immediately. The job it is processing will not
     * be completed.
     */
    public function killChild()
    {
        $this->worker->abort();
    }

    protected function logInfo($msg)
    {
        if ($this->hasLogger()) {
            $this->logger->info($msg);
        }
    }

    /**
     * Perform necessary actions to start a worker.
     */
    protected function startup()
    {
        $this->registerSignalHandlers();
    }

    private function registerSignalHandlers()
    {
        declare(ticks = 1);
        pcntl_signal(SIGTERM, array($this, 'abort'));
        pcntl_signal(SIGINT,  array($this, 'abort'));
        pcntl_signal(SIGQUIT, array($this, 'shutdown'));
        pcntl_signal(SIGUSR1, array($this, 'killChild'));
        pcntl_signal(SIGUSR2, array($this, 'pause'));
        pcntl_signal(SIGCONT, array($this, 'resume'));
    }
}