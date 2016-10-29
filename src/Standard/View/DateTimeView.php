<?php

declare(strict_types=1);

namespace Psi\Component\ContentType\Standard\View;

use Psi\Component\ContentType\View\ViewInterface;

class DateTimeView implements ViewInterface
{
    private $value;
    private $tag;

    public function __construct($value, string $tag = null)
    {
        $this->value = $value;
        $this->tag = $tag;
    }

    public function getTag()
    {
        return $this->tag;
    }

    public function getValue()
    {
        return $this->value;
    }
}
