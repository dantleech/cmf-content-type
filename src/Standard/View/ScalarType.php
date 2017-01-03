<?php

declare(strict_types=1);

namespace Psi\Component\ContentType\Standard\View;

use Psi\Component\View\TypeInterface;
use Psi\Component\View\View;
use Psi\Component\View\ViewFactory;
use Psi\Component\View\ViewInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class ScalarType implements TypeInterface
{
    public function createView(ViewFactory $factory, $data, array $options): ViewInterface
    {
        if (null !== $data && !is_scalar($data)) {
            throw new \InvalidArgumentException(sprintf(
                'Scalar view only accepts scalar values! Got "%s"',
                is_object($data) ? get_class($data) : gettype($data)
            ));
        }

        return new ScalarView($data, $options['tag'], $options['raw']);
    }

    public function configureOptions(OptionsResolver $options)
    {
        $options->setDefault('tag', null);
        $options->setDefault('raw', false);
    }
}
