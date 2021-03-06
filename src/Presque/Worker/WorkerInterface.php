<?php

/*
 * This file is part of the Presque package.
 *
 * (c) Justin Rainbow <justin.rainbow@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Presque\Worker;

use Presque\Queue\QueueInterface;

interface WorkerInterface
{
	function getId();

	function addQueue(QueueInterface $queue);

	function removeQueue(QueueInterface $queue);

	function getQueues();

	function isRunning();

	function isDying();

	function run();
}