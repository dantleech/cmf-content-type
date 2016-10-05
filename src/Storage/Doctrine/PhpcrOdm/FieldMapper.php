<?php

namespace Psi\Component\ContentType\Storage\Doctrine\PhpcrOdm;

use Doctrine\ODM\PHPCR\Mapping\ClassMetadata;
use Psi\Component\ContentType\ConfiguredMapping;
use Psi\Component\ContentType\Mapping\CollectionMapping;
use Psi\Component\ContentType\Mapping\CompoundMapping;
use Psi\Component\ContentType\Mapping\DateTimeMapping;
use Psi\Component\ContentType\Mapping\IntegerMapping;
use Psi\Component\ContentType\Mapping\ReferenceMapping;
use Psi\Component\ContentType\Mapping\StringMapping;
use Psi\Component\ContentType\MappingInterface;

/**
 * The FieldMapper maps the metadata for PHPCR-ODM fields for
 * a given $fieldName and content-type Mapping object.
 */
class FieldMapper
{
    /**
     * @var PropertyEncoder
     */
    private $encoder;

    public function __construct(PropertyEncoder $encoder)
    {
        $this->encoder = $encoder;
    }

    /**
     * @param string $fieldName
     * @param MappingInterface $fieldMapping
     * @param ClassMetadata $metadata
     */
    public function __invoke($fieldName, ConfiguredMapping $configuredMapping, ClassMetadata $metadata)
    {
        $fieldMapping = $configuredMapping->getMapping();

        if ($fieldMapping instanceof CompoundMapping) {
            $metadata->mapChild([
                'fieldName' => $fieldName,
                'nodeName' => $this->encoder->encode($fieldName),
                'nullable' => true,
            ]);

            return;
        }

        if ($fieldMapping instanceof CollectionMapping) {
            $metadata->mapChildren([
                'fieldName' => $fieldName,
                'fetchDepth' => 1,
                'filter' => $this->encoder->encode($fieldName) . '-*',
                'cascade' => ClassMetadata::CASCADE_ALL,
                'nullable' => true,
            ]);

            return;
        }

        if ($fieldMapping instanceof StringMapping) {
            $metadata->mapField([
                'fieldName' => $fieldName,
                'type' => 'string',
                'nullable' => true,
            ]);

            return;
        }

        if ($fieldMapping instanceof IntegerMapping) {
            $metadata->mapField([
                'fieldName' => $fieldName,
                'type' => 'long',
                'nullable' => true,
            ]);

            return;
        }

        if ($fieldMapping instanceof DateTimeMapping) {
            $metadata->mapField([
                'fieldName' => $fieldName,
                'type' => 'date',
                'nullable' => true,
            ]);

            return;
        }

        if ($fieldMapping instanceof ReferenceMapping) {
            $metadata->mapManyToOne([
                'fieldName' => $fieldName,
                'strategy' => 'hard',

                'nullable' => true,
                'cascade' => ClassMetadata::CASCADE_ALL,
            ]);

            return;
        }

        throw new \RuntimeException(sprintf(
            'Do not know how to map field of type "%s"',
            get_class($fieldMapping)
        ));
    }
}
