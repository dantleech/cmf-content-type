<?php

/*
 * This file is part of the Symfony CMF package.
 *
 * (c) 2011-2016 Symfony CMF
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Symfony\Cmf\Component\ContentType\Storage\Doctrine\PhpcrOdm;

use Doctrine\ODM\PHPCR\Mapping\ClassMetadata;
use Symfony\Cmf\Component\ContentType\Mapping\CollectionMapping;
use Symfony\Cmf\Component\ContentType\Mapping\CompoundMapping;
use Symfony\Cmf\Component\ContentType\Mapping\IntegerMapping;
use Symfony\Cmf\Component\ContentType\Mapping\StringMapping;
use Symfony\Cmf\Component\ContentType\MappingInterface;

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
    public function __invoke($fieldName, MappingInterface $fieldMapping, ClassMetadata $metadata)
    {
        if ($fieldMapping instanceof CompoundMapping) {
            $metadata->mapChild([
                'fieldName' => $fieldName,
                'nodeName' => $this->encoder->encode($fieldName),
            ]);

            return;
        }

        if ($fieldMapping instanceof CollectionMapping) {
            $metadata->mapChildren([
                'fieldName' => $fieldName,
                'fetchDepth' => 1,
                'filter' => $this->encoder->encode($fieldName) . '-*',
                'cascade' => ClassMetadata::CASCADE_ALL,
            ]);

            return;
        }

        if ($fieldMapping instanceof StringMapping) {
            $metadata->mapField([
                'fieldName' => $fieldName,
                'type' => 'string',
            ]);

            return;
        }

        if ($fieldMapping instanceof IntegerMapping) {
            $metadata->mapField([
                'fieldName' => $fieldName,
                'type' => 'long',
            ]);

            return;
        }

        throw new \RuntimeException(sprintf(
            'Do not know how to map field of type "%s"',
            get_class($fieldMapping)
        ));
    }
}
