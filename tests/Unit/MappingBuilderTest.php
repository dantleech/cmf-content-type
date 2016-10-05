<?php

namespace Psi\Component\ContentType\Tests\Unit;

use Psi\Component\ContentType\ConfiguredMapping;
use Psi\Component\ContentType\MappingBuilder;
use Psi\Component\ContentType\MappingBuilderCompound;
use Psi\Component\ContentType\MappingInterface;
use Psi\Component\ContentType\MappingRegistry;

class MappingBuilderTest extends \PHPUnit_Framework_TestCase
{
    private $registry;
    private $builder;

    public function setUp()
    {
        $this->registry = $this->prophesize(MappingRegistry::class);
        $this->builder = new MappingBuilder($this->registry->reveal());

        $this->mapping1 = $this->prophesize(MappingInterface::class);
        $this->configuredMapping1 = $this->prophesize(ConfiguredMapping::class);
        $this->configuredMapping1->getMapping()->willReturn($this->mapping1->reveal());
        $this->mapping1->getDefaultOptions()->willReturn([
            'foobar' => 'booboo',
        ]);
    }

    /**
     * It should provide a single scalar mapping.
     */
    public function testSingleScalar()
    {
        $this->registry->getConfiguredMapping('string', [])->willReturn($this->configuredMapping1->reveal());
        $mapping = $this->builder->single('string');

        $this->assertSame($this->mapping1->reveal(), $mapping->getMapping());
    }

    /**
     * It should allow to build a compound mapping.
     */
    public function testCompound()
    {
        $classFqn = 'My\Compound\DataTransferObject';
        $this->registry->getConfiguredMapping('string', [])->willReturn($this->configuredMapping1->reveal());
        $this->registry->getConfiguredMapping('string', ['foobar' => 'barfoo'])->willReturn($this->configuredMapping1->reveal());

        $mapping = $this->builder->compound($classFqn)
            ->map('foobar', 'string', ['foobar' => 'barfoo'])
            ->map('foobar_barfoo', 'string');

        $this->assertInstanceOf(MappingBuilderCompound::class, $mapping);
        $compound = $mapping->getCompound();

        $this->assertEquals($classFqn, $compound->getClass());
        $mapping = iterator_to_array($compound);

        $this->assertCount(2, $mapping);
        $this->assertSame($this->mapping1->reveal(), $mapping['foobar']->getMapping());
    }
}
