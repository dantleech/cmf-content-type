<?php

declare(strict_types=1);

namespace Psi\Component\ContentType\Standard\View;

use Psi\Component\ContentType\FieldLoader;
use Psi\Component\ContentType\FieldOptions;
use Psi\Component\View\TypeInterface;
use Psi\Component\View\ViewFactory;
use Psi\Component\View\ViewInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class CollectionType implements TypeInterface
{
    private $fieldLoader;

    public function __construct(FieldLoader $fieldLoader)
    {
        $this->fieldLoader = $fieldLoader;
    }

    public function createView(ViewFactory $factory, $data, array $options): ViewInterface
    {
        if (!is_array($data) && !$data instanceof \Traversable) {
            throw new \InvalidArgumentException(sprintf(
                'Data must be traversable or an array, got: "%s"',
                is_object($data) ? get_class($data) : gettype($data)
            ));
        }

        if (is_array($data)) {
            $data = new \ArrayIterator($data);
        }

        $field = $this->fieldLoader->load(
            $options['field_type'],
            FieldOptions::create($options['field_options'])
        );

        return new CollectionView(
            $factory,
            $data,
            $field->getViewType(),
            $field->getViewOptions()
        );
    }

    public function configureOptions(OptionsResolver $options)
    {
        $options->setDefaults([
            'field_options' => [],
        ]);

        $options->setRequired([
            'field_type',
        ]);

        $options->setAllowedTypes('field_type', ['string']);
        $options->setAllowedTypes('field_options', ['array']);
    }
}
