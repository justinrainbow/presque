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

final class Events
{
	const WORK_STARTED = 'presque.work.started';

	const WORK_PAUSED = 'presque.work.paused';

	const WORK_STOPPED = 'presque.work.stopped';

	const JOB_STARTED = 'presque.job.started';

	const JOB_FINISHED = 'presque.job.finished';
}