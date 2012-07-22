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

use Predis\Client;

class PredisStorage implements StorageInterface
{
    private $prefix;
    private $connection;

    public function __construct(Client $connection, $prefix = null)
    {
        $this->prefix = $prefix;
        $this->connection = $connection;
    }

    public function setPrefix($prefix = null)
    {
        $this->prefix = $prefix;
    }

    public function getPrefix()
    {
        return $this->prefix;
    }

    public function push($listName, $payload)
    {
        $this->connection->lpush($this->getKey($listName), json_encode($payload));
    }

    public function pop($listName, $waitTimeout = null)
    {
        $payload = $this->connection->blpop($this->getKey($listName), $waitTimeout);

        if (is_array($payload) && isset($payload[1])) {
            return json_decode($payload[1], true);
        }

        return false;
    }

    protected function getKey($key)
    {
        if (null === $this->prefix) {
            return $key;
        }

        return $this->prefix . ':' . $key;
    }
}