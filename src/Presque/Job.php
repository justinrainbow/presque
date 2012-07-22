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

use Presque\Exception\InvalidArgumentException;

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

        if (!$method->isPublic()) {
            $visibility = 'unknown';
            if ($method->isPrivate()) {
                $visibility = 'private';
            } elseif ($method->isProtected()) {
                $visibility = 'protected';
            }

            throw new InvalidArgumentException(
                'The "perform" method for class "' . $class . '" must be public, not ' . $visibility
            );
        }

        if (count($args) < $method->getNumberOfRequiredParameters()) {
            throw new InvalidArgumentException(
                'The ' . $class . ' has ' . $method->getNumberOfRequiredParameters() . ' required '.
                'arguments, but only ' . count($args) . ' were provided.'
            );
        }

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
        $this->lastResult = $this->lastError = null;

        $this->setStatus(StatusInterface::RUNNING);

        try {
            $this->lastResult = $this->reflMethod->invokeArgs(
                $this->getInstance(),
                $this->getArguments()
            );

            $this->setStatus(StatusInterface::SUCCESS);
        } catch (\Exception $e) {
            $this->setStatus(StatusInterface::FAILED);
            $this->lastError = $e;
        }

        return $this;
    }
}