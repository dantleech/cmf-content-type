<?php

/*
 * This file is part of the Symfony CMF package.
 *
 * (c) 2011-2016 Symfony CMF
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Symfony\Cmf\Component\ContentType\Form\Extension\Type;

use Symfony\Cmf\Component\ContentType\FieldRegistry;
use Symfony\Cmf\Component\ContentType\Metadata\ClassMetadata;
use Symfony\Cmf\Component\ContentType\OptionsResolver\FieldOptionsResolver;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

/**
 * Surrogate type for virtual user "content" form types.
 *
 * For example, if the user manages an "Article" class with the content type
 * system, this class will act as its form type.
 */
class SurrogateType extends AbstractType
{
    /**
     * @var string
     */
    private $contentFqn;

    /**
     * @var ClassMetadata
     */
    private $classMetadata;

    /**
     * @var FieldRegistry
     */
    private $fieldRegistry;

    public function __construct(
        $contentFqn,
        FieldRegistry $fieldRegistry,
        ClassMetadata $classMetadata
    ) {
        $this->contentFqn = $contentFqn;
        $this->fieldRegistry = $fieldRegistry;
        $this->classMetadata = $classMetadata;
    }

    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        foreach ($this->classMetadata->getPropertyMetadata() as $propertyMetadata) {
            $field = $this->fieldRegistry->get($propertyMetadata->getType());
            $formOptions = $propertyMetadata->getOptions();

            $resolver = new FieldOptionsResolver();
            $field->configureOptions($resolver);
            $formOptions = $resolver->resolveFormOptions($formOptions);

            $builder->add(
                $propertyMetadata->getName(),
                $field->getFormType(),
                $formOptions
            );
        }
    }

    /**
     * {@inheritdoc}
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefault('data_class', $this->contentFqn);
    }
}
