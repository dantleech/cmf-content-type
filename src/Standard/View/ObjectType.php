<?php

declare(strict_types=1);

namespace Psi\Component\ContentType\Standard\View;

use Metadata\MetadataFactory;
use Metadata\NullMetadata;
use Psi\Component\ContentType\FieldLoader;
use Psi\Component\View\TypeInterface;
use Psi\Component\View\ViewFactory;
use Psi\Component\View\ViewInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class ObjectType implements TypeInterface
{
    private $metadataFactory;
    private $fieldLoader;

    public function __construct(
        MetadataFactory $metadataFactory,
        FieldLoader $fieldLoader
    ) {
        $this->metadataFactory = $metadataFactory;
        $this->fieldLoader = $fieldLoader;
    }

    public function createView(ViewFactory $factory, $data, array $options): ViewInterface
    {
        $classFqn = get_class($data);
        $metadata = $this->metadataFactory->getMetadataForClass($classFqn);

        if (null === $metadata || $metadata instanceof NullMetadata) {
            throw new \RuntimeException(sprintf(
                'Class "%s" is not mapped',
                $classFqn
            ));
        }

        $map = [];
        foreach ($metadata->getPropertyMetadata() as $propertyMetadata) {
            $map[$propertyMetadata->getName()] = function () use ($propertyMetadata, $factory, $data) {
                $field = $this->fieldLoader->load(
                    $propertyMetadata->getType(),
                    $propertyMetadata->getOptions()
                );

                return $factory->create(
                    $field->getViewType(),
                    $propertyMetadata->getValue($data),
                    $field->getViewOptions()
                );
            };
        }

        return new ObjectView($map);
    }

    public function configureOptions(OptionsResolver $options)
    {
    }
}
