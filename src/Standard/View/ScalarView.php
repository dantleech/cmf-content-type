<?php

declare(strict_types=1);

namespace Psi\Component\ContentType\Standard\View;

use Psi\Component\View\ViewInterface;

class ScalarView implements ViewInterface
{
    private $value;
    private $raw;
    private $tag;

    public function __construct($value, string $tag = null, bool $raw = false)
    {
        $this->value = $value;
        $this->tag = $tag;
        $this->raw = $raw;
    }

    public function getTag()
    {
        return $this->tag;
    }

    public function getValue()
    {
        return $this->value;
    }

    public function isRaw()
    {
        return $this->raw;
    }
}
