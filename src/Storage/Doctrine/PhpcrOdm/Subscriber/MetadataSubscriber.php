<?php

/*
 * This file is part of the Symfony CMF package.
 *
 * (c) 2011-2016 Symfony CMF
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Symfony\Cmf\Component\ContentType\Storage\Doctrine\PhpcrOdm\Subscriber;

use Doctrine\Common\EventSubscriber;
use Doctrine\Common\Persistence\Event\LoadClassMetadataEventArgs;
use Doctrine\ODM\PHPCR\Event;
use Metadata\MetadataFactory;
use Symfony\Cmf\Component\ContentType\FieldRegistry;
use Symfony\Cmf\Component\ContentType\MappingResolver;
use Symfony\Cmf\Component\ContentType\Storage\Doctrine\PhpcrOdm\FieldMapper;

class MetadataSubscriber implements EventSubscriber
{
    private $metadataFactory;
    private $fieldRegistry;
    private $mappingResolver;
    private $mapper;

    public function __construct(
        MetadataFactory $metadataFactory,
        FieldRegistry $fieldRegistry,
        MappingResolver $mappingResolver,
        FieldMapper $mapper
    ) {
        $this->metadataFactory = $metadataFactory;
        $this->fieldRegistry = $fieldRegistry;
        $this->mappingResolver = $mappingResolver;
        $this->mapper = $mapper;
    }

    public function getSubscribedEvents()
    {
        return [
            Event::loadClassMetadata,
        ];
    }

    public function loadClassMetadata(LoadClassMetadataEventArgs $args)
    {
        $metadata = $args->getClassMetadata();
        $fieldMapper = $this->mapper;

        if (null === $metadata = $this->metadataFactory->getMetadataForClass($metadata->getName())) {
            return;
        }

        $odmMetadata = $args->getClassMetadata();

        foreach ($metadata->getPropertyMetadata() as $property) {
            $field = $this->fieldRegistry->get($property->getType());
            $mapping = $this->mappingResolver->resolveMapping($field);
            $fieldMapper($property->getName(), $mapping, $odmMetadata);
        }
    }
}
