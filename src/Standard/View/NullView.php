<?php

declare(strict_types=1);

namespace Psi\Component\ContentType\Standard\View;

use Psi\Component\View\ViewInterface;

class NullView implements ViewInterface
{
    public function getTemplate(): string
    {
        return 'psi/null';
    }
}
