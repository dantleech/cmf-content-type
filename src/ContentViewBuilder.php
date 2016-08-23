<?php

/*
 * This file is part of the Symfony CMF package.
 *
 * (c) 2011-2016 Symfony CMF
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Symfony\Cmf\Component\ContentType;

use Metadata\MetadataFactory;
use Metadata\NullMetadata;
use Symfony\Cmf\Component\ContentType\OptionsResolver\FieldOptionsResolver;

class ContentViewBuilder
{
    private $metadataFactory;
    private $fieldRegistry;
    private $viewRegistry;

    public function __construct(
        MetadataFactory $metadataFactory,
        FieldRegistry $fieldRegistry,
        ViewRegistry $viewRegistry
    ) {
        $this->metadataFactory = $metadataFactory;
        $this->fieldRegistry = $fieldRegistry;
        $this->viewRegistry = $viewRegistry;
    }

    public function build($content)
    {
        $classFqn = get_class($content);
        $metadata = $this->metadataFactory->getMetadataForClass($classFqn);

        if ($metadata instanceof NullMetadata) {
            throw new \RuntimeException(sprintf(
                'Class "%s" is not mapped',
                $classFqn
            ));
        }

        $contentView = new ContentView();

        foreach ($metadata->getPropertyMetadata() as $propertyMetadata) {
            $subView = new ContentView();
            $field = $this->fieldRegistry->get($propertyMetadata->getType());
            $view = $this->viewRegistry->get($field->getViewType());

            $resolver = new FieldOptionsResolver();
            $field->configureOptions($resolver);
            $options = $resolver->resolveViewOptions($propertyMetadata->getOptions());
            $value = $propertyMetadata->getValue($content);
            $view->buildView($this, $subView, $value, $options);

            $contentView[$propertyMetadata->getName()] = $subView;
        }

        return $contentView;
    }
}
