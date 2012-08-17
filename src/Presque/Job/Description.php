<?php

namespace Presque\Job;

class Description implements DescriptionInterface, \ArrayAccess
{
    protected $arguments;

    public function __construct(array $arguments = array())
    {
        $this->arguments = $arguments;
    }

    public function offsetSet($offset, $value)
    {
        if (null === $offset) {
            $this->arguments[] = $value;
        } else {
            $this->arguments[$offset] = $value;
        }
    }

    public function offsetExists($offset)
    {
        return isset($this->arguments[$offset]);
    }

    public function offsetUnset($offset)
    {
        unset($this->arguments[$offset]);
    }

    public function offsetGet($offset)
    {
        return $this->arguments[$offset];
    }
}