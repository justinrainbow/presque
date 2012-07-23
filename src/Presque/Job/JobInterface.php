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

interface JobInterface
{
    /**
     * @param string $class Class that will be performing
     * @param array  $args  List of arguments to pass to the `$class->perform()` method
     *
     * @throws InvalidArgumentException If `$class` does not implement the required methods
     */
    static function create($class, array $args = array());

    /**
     * Recreates a Job instance from the payload data.
     *
     * @param array $payload
     *
     * @return JobInterface
     *
     * @throws InvalidArgumentException If `$payload` is not valid
     */
    static function recreate(array $payload);

    /**
     * @return Boolean
     */
    function isSuccessful();

    /**
     * @return Boolean
     */
    function isError();

    /**
     * @return Boolean
     */
    function isActive();

    /**
     * Does the work
     */
    function perform();

    function prepare();

    function complete();

    /**
     * Returns an instance of the `class`
     *
     * @return mixed
     */
    function getInstance();

    /**
     * Returns the class name associated with this Job
     *
     * @return string
     */
    function getClass();

    /**
     * Returns a list of arguments to pass to the `perform` method
     *
     * @return array
     */
    function getArguments();
}