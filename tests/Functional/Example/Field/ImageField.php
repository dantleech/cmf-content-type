<?php

/*
 * This file is part of the Symfony CMF package.
 *
 * (c) 2011-2016 Symfony CMF
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Symfony\Cmf\Component\ContentType\Tests\Functional\Example\Field;

use Symfony\Cmf\Component\ContentType\FieldInterface;
use Symfony\Cmf\Component\ContentType\MappingBuilder;
use Symfony\Cmf\Component\ContentType\Tests\Functional\Example\Form\Type\ImageType;
use Symfony\Cmf\Component\ContentType\Tests\Functional\Example\Model\Image;
use Symfony\Cmf\Component\ContentType\Tests\Functional\Example\View\ImageView;
use Symfony\Component\OptionsResolver\OptionsResolver;

class ImageField implements FieldInterface
{
    public function getViewType()
    {
        return ImageView::class;
    }

    public function getFormType()
    {
        return ImageType::class;
    }

    public function getMapping(MappingBuilder $builder)
    {
        return $builder->compound(Image::class)
          ->map('path', 'string', ['length' => 255])
          ->map('width', 'integer')
          ->map('height', 'integer')
          ->map('mimetype', 'string');
    }

    public function configureOptions(OptionsResolver $options)
    {
        $options->setDefault('repository', 'default');
        $options->setDefault('path', '/');

        $options->setViewOptions(['repository', 'path']);
    }
}
