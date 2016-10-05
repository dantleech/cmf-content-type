<?php

namespace Psi\Component\ContentType;

use Psi\Component\ContentType\Mapping\CompoundMapping;

/**
 * Builder for compound mappings.
 */
class MappingBuilderCompound
{
    private $registry;
    private $mappings = [];
    private $classFqn;

    public function __construct(MappingRegistry $registry, $classFqn)
    {
        $this->classFqn = $classFqn;
        $this->registry = $registry;
    }

    public function map($propertyName, $mappingName, array $options = [])
    {
        $mapping = $this->registry->getConfiguredMapping($mappingName, $options);

        if (isset($this->mappings[$propertyName])) {
            throw new \InvalidArgumentException(sprintf(
                'Property "%s" is already mapped',
                $propertyName
            ));
        }

        $this->mappings[$propertyName] = $mapping;

        return $this;
    }

    public function getCompound()
    {
        return new CompoundMapping($this->classFqn, $this->mappings);
    }
}
