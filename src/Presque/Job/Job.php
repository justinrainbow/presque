<?php

/*
 * This file is part of the Presque package.
 *
 * (c) Justin Rainbow <justin.rainbow@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Presque\Job;

use Presque\Exception\InvalidArgumentException;
use Presque\StatusInterface;

class Job extends AbstractJob
{
    protected $class;
    protected $args;

    protected $lastResult;
    protected $lastError;

    private $reflClass;
    private $reflMethod;
    private $instance;

    /**
     * {@inheritDoc}
     */
    public static function create($class, array $args = array())
    {
        return new static($class, $args);
    }

    /**
     * {@inheritDoc}
     */
    public static function recreate(array $payload)
    {
        return new static($payload['class'], $payload['args']);
    }

    /**
     * @param string $class Class that will be performing
     * @param array  $args  List of arguments to pass to the `$class->perform()` method
     *
     * @throws InvalidArgumentException If `$class` does not implement the required methods
     */
    public function __construct($class, array $args = array())
    {
        try {
            $refl = new \ReflectionClass($class);
        } catch (\ReflectionException $e) {
            throw new InvalidArgumentException(
                $class . ' is not a valid class. Please make sure it has already been '.
                'defined or can be autoloaded.'
            );
        }

        if (!$refl->hasMethod('perform')) {
            throw new InvalidArgumentException(
                $class . ' must implement the "perform" method to be added to the queue.'
            );
        }

        $method = $refl->getMethod('perform');

        $this->class = $class;
        $this->args  = $args;

        $this->reflClass  = $refl;
        $this->reflMethod = $method;
    }

    /**
     * {@inheritDoc}
     */
    public function getClass()
    {
        return $this->class;
    }

    /**
     * {@inheritDoc}
     */
    public function getArguments()
    {
        return $this->args;
    }

    /**
     * {@inheritDoc}
     */
    public function getInstance()
    {
        if (!$this->instance) {
            $this->instance = $this->reflClass->newInstance();
        }

        return $this->instance;
    }

    /**
     * {@inheritDoc}
     */
    public function perform()
    {
        try {
            $args = $this->getArguments();
            if ($this->isFirstParameterJobInterface()) {
                array_unshift($args, $this);
            }
            $this->lastResult = $this->reflMethod->invokeArgs(
                $this->getInstance(),
                $args
            );

            $this->setStatus(StatusInterface::SUCCESS);
        } catch (\Exception $e) {
            $this->setStatus(StatusInterface::FAILED);
            $this->lastError = $e;
        }

        return $this;
    }

    /**
     * {@inheritDoc}
     */
    public function validate()
    {
        if (!$this->reflMethod->isPublic()) {
            $visibility = 'unknown';
            if ($this->reflMethod->isPrivate()) {
                $visibility = 'private';
            } elseif ($this->reflMethod->isProtected()) {
                $visibility = 'protected';
            }

            throw new InvalidArgumentException(
                'The "perform" method for class "' . $this->class . '" must be public, not ' . $visibility
            );
        }

        $numberOfRequiredParameters = $this->reflMethod->getNumberOfRequiredParameters();
        if ($this->isFirstParameterJobInterface()) {
            $numberOfRequiredParameters--;
        }

        if (count($this->args) < $numberOfRequiredParameters) {
            throw new InvalidArgumentException(
                'The ' . $this->class . ' has ' . $numberOfRequiredParameters . ' required '.
                'arguments, but only ' . count($this->args) . ' were provided.'
            );
        }

        return true;
    }

    public function __toString()
    {
        return (string) $this->getClass();
    }

    protected function isFirstParameterJobInterface()
    {
        $params = $this->reflMethod->getParameters();

        if (0 < count($params)) {
            return $params[0]->getClass() && $params[0]->getClass()->implementsInterface('Presque\\Job\\JobInterface');
        }

        return false;
    }
}