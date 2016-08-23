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
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CollectionType;
use Symfony\Component\OptionsResolver\Options;
use Symfony\Component\OptionsResolver\OptionsResolver;

class FieldCollectionType extends AbstractType
{
    private $registry;

    public function __construct(FieldRegistry $registry)
    {
        $this->registry = $registry;
    }

    public function configureOptions(OptionsResolver $options)
    {
        $options->setNormalizer('entry_type', function (Options $options, $value) {
            // get the field type
            $field = $this->registry->get($value);

            return $field->getFormType();
        });
    }

    public function getParent()
    {
        return CollectionType::class;
    }
}
