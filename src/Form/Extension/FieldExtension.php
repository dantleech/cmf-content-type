<?php

/*
 * This file is part of the Symfony CMF package.
 *
 * (c) 2011-2016 Symfony CMF
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Symfony\Cmf\Component\ContentType\Form\Extension;

use Metadata\MetadataFactoryInterface;
use Symfony\Cmf\Component\ContentType\FieldRegistry;
use Symfony\Cmf\Component\ContentType\Form\Extension\Type\FieldCollectionType;
use Symfony\Cmf\Component\ContentType\Form\Extension\Type\SurrogateType;
use Symfony\Component\Form\AbstractExtension;

/**
 * Form type extension to provide form types for user content-type-managed
 * classes in addition to content-type component specific types.
 */
class FieldExtension extends AbstractExtension
{
    /**
     * @var MetadataFactoryInterface
     */
    private $metadataFactory;

    /**
     * @var FieldRegistry
     */
    private $fieldRegistry;

    public function __construct(
        MetadataFactoryInterface $metadataFactory,
        FieldRegistry $fieldRegistry
    ) {
        $this->metadataFactory = $metadataFactory;
        $this->fieldRegistry = $fieldRegistry;
    }

    /**
     * {@inheritdoc}
     */
    public function hasType($type)
    {
        if ($this->metadataFactory->getMetadataForClass($type)) {
            return true;
        }

        return parent::hasType($type);
    }

    /**
     * {@inheritdoc}
     */
    public function loadTypes()
    {
        return [
            new FieldCollectionType($this->fieldRegistry),
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function getType($type)
    {
        if (parent::hasType($type)) {
            return parent::getType($type);
        }

        $metadata = $this->metadataFactory->getMetadataForClass($type);

        if (!$metadata) {
            throw new \InvalidArgumentException(sprintf(
                'The type "%s" cannot be loaded by this extension',
                $type
            ));
        }

        $surrogateType = new SurrogateType(
            $type,
            $this->fieldRegistry,
            $metadata
        );

        return $surrogateType;
    }
}
