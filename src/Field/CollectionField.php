<?php

/*
 * This file is part of the Symfony CMF package.
 *
 * (c) 2011-2016 Symfony CMF
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Symfony\Cmf\Component\ContentType\Field;

use Symfony\Cmf\Component\ContentType\FieldInterface;
use Symfony\Cmf\Component\ContentType\Form\Extension\Type\FieldCollectionType;
use Symfony\Cmf\Component\ContentType\MappingBuilder;
use Symfony\Component\OptionsResolver\OptionsResolver;

class CollectionField implements FieldInterface
{
    public function getViewType()
    {
        return CollectionView::class;
    }

    public function getFormType()
    {
        return FieldCollectionType::class;
    }

    public function getMapping(MappingBuilder $builder)
    {
        return $builder->collection();
    }

    public function configureOptions(OptionsResolver $options)
    {
        $options->setRequired([
            'entry_type',
            'allow_add',
        ]);
    }
}
