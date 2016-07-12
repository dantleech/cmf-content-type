<?php

/*
 * This file is part of the Symfony CMF package.
 *
 * (c) 2011-2016 Symfony CMF
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Symfony\Cmf\Component\ContentType\Form;

use Metadata\MetadataFactory;
use Metadata\NullMetadata;
use Symfony\Cmf\Component\ContentType\FieldRegistry;
use Symfony\Component\Form\Extension\Core\Type\FormType;
use Symfony\Component\Form\FormFactoryInterface;
use Symfony\Component\Form\CallbackTransformer;

class FormBuilder
{
    private $metadataFactory;
    private $formFactory;
    private $fieldRegistry;

    public function __construct(
        MetadataFactory $metadataFactory,
        FormFactoryInterface $formFactory,
        FieldRegistry $fieldRegistry
    ) {
        $this->metadataFactory = $metadataFactory;
        $this->formFactory = $formFactory;
        $this->fieldRegistry = $fieldRegistry;
    }

    public function buildFormForContent($content)
    {
        $classFqn = get_class($content);
        $metadata = $this->metadataFactory->getMetadataForClass($classFqn);

        if ($metadata instanceof NullMetadata) {
            throw new \RuntimeException(sprintf(
                'Class "%s" is not mapped',
                $classFqn
            ));
        }

        $builder = $this->formFactory->createBuilder(FormType::class, $content);
        $compoundFields = [];

        foreach ($metadata->getPropertyMetadata() as $propertyMetadata) {
            $field = $this->fieldRegistry->get($propertyMetadata->getType());
            $formOptions = $propertyMetadata->getFormOptions();

            $builder->add(
                $propertyMetadata->getName(),
                $field->getFormType(),
                $formOptions
            );
            $child = $builder->get($propertyMetadata->getName());
            if ($child->getCompound()) {
                $compoundFields[] = $propertyMetadata->getName();

                $child->addModelTransformer(new CallbackTransformer(
                    function ($value) use ($compoundFields) {
                        if (null === $value) {
                            return $value;
                        }

                        return unserialize($value);
                    },
                    function ($value) {
                        return serialize($value);
                    }
                ));
            }
        }

        return $builder;
    }
}
