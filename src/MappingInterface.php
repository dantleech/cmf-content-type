<?php

namespace Psi\Component\ContentType;

/**
 * Currently a marker interface for mappings.
 *
 * In the future mappings may define options.
 */
interface MappingInterface
{
    public function getDefaultOptions(): array;
}
