<?php

namespace Psi\Component\ContentType\Storage\Doctrine\PhpcrOdm;

use Doctrine\Common\Persistence\Mapping\ClassMetadata;
use Doctrine\Common\Persistence\Mapping\Driver\MappingDriver;
use Psi\Component\ContentType\FieldRegistry;
use Psi\Component\ContentType\Mapping\CompoundMapping;
use Psi\Component\ContentType\MappingRegistry;
use Psi\Component\ContentType\MappingResolver;

/**
 * The content type driver will take care of automatically mapping objects that
 * are required by content type fields (a CT field might use an Image model, or
 * a FormConfig object for example). This driver will automatically handle the
 * mapping for these fields.
 */
class ContentTypeDriver implements MappingDriver
{
    private $registry;
    private $fieldMappings = [];
    private $initialized = false;
    private $mappingRegistry;
    private $mappingResolver;
    private $mapper;

    public function __construct(
        FieldRegistry $registry,
        MappingRegistry $mappingRegistry,
        MappingResolver $resolver,
        FieldMapper $mapper
    ) {
        $this->registry = $registry;
        $this->mappingRegistry = $mappingRegistry;
        $this->mappingResolver = $resolver;
        $this->mapper = $mapper;
    }

    private function init()
    {
        if ($this->initialized) {
            return;
        }

        foreach ($this->registry->all() as $fieldName => $field) {
            try {
                $mapping = $this->mappingResolver->resolveMapping($field);
            } catch (\Exception $e) {
                throw new \RuntimeException(sprintf(
                    'Could not map field type "%s"',
                    $fieldName
                ), null, $e);
            }

            // scalar fields will be mapped by the metadata subscriber.
            if (!$mapping->getMapping() instanceof CompoundMapping) {
                continue;
            }

            // otherwise the field maps to its own object, so this driver will
            // take care of mapping that.
            $this->fieldMappings[$mapping->getMapping()->getClass()] = $mapping;
        }

        $this->initialized = true;
    }

    /**
     * Loads the metadata for the specified class into the provided container.
     *
     * @param string        $className
     * @param ClassMetadata $metadata
     *
     * @return void
     */
    public function loadMetadataForClass($className, ClassMetadata $metadata)
    {
        $this->init();

        if (!isset($this->fieldMappings[$className])) {
            return;
        }

        $mapping = $this->fieldMappings[$metadata->getName()];

        // assume there is an ID field.
        // TODO: we should implement an interface if we want this to be a
        //       prerequisite.
        $metadata->mapId([
            'fieldName' => 'id',
            'id' => true,
            'strategy' => 'assigned',
        ]);
        $metadata->setReferenceable(true);

        foreach ($mapping->getMapping() as $fieldName => $fieldMapping) {
            $this->mapper->__invoke($fieldName, $fieldMapping, $metadata);
        }
    }

    /**
     * Gets the names of all mapped classes known to this driver.
     *
     * @return array The names of all mapped classes known to this driver.
     */
    public function getAllClassNames()
    {
        $this->init();

        return array_keys($this->fieldMappings);
    }

    /**
     * Returns whether the class with the specified name should have its metadata loaded.
     * This is only the case if it is either mapped as an Entity or a MappedSuperclass.
     *
     * @param string $className
     *
     * @return bool
     */
    public function isTransient($className)
    {
        $this->init();

        return isset($this->fieldMappings[$className]);
    }
}
