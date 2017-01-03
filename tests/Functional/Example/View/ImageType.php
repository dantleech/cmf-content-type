<?php

namespace Psi\Component\ContentType\Tests\Functional\Example\View;

use Psi\Component\View\TypeInterface;
use Psi\Component\View\ViewFactory;
use Psi\Component\View\ViewInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class ImageType implements TypeInterface
{
    public function createView(ViewFactory $factory, $data, array $options): ViewInterface
    {
        return new ImageView($data);
    }

    public function configureOptions(OptionsResolver $options)
    {
        $options->setDefaults([
            'path' => '/path',
            'repository' => 'foo',
        ]);
    }
}
