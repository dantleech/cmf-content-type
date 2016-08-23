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
use Doctrine\Common\Persistence\Event\LifecycleEventArgs;
use Doctrine\Common\Util\ClassUtils;
use Doctrine\ODM\PHPCR\Event;
use Metadata\MetadataFactory;
use Metadata\MetadataFactoryInterface;
use Symfony\Cmf\Component\ContentType\FieldRegistry;
use Symfony\Cmf\Component\ContentType\Storage\Doctrine\PhpcrOdm\PropertyEncoder;

class CollectionSubscriber implements EventSubscriber
{
    private $metadataFactory;
    private $fieldRegistry;
    private $encoder;

    public function __construct(
        MetadataFactoryInterface $metadataFactory,
        FieldRegistry $fieldRegistry,
        PropertyEncoder $encoder
    ) {
        $this->metadataFactory = $metadataFactory;
        $this->fieldRegistry = $fieldRegistry;
        $this->encoder = $encoder;
    }

    public function getSubscribedEvents()
    {
        return [
            Event::prePersist,
        ];
    }

    public function prePersist(LifecycleEventArgs $args)
    {
        $document = $args->getObject();
        $metadataFactory = $args->getObjectManager()->getMetadataFactory();
        $odmMetadata = $metadataFactory->getMetadataFor(ClassUtils::getRealClass(get_class($document)));

        if (null === $ctMetadata = $this->metadataFactory->getMetadataForClass($odmMetadata->getName())) {
            return;
        }

        $documentId = $odmMetadata->getIdentifierValue($document);

        foreach ($odmMetadata->childrenMappings as $childrenField) {

            // if the children field is not managed by the CT component,
            // continue
            if (!isset($ctMetadata->propertyMetadata[$childrenField])) {
                continue;
            }

            $children = $odmMetadata->getFieldValue($document, $childrenField);

            if (!$children) {
                continue;
            }

            // note that we do not preserve array keys. PHPCR ODM will return a
            // children collection using the PHPCR property names as keys, so
            // we currently have no control over how these keys populated.
            $index = 0;
            foreach ($children as $child) {
                $childMetadata = $metadataFactory->getMetadataFor(ClassUtils::getRealClass(get_class($child)));
                $childMetadata->setIdentifierValue($child, sprintf(
                    '%s/%s',
                    $documentId,
                    $this->encoder->encode($childrenField, $index)
                ));
                $index++;
            }
        }
    }
}
