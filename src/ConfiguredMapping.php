<?php

declare(strict_types=1);

namespace Psi\Component\ContentType;

/**
 * Wrapper for mappings, containing options.
 */
class ConfiguredMapping
{
    private $mapping;
    private $options = [];

    public function __construct(MappingInterface $mapping, array $options)
    {
        $this->mapping = $mapping;
        $this->options = $options;
    }

    /**
     * Return the wrapped mapping.
     */
    public function getMapping(): MappingInterface
    {
        return $this->mapping;
    }

    /**
     * Return the named option.
     *
     * @return mixed
     */
    public function getOption($name)
    {
        if (!isset($this->options[$name])) {
            throw new \InvalidArgumentException(sprintf(
                'Unknown option "%s". Known options: "%s"',
                $name, implode('", "', array_keys($this->options))
            ));
        }

        return $this->options[$name];
    }
}
