<?php

declare(strict_types=1);

namespace Psi\Component\ContentType\Standard\View;

use Psi\Component\View\View;
use Psi\Component\View\ViewFactory;
use Psi\Component\View\ViewInterface;

class BooleanType extends ScalarType
{
    public function createView(ViewFactory $factory, $data, array $options): ViewInterface
    {
        if (null !== $data && !is_bool($data)) {
            throw new \InvalidArgumentException(sprintf(
                'Boolean view only accepts boolean values! Got "%s"',
                is_object($data) ? get_class($data) : gettype($data)
            ));
        }

        $data = $data ? 'true' : 'false';

        return new ScalarView($data, $options['tag'], $options['raw']);
    }
}
