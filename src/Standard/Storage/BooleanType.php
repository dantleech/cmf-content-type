<?php

namespace Psi\Component\ContentType\Standard\Storage;

use Psi\Component\ContentType\Storage\TypeInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

final class BooleanType implements TypeInterface
{
    public function configureOptions(OptionsResolver $options)
    {
    }
}
