<?php

/*
 * This file is part of the Symfony CMF package.
 *
 * (c) 2011-2016 Symfony CMF
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Symfony\Cmf\Component\ContentType\Metadata\Driver;

use Metadata\Driver\AdvancedDriverInterface;
use Symfony\Cmf\Component\ContentType\Metadata\ClassMetadata;
use Symfony\Cmf\Component\ContentType\Metadata\PropertyMetadata;

class ArrayDriver implements AdvancedDriverInterface
{
    /**
     * @var array
     */
    private $config;

    /**
     * @param array $config
     */
    public function __construct(array $config)
    {
        $this->config = $config;
    }

    /**
     * {@inheritdoc}
     */
    public function loadMetadataForClass(\ReflectionClass $class)
    {
        if (!isset($this->config[$class->getName()])) {
            return;
        }

        $config = array_merge([
            'driver' => null,
            'fields' => [],
        ], $this->config[$class->getName()]);

        $classMetadata = new ClassMetadata($class->getName(), $config['driver']);

        foreach ($config['properties'] as $fieldName => $fieldConfig) {
            $fieldConfig = array_merge([
                'type' => null,
                'options' => [],
            ], $fieldConfig);
            $propertyMetadata = new PropertyMetadata(
                $class->getName(),
                $fieldName,
                $fieldConfig['type'],
                $fieldConfig['options']
            );

            $classMetadata->addPropertyMetadata($propertyMetadata);
        }

        return $classMetadata;
    }

    /**
     * {@inheritdoc}
     */
    public function getAllClassNames()
    {
        return array_keys($this->config);
    }
}
