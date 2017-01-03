<?php

namespace Psi\Component\ContentType\Tests\Functional\Example\View;

use Psi\Component\View\ViewInterface;

class ImageView implements ViewInterface
{
    private $value;

    public function __construct($value)
    {
        $this->value = $value;
    }

    public function getValue()
    {
        return $this->value;
    }

    public function getTemplate(): string
    {
        return 'psi/image';
    }
}
