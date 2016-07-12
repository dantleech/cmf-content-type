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
use Symfony\Cmf\Component\ContentType\View\ScalarView;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Cmf\Component\ContentType\Tests\Functional\Example\Form\Type\ImageType;

class ImageField implements FieldInterface
{
    public function getViewType()
    {
        return 'image';
    }

    public function getFormType()
    {
        return ImageType::class;
    }

    public function configureOptions(OptionsResolver $options)
    {
        $options->setDefault('reosurce_path', '/');
    }
}
