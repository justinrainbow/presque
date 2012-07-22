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

    public function push($listName, $payload)
    {

    }

    public function pop($listName, $waitTimeout = null)
    {
        if ($payload = $this->connection->blpop($this->getKey($listName), $waitTimeout)) {
            return json_decode($payload, true);
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