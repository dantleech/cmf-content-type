<?php

namespace Psi\Component\ContentType;

use Psi\Component\ContentType\Util\OptionsUtil;
use Sylius\Component\Registry\ServiceRegistry;

/**
 * Registry for mapping objects.
 */
class MappingRegistry extends ServiceRegistry
{
    public function __construct()
    {
        parent::__construct(
            MappingInterface::class,
            'mapping'
        );
    }

    /**
     * {@inheritdoc}
     */
    final public function get($name)
    {
        throw new \BadMethodCallException('Get is not supported for mapping registry. Use getConfiguredMapping instead.');
    }

    /**
     * Return a named configured mapping.
     */
    public function getConfiguredMapping($mappingName, array $options): ConfiguredMapping
    {
        $innerMapping = parent::get($mappingName);
        $options = OptionsUtil::resolve($innerMapping->getDefaultOptions(), $options);

        return new ConfiguredMapping($innerMapping, $options);
    }
}
