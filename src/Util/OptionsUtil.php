<?php

declare(strict_types=1);

namespace Psi\Component\ContentType\Util;

class OptionsUtil
{
    public static function resolve(array $defaults, array $options)
    {
        if ($diff = array_diff(array_keys($options), array_keys($defaults))) {
            throw new \InvalidArgumentException(sprintf(
                'Unknown option(s) "%s", available options: "%s"',
                implode('", "', $diff), implode('", "', array_keys($defaults))
            ));
        }

        $options = array_merge($defaults, $options);

        return $options;
    }
}
