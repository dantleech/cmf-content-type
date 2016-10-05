<?php

declare(strict_types=1);

namespace Psi\Component\ContentType\Mapping;

use Psi\Component\ContentType\MappingInterface;

class CollectionMapping implements MappingInterface
{
    public function getDefaultOptions(): array
    {
        return [];
    }
}
