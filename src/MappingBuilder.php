<?php

namespace Psi\Component\ContentType;

use Psi\Component\ContentType\Mapping\CollectionMapping;


/**
 * Builder for mappings.
 *
 * In the case where a field uses only a single property
 * the `single` method should be used.
 *
 * In the case of multiple properties, `map(...)` should be called
 * as many times as required before returning the result
 * of `getCompound`.
 */
class MappingBuilder
{
    private $registry;

    public function __construct(MappingRegistry $registry)
    {
        $this->registry = $registry;
    }

    public function single($mappingName, array $options = []): ConfiguredMapping
    {
        return $this->registry->getConfiguredMapping($mappingName, $options);
    }

    public function compound($classFqn): MappingBuilderCompound
    {
        return new MappingBuilderCompound($this->registry, $classFqn);
    }

    public function collection(): CollectionMapping
    {
        return new CollectionMapping();
    }
}
