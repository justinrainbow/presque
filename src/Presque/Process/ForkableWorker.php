<?php

/*
 * This file is part of the Presque package.
 *
 * (c) Justin Rainbow <justin.rainbow@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Presque\Process;

class ForkableWorker
{
    private $active;
    private $pid;
    private $timeout;
    private $startTime;

    public function __construct($timeout = null)
    {
        pcntl_signal(SIGALRM, array(&$this, 'onTimeout'));

        $this->timeout = $timeout;
    }

    public function forkAndWait($callback)
    {
        if (!is_callable($callback)) {
            throw new \InvalidArgumentException(
                'The $callback passed to forkAndWait must be callable'
            );
        }
        $this->startTime = microtime(true);

        $this->start();

        $this->pid = $this->fork();

        // Forked and we're the child. Run the job.
        if ($this->isChild()) {
            call_user_func($callback, $this);

            if (0 === $this->pid) {
                exit(0);
            }
        }

        if ($this->isParent()) {
            $this->pid = false;

            return $this->waitForChild();
        }
    }

    public function start()
    {
        $this->active = true;
        if (null !== $this->timeout) {
            pcntl_alarm($this->timeout);
        }
    }

    public function cancel()
    {
        $this->active = false;

        if ($this->isChild()) {
            exit(SIGKILL);
        }

        if (!$this->pid) {
            return;
        }

        if (posix_kill($this->pid, SIGKILL)) {
            $this->pid = null;

            return false;
        }

        $this->pid = null;

        return true;
    }

    public function onTimeout()
    {
        if ($this->active) {
            throw new \RuntimeException('Timeout');
        }
    }

    protected function isChild()
    {
        return 0 === $this->pid;
    }

    protected function isParent()
    {
        return 0 < $this->pid;
    }

    protected function waitForChild()
    {
        // Wait until the child process finishes before continuing
        pcntl_wait($status);
        $exitStatus = pcntl_wexitstatus($status);

        $this->pid = null;

        return $exitStatus;
    }

    /**
     * Attempt to fork a child process from the parent to run a job in.
     *
     * Return values are those of pcntl_fork().
     *
     * @return integer 0 for the forked child, the PID of the child for the parent.
     *
     * @throws RuntimeException If pcntl_fork() returns -1
     */
    protected function fork()
    {
        $pid = pcntl_fork();
        if (-1 === $pid) {
            throw new \RuntimeException('Unable to fork child worker.');
        }

        return $pid;
    }
}