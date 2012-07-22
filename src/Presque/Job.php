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

    private $reflClass;
    private $reflMethod;
    private $instance;

    public static function create($class, array $args = array())
    {
        return new static($class, $args);
    }

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
            throw new InvalidArgumentException(
                'The "perform" method for class "' . $class . '" must be public, not ' . $method->getVisiblity()
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

    public function perform()
    {
        return $this->reflMethod->invokeArgs(
            $this->getInstance(),
            $this->getArguments()
        );
    }

    public function getMaxAttempts()
    {

    }

    public function getClass()
    {
        return $this->class;
    }

    public function getArguments()
    {
        return $this->args;
    }

    public function getInstance()
    {
        if (!$this->instance) {
            $this->instance = $this->reflClass->newInstance();
        }

        return $this->instance;
    }
}