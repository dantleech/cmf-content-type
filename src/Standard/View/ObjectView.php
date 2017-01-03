<?php

declare(strict_types=1);

namespace Psi\Component\ContentType\Standard\View;

use Psi\Component\View\ViewInterface;

class ObjectView implements ViewInterface, \ArrayAccess, \Iterator
{
    private $viewClosures;

    public function __construct(array $viewClosures)
    {
        $this->viewClosures = $viewClosures;
    }

    public function offsetGet($name)
    {
        if (!isset($this->viewClosures[$name])) {
            throw new \InvalidArgumentException(sprintf(
                'Child view "%s" has not been set, known children: "%s"',
                $name, implode('", "', array_keys($this->viewClosures))
            ));
        }

        return $this->viewClosures[$name]();
    }

    public function offsetExists($name)
    {
        return isset($this->viewClosures[$name]);
    }

    public function offsetSet($name, $value)
    {
        throw new \BadMethodCallException(
            'Cannot modify an object view.'
        );
    }

    public function offsetUnset($name)
    {
        throw new \BadMethodCallException(
            'Cannot modify an object view.'
        );
    }

    public function current()
    {
        return $this->offsetGet(key($this->viewClosures));
    }

    public function key()
    {
        return key($this->viewClosures);
    }

    public function next()
    {
        return next($this->viewClosures);
    }

    public function rewind()
    {
        return reset($this->viewClosures);
    }

    public function valid()
    {
        return key($this->viewClosures) !== null;
    }
}
