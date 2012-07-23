<?php

/*
 * This file is part of the Presque package.
 *
 * (c) Justin Rainbow <justin.rainbow@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Presque\Storage;

use Redis;

class PhpredisStorage implements StorageInterface
{
    private $prefix;
    private $connection;

    public function __construct(Redis $connection, $prefix = null)
    {
        $this->prefix = $prefix;
        $this->connection = $connection;
    }

    public function setPrefix($prefix = null)
    {
        $this->prefix = $prefix;

        $this->connection->setOption(Redis::OPT_PREFIX, null === $prefix ? '' : rtrim($prefix, ':') . ':');
    }

    public function getPrefix()
    {
        return $this->prefix;
    }

    public function push($listName, $payload)
    {
        $this->connection->rPush($listName, json_encode($payload));
    }

    public function pop($listName, $waitTimeout = null)
    {
        $payload = $this->connection->blPop($listName, $waitTimeout);

        if (is_array($payload) && isset($payload[1])) {
            return json_decode($payload[1], true);
        }

        return false;
    }
}