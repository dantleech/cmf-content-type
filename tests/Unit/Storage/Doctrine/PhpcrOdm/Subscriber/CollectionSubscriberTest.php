<?php

/*
 * This file is part of the Symfony CMF package.
 *
 * (c) 2011-2016 Symfony CMF
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Symfony\Cmf\Component\ContentType\Tests\Unit\Storage\Doctrine\PhpcrOdm\Subscriber;

use Doctrine\Common\Persistence\Event\LifecycleEventArgs;
use Doctrine\ODM\PHPCR\DocumentManagerInterface;
use Doctrine\ODM\PHPCR\Mapping\ClassMetadata as OdmClassMetadata;
use Doctrine\ODM\PHPCR\Mapping\ClassMetadataFactory;
use Metadata\MetadataFactoryInterface;
use Prophecy\Argument;
use Symfony\Cmf\Component\ContentType\FieldRegistry;
use Symfony\Cmf\Component\ContentType\Metadata\ClassMetadata;
use Symfony\Cmf\Component\ContentType\Metadata\PropertyMetadata;
use Symfony\Cmf\Component\ContentType\Storage\Doctrine\PhpcrOdm\PropertyEncoder;
use Symfony\Cmf\Component\ContentType\Storage\Doctrine\PhpcrOdm\Subscriber\CollectionSubscriber;

class CollectionSubscriberTest extends \PHPUnit_Framework_TestCase
{
    private $metadataFactory;
    private $fieldRegistry;
    private $propertyEncoder;
    private $subscriber;
    private $object;
    private $odmMetadataFactory;

    public function setUp()
    {
        $this->metadataFactory = $this->prophesize(MetadataFactoryInterface::class);
        $this->fieldRegistry = $this->prophesize(FieldRegistry::class);
        $this->propertyEncoder = $this->prophesize(PropertyEncoder::class);

        $this->subscriber = new CollectionSubscriber(
            $this->metadataFactory->reveal(),
            $this->fieldRegistry->reveal(),
            $this->propertyEncoder->reveal()
        );

        $this->object = new \stdClass();
        $this->odmMetadataFactory = $this->prophesize(ClassMetadataFactory::class);
        $this->odmMetadata = $this->prophesize(OdmClassMetadata::class);
        $this->event = $this->prophesize(LifecycleEventArgs::class);
        $this->metadata = $this->prophesize(ClassMetadata::class);
        $this->documentManager = $this->prophesize(DocumentManagerInterface::class);

        $this->event->getObjectManager()->willReturn($this->documentManager->reveal());
        $this->event->getObject()->willReturn($this->object);
        $this->documentManager->getMetadataFactory()->willReturn($this->odmMetadataFactory->reveal());
        $this->propertyMetadata1 = $this->prophesize(PropertyMetadata::class);
    }

    /**
     * It should return early on prePersist if no metadata was registered for the ODM object in the content-type metadata factory.
     */
    public function testNoMetadataContentTypeMetadata()
    {
        $this->metadataFactory->getMetadataForClass(\stdClass::class)->willReturn(null);
        $this->odmMetadataFactory->getMetadataFor(\stdClass::class)->willReturn(
            $this->odmMetadata->reveal()
        );
        $this->odmMetadata->getName()->willReturn(\stdClass::class);

        $this->subscriber->prePersist($this->event->reveal());
    }

    /**
     * It should not take action on non-children mappings.
     */
    public function testNoActionNonChildrenMappings()
    {
        $idValue = '/path/to/document';
        $this->metadataFactory->getMetadataForClass(\stdClass::class)->willReturn($this->metadata->reveal());

        $this->odmMetadataFactory->getMetadataFor(\stdClass::class)->willReturn($this->odmMetadata->reveal());
        $this->odmMetadata->getName()->willReturn(\stdClass::class);
        $this->odmMetadata->getIdentifierValue($this->object)->willReturn($idValue);
        $this->odmMetadata->childrenMappings = [
            'mapping1',
        ];

        $this->odmMetadata->getFieldValue(Argument::cetera())->shouldNotBeCalled();

        $this->subscriber->prePersist($this->event->reveal());
    }

    /**
     * It should set the paths for the children.
     */
    public function testSetChildPaths()
    {
        $idValue = '/path/to/document';
        $this->metadataFactory->getMetadataForClass(\stdClass::class)->willReturn($this->metadata->reveal());

        $this->odmMetadataFactory->getMetadataFor(\stdClass::class)->willReturn($this->odmMetadata->reveal());
        $this->odmMetadata->getName()->willReturn(\stdClass::class);
        $this->odmMetadata->getIdentifierValue($this->object)->willReturn($idValue);
        $this->odmMetadata->childrenMappings = [
            'mapping1',
        ];
        $this->metadata->propertyMetadata = ['mapping1' => $this->propertyMetadata1->reveal()];
        $this->odmMetadata->getFieldValue($this->object, 'mapping1')->willReturn([
            $child1 = new \stdClass(),
        ]);

        $this->odmMetadata->setIdentifierValue($child1, sprintf(
            '%s/%s',
            $idValue, 'foo'
        ))->shouldBeCalled();
        $this->propertyEncoder->encode('mapping1', 0)->willReturn('foo');

        $this->subscriber->prePersist($this->event->reveal());
    }
}
