<?php

namespace Psi\Component\ContentType\Tests\Unit;

use Psi\Component\ContentType\ConfiguredMapping;
use Psi\Component\ContentType\MappingInterface;
use Psi\Component\ContentType\MappingRegistry;

class MappingRegistryTest extends \PHPUnit_Framework_TestCase
{
    private $registry;

    public function setUp()
    {
        $this->mapping = $this->prophesize(MappingInterface::class);
        $this->registry = new MappingRegistry();
    }

    /**
     * @expectedException \BadMethodCallException
     */
    public function testDisabledGet()
    {
        $this->registry->get('foobar');
    }

    /**
     * It should return a configured mapping.
     */
    public function testGetConfigured()
    {
        $this->registry->register('foobar', $this->mapping->reveal());
        $this->mapping->getDefaultOptions()->willReturn(['one' => null]);

        $mapping = $this->registry->getConfiguredMapping('foobar', ['one' => 'two']);
        $this->assertInstanceOf(ConfiguredMapping::class, $mapping);
        $this->assertSame($this->mapping->reveal(), $mapping->getMapping());
        $this->assertEquals('two', $mapping->getOption('one'));
    }

    /**
     * It should throw an exception if unknown options are given.
     *
     * @expectedExceptionMessage Unknown option(s) "five", available options: "one"
     * @expectedException \InvalidArgumentException
     */
    public function testUnknownOptions()
    {
        $this->registry->register('foobar', $this->mapping->reveal());
        $this->mapping->getDefaultOptions()->willReturn(['one' => null]);

        $this->registry->getConfiguredMapping('foobar', ['five' => 'two']);
    }
}
