<?php

namespace Symfony\Cmf\Component\ContentType;

use Symfony\Cmf\Component\ContentType\MappingInterface;

/**
 * Represents the abstract mapping for a complex content type
 *
 * For example an Image object which needs to be mapped to a single
 * property in the content object:
 *
 *    {
 *        'path' => (string)
 *        'width' => (integer)
 *        '...' => (...)
 *    }
 *
 * Storage drivers will then be able to automatically map complex objects
 * belonging to the field types.
 */
class CompoundMapping implements \IteratorAggregate, MappingInterface
{
    private $mappings;

    public function __construct(array $mappings = [])
    {
        $this->mappings = $mappings;
    }

    public function getIterator()
    {
        return new \ArrayIterator($this->mappings);
    }
}

