# Presque [![Build Status](https://secure.travis-ci.org/justinrainbow/presque.png?branch=master)](http://travis-ci.org/justinrainbow/presque)

Presque is a job queuing lib for PHP 5.3+.  It is similar to the [Resque](https://github.com/defunkt/resque/)
library, but does not aim to be completely compatible.

Presque is still under heavy development and is not ready for production use.

## Creating your first Job

```php
<?php

namespace Acme\Queue;

class WidgetJob
{
    /**
     * @param string $group
     */
    public function perform($group)
    {

    }
}
```

Once the basic class has been created, all you need to do is add the job to
the queue.

```console
presque queue 'Acme\Queue\WidgetJob' 'mygroup'
```

## Roadmap

 * Make the Workers fork-able - so Presque can be ran as a daemon
 * 100% test coverage
 * Additional storage engines (eg. filesystem, phpredis, etc)
 * Full logging integration