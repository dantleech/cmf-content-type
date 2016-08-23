<?php

/*
 * This file is part of the Symfony CMF package.
 *
 * (c) 2011-2016 Symfony CMF
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Symfony\Cmf\Component\ContentType\Tests\Unit\Metadata\Driver;

use Symfony\Cmf\Component\ContentType\Metadata\ClassMetadata;
use Symfony\Cmf\Component\ContentType\Metadata\Driver\ArrayDriver;

class ArrayDriverTest extends \PHPUnit_Framework_TestCase
{
    /**
     * It should return NULL if the class is not known.
     */
    public function testClassNotKnown()
    {
        $reflection = new \ReflectionClass('stdClass');
        $driver = $this->createDriver([]);
        $metadata = $driver->loadMetadataForClass($reflection);

        $this->assertNull($metadata);
    }

    /**
     * It should return class metadata.
     */
    public function testLoadClassMetadata()
    {
        $reflection = new \ReflectionClass(TestContent::class);
        $driver = $this->createDriver([
            TestContent::class => [
                'driver' => 'doctrine_orm',
                'properties' => [
                    'title' => [
                        'type' => 'Class\Fqn\TextField',
                        'options' => [
                            'option_1' => 100,
                        ],
                    ],
                    'image' => [
                        'type' => 'Class\Fqn\ImageField',
                    ],
                ],
            ],
        ]);

        $classMetadata = $driver->loadMetadataForClass($reflection);
        $this->assertInstanceOf(ClassMetadata::class, $classMetadata);
        $this->assertEquals(TestContent::class, $classMetadata->getName());
        $this->assertEquals('doctrine_orm', $classMetadata->getDriver());

        $properties = $classMetadata->getPropertyMetadata();
        $this->assertCount(2, $properties);

        $property1 = current($properties);
        $this->assertEquals('Class\Fqn\TextField', $property1->getType());
        $this->assertEquals(['option_1' => 100], $property1->getOptions());

        $property2 = next($properties);
        $this->assertEquals('Class\Fqn\ImageField', $property2->getType());
    }

    /**
     * If the form is compound, it should add the serialize transformer.
     *
     * NOTE: We will be a strategy in the future to enable things such as Doctrine Embedables.
     */
    public function testCompound()
    {
        $this->markTestIncomplete('TODO');
    }

    private function createDriver(array $config)
    {
        return new ArrayDriver($config);
    }
}

class TestContent
{
    private $title;
    private $image;
}
