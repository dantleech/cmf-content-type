<?php

/*
 * This file is part of the Symfony CMF package.
 *
 * (c) 2011-2016 Symfony CMF
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Symfony\Cmf\Component\ContentType\Tests\Unit\Form\Extension;

use Metadata\MetadataFactoryInterface;
use Symfony\Cmf\Component\ContentType\FieldRegistry;
use Symfony\Cmf\Component\ContentType\Form\Extension\FieldExtension;
use Symfony\Cmf\Component\ContentType\Form\Extension\Type\FieldCollectionType;
use Symfony\Cmf\Component\ContentType\Form\Extension\Type\SurrogateType;
use Symfony\Cmf\Component\ContentType\Metadata\ClassMetadata;

class FieldExtensionTest extends \PHPUnit_Framework_TestCase
{
    private $metadataFactory;
    private $fieldRegistry;
    private $extension;

    public function setUp()
    {
        $this->metadataFactory = $this->prophesize(MetadataFactoryInterface::class);
        $this->fieldRegistry = $this->prophesize(FieldRegistry::class);

        $this->extension = new FieldExtension(
            $this->metadataFactory->reveal(),
            $this->fieldRegistry->reveal()
        );

        $this->metadata = $this->prophesize(ClassMetadata::class);
    }

    /**
     * It should return true if it hasType is passed an object registered with the metadata factory.
     */
    public function testHasTypeRegisteredMetadataFactory()
    {
        $this->metadataFactory->getMetadataForClass(\stdClass::class)
            ->willReturn($this->metadata->reveal());

        $result = $this->extension->hasType(\stdClass::class);

        $this->assertTrue($result);
    }

    /**
     * It should return true if a built-in type is given to hasType.
     */
    public function testHasBuiltInType()
    {
        $result = $this->extension->hasType(FieldCollectionType::class);

        $this->assertTrue($result);
    }

    /**
     * hasType should return false if an there is no metadata nor built-in type.
     */
    public function testHasNoType()
    {
        $result = $this->extension->hasType('foobar');

        $this->assertFalse($result);
    }

    /**
     * It should getType for built-in types.
     */
    public function testGetTypeBuiltIn()
    {
        $type = $this->extension->getType(FieldCollectionType::class);

        $this->assertInstanceOf(FieldCollectionType::class, $type);
    }

    /**
     * It should getType for a content entity.
     */
    public function testGetTypeContent()
    {
        $this->metadataFactory->getMetadataForClass(\stdClass::class)
            ->willReturn($this->metadata->reveal());

        $result = $this->extension->getType(\stdClass::class);

        $this->assertInstanceOf(SurrogateType::class, $result);
    }

    /**
     * It should throw an exception if an unknown type is requested from getType.
     *
     * @expectedException \InvalidArgumentException
     * @expectedExceptionMessage cannot be loaded
     */
    public function testGetTypeUnknown()
    {
        $this->metadataFactory->getMetadataForClass('foobar')->willReturn(null);

        $this->extension->getType('foobar');
    }
}
