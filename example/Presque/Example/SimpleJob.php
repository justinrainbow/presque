<?php

namespace Presque\Example;

class SimpleJob
{
    public function perform($path)
    {
        file_put_contents(
            $path,
            sprintf('Run at %s!', date('Y-m-d H:i:s')) . PHP_EOL,
            FILE_APPEND
        );
    }
}