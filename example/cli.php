#!/usr/bin/env php
<?php

$loader = require_once __DIR__.'/../vendor/autoload.php';
$loader->add('Presque\\Example', __DIR__);

$connection = new Predis\Client(isset($_SERVER['REDIS_DSN']) ? $_SERVER['REDIS_DSN'] : 'tcp://127.0.0.1:6379');

$storage = new Presque\Storage\PredisStorage($connection, 'presque');

$queue = new Presque\Queue('example', $storage);

$worker = new Presque\Worker();
$worker->addQueue($queue);

if (count($argv) > 1) {
	$queue->enqueue(Presque\Job::create('Presque\Example\SimpleJob', array($argv[1])));
} else {
	$worker->start();
}