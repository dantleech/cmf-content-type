<?php

declare(strict_types=1);

namespace Psi\Component\ContentType;

class MappingResolver
{
    /**
     * @var MappingRegistry
     */
    private $registry;

    /**
     * @param MappingRegistry $registry
     */
    public function __construct(MappingRegistry $registry)
    {
        $this->registry = $registry;
    }

    public function resolveMapping(FieldInterface $field): ConfiguredMapping
    {
        $mappingBuilder = new MappingBuilder($this->registry);
        $mapping = $field->getMapping($mappingBuilder);

        if ($mapping instanceof ConfiguredMapping) {
            return $mapping;
        }

        if ($mapping instanceof MappingInterface) {
            return new ConfiguredMapping($mapping, []);
        }

        if ($mapping instanceof MappingBuilderCompound) {
            return new ConfiguredMapping($mapping->getCompound(), []);
        }

        throw new \InvalidArgumentException(sprintf(
            'Mapping must be a MappingInterface or a MappingBuilderCompound, got "%s"',
            is_object($field) ? get_class($field) : gettype($field)
        ));
    }
}
