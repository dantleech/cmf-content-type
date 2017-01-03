<?php

declare(strict_types=1);

namespace Psi\Component\ContentType\Standard\View;

use Psi\Component\View\ViewFactory;
use Psi\Component\View\ViewInterface;

class CollectionView implements ViewInterface, \Iterator
{
    private $collection;
    private $factory;
    private $viewType;
    private $viewOptions;

    public function __construct(
        ViewFactory $factory,
        \Traversable $collection,
        string $viewType,
        array $viewOptions
    ) {
        $this->factory = $factory;
        $this->collection = new \IteratorIterator($collection);
        $this->viewType = $viewType;
        $this->viewOptions = $viewOptions;
    }

    public function current()
    {
        return $this->factory->create(
            $this->viewType,
            $this->collection->current(),
            $this->viewOptions
        );
    }

    public function next()
    {
        return $this->collection->next();
    }

    public function key()
    {
        return $this->collection->key();
    }

    public function rewind()
    {
        return $this->collection->rewind();
    }

    public function valid()
    {
        return $this->collection->valid();
    }
}
