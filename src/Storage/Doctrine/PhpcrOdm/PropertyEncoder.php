<?php

/*
 * This file is part of the Symfony CMF package.
 *
 * (c) 2011-2016 Symfony CMF
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Symfony\Cmf\Component\ContentType\Storage\Doctrine\PhpcrOdm;

/**
 * Responsible for encoding PHPCR property names which are managed by the
 * content-type component.
 */
class PropertyEncoder
{
    /**
     * @param string $prefix
     * @param string $uri
     */
    public function __construct($prefix, $uri)
    {
        $this->prefix = $prefix;
        $this->uri = $uri;
    }

    /**
     * Encode a field name. If the field reprents a compound type then $key
     * should be passed to represent the key for the value of the compound
     * field.
     *
     * @param string $fieldName
     * @param string $suffix
     *
     * @return string
     */
    public function encode($fieldName, $key = null)
    {
        $propertyName = sprintf(
            '%s:%s',
            $this->prefix,
            $fieldName
        );

        if (null !== $key) {
            $propertyName .= '-' . $key;
        }


        return $propertyName;
    }

    /**
     * Return the PHPCR namespace prefix (alias).
     *
     * @return string
     */
    public function getPrefix()
    {
        return $this->prefix;
    }

    /**
     * Return the namespace URI.
     *
     * @return string
     */
    public function getUri()
    {
        return $this->uri;
    }
}
