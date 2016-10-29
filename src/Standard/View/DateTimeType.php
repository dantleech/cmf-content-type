<?php

declare(strict_types=1);

namespace Psi\Component\ContentType\Standard\View;

use Psi\Component\ContentType\View\TypeInterface;
use Psi\Component\ContentType\View\View;
use Psi\Component\ContentType\View\ViewFactory;
use Psi\Component\ContentType\View\ViewInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class DateTimeType implements TypeInterface
{
    public function createView(ViewFactory $factory, $data, array $options): ViewInterface
    {
        if (null !== $data && !$data instanceof \DateTime) {
            throw new \InvalidArgumentException(sprintf(
                'DateTime view only accepts \DateTime objects, got "%s"',
                is_object($data) ? get_class($data) : gettype($data)
            ));
        }

        return new DateTimeView($data, $options['tag']);
    }

    public function configureOptions(OptionsResolver $options)
    {
        $options->setDefault('tag', null);
    }
}
