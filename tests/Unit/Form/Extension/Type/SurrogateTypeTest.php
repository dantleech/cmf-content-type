<?php

/*
 * This file is part of the Symfony CMF package.
 *
 * (c) 2011-2016 Symfony CMF
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Symfony\Cmf\Component\ContentType\Tests\Unit\Form\Extension\Type;

use Prophecy\Argument;
use Symfony\Cmf\Component\ContentType\FieldInterface;
use Symfony\Cmf\Component\ContentType\FieldRegistry;
use Symfony\Cmf\Component\ContentType\Form\Extension\Type\SurrogateType;
use Symfony\Cmf\Component\ContentType\Metadata\ClassMetadata;
use Symfony\Cmf\Component\ContentType\Metadata\PropertyMetadata;
use Symfony\Cmf\Component\ContentType\OptionsResolver\FieldOptionsResolver;
use Symfony\Component\Form\FormBuilderInterface;

class SurrogateTypeTest extends \PHPUnit_Framework_TestCase
{
    private $classMetadata;
    private $fieldRegistry;
    private $type;
    private $field;

    public function setUp()
    {
        $this->classMetadata = $this->prophesize(ClassMetadata::class);
        $this->fieldRegistry = $this->prophesize(FieldRegistry::class);

        $this->type = new SurrogateType(
            'ContentFqn',
            $this->fieldRegistry->reveal(),
            $this->classMetadata->reveal()
        );

        $this->property1 = $this->prophesize(PropertyMetadata::class);
        $this->formBuilder = $this->prophesize(FormBuilderInterface::class);
        $this->field = $this->prophesize(FieldInterface::class);
    }

    /**
     * It should build a form.
     */
    public function testBuildForm()
    {
        $type = 'foo';
        $propertyName = 'propname';
        $formType = 'formtype';

        $this->classMetadata->getPropertyMetadata()->willReturn([
            $this->property1->reveal(),
        ]);
        $this->property1->getType()->willReturn($type);
        $this->property1->getOptions()->willReturn([
            'option' => 'value',
        ]);
        $this->property1->getName()->willReturn($propertyName);
        $this->field->getFormType()->willReturn($formType);
        $this->fieldRegistry->get($type)->willReturn($this->field);
        $this->field->configureOptions(Argument::type(FieldOptionsResolver::class))->will(function ($args) {
            $args[0]->setDefault('foo', 'bar');
            $args[0]->setDefault('option', 'eulav');
        });
        $this->formBuilder->add(
            $propertyName,
            $formType,
            [
                'option' => 'value',
                'foo' => 'bar',
            ]
        )->shouldBeCalled();

        $this->type->buildForm($this->formBuilder->reveal(), []);
    }
}
