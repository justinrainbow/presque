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

use Presque\Worker\WorkerFactory;
use Presque\Worker\WorkerFactoryInterface;
use Presque\Queue\QueueFactory;
use Presque\Queue\QueueFactoryInterface;
use Presque\Job\JobFactory;
use Presque\Job\JobFactoryInterface;
use Presque\Event\EventDispatcherAwareInterface;
use Presque\Log\LoggerAwareInterface;
use Presque\Log\LoggerInterface;
use Symfony\Component\EventDispatcher\EventDispatcher;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;

class Presque implements EventDispatcherAwareInterface, LoggerAwareInterface
{
    const VERSION = "0.1.0";

    private static $eventSubscribers = array(
        'Presque\EventListener\JobRetryListener'
    );

    protected $workerFactory;
    protected $queueFactory;
    protected $jobFactory;
    protected $dispatcher;
    protected $logger;

    public function __construct(
        WorkerFactoryInterface $workerFactory = null,
        QueueFactoryInterface $queueFactory = null,
        JobFactoryInterface $jobFactory = null,
        EventDispatcherInterface $dispatcher = null,
        LoggerInterface $logger = null
    )
    {
        if (null === $dispatcher) {
            $dispatcher = new EventDispatcher();
            foreach (static::$eventSubscribers as $class) {
                $dispatcher->addSubscriber(new $class());
            }
        }

        $this->workerFactory = $workerFactory ?: new WorkerFactory();
        $this->queueFactory  = $queueFactory  ?: new QueueFactory();
        $this->jobFactory    = $jobFactory    ?: new JobFactory();
        $this->dispatcher    = $dispatcher;
        $this->logger        = $logger;
    }

    /**
     * @see Presque\Worker\WorkerFactoryInterface::create
     */
    public function createWorker($id = null)
    {
        return $this->injectServices(
            $this->workerFactory->create($id)
        );
    }

    /**
     * @see Presque\Queue\QueueFactoryInterface::create
     */
    public function createQueue($name)
    {
        return $this->injectServices(
            $this->queueFactory->create($name)
        );
    }

    /**
     * @see Presque\Job\JobFactoryInterface::create
     */
    public function createJob($class, array $args = array())
    {
        return $this->injectServices(
            $this->jobFactory->create($class, $args)
        );
    }

    /**
     * {@inheritDoc}
     */
    public function setEventDispatcher(EventDispatcherInterface $dispatcher = null)
    {
        $this->dispatcher = $dispatcher;
    }

    /**
     * {@inheritDoc}
     */
    public function hasEventDispatcher()
    {
        return null !== $this->dispatcher;
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

    /**
     * Injects instances of the EventDispatcher and Logger to objects that implement
     * the EventDispatcherAwareInterface or the LoggerAwareInterface.
     *
     * @param mixed $object
     *
     * @return mixed $object injected with the services
     */
    protected function injectServices($object)
    {
        if ($this->hasEventDispatcher()) {
            if ($object instanceof EventDispatcherAwareInterface) {
                $object->setEventDispatcher($this->dispatcher);
            }
        }

        if ($this->hasLogger()) {
            if ($object instanceof LoggerAwareInterface) {
                $object->setLogger($this->logger);
            }
        }

        return $object;
    }
}