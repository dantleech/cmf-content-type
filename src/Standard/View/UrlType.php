<?php

declare(strict_types=1);

namespace Psi\Component\ContentType\Standard\View;

use Psi\Component\View\TypeInterface;
use Psi\Component\View\View;
use Psi\Component\View\ViewFactory;
use Psi\Component\View\ViewInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class UrlType implements TypeInterface
{
    public function createView(ViewFactory $factory, $data, array $options): ViewInterface
    {
        if (null !== $data && !is_string($data)) {
            throw new \InvalidArgumentException(sprintf(
                'URL view only accepts string values! Got "%s"',
                is_object($data) ? get_class($data) : gettype($data)
            ));
        }

        return new UrlView($data);
    }

    public function configureOptions(OptionsResolver $options)
    {
    }
}
