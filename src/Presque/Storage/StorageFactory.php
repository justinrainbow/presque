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

class StorageFactory
{
    public static function create($dsn, $prefix = null)
    {
        $url = static::parse($dsn);

        if (!isset($url['host'])) {
            throw new \InvalidArgumentException(sprintf(
                '"%s" is missing the required "host" part of the URL. '.
                'Please make sure the DSN is a valid URL.',
                $dsn
            ));
        }

        if (extension_loaded('redis')) {
            $redis = new \Redis();
            $redis->connect($url['host'], isset($url['port']) ? $url['port'] : 6379);

            $storage = new PhpredisStorage($redis);
        } else {
            $redis = new \Predis\Client($url);

            $storage = new PredisStorage($redis);
        }

        if (null !== $prefix) {
            $storage->setPrefix($prefix);
        }

        return $storage;
    }

    protected static function parse($dsn)
    {
        $url = parse_url($dsn);

        return $url;
    }
}