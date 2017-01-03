<?php

declare(strict_types=1);

namespace Psi\Component\ContentType\Standard\View;

use Psi\Component\View\ViewInterface;

class DateTimeView implements ViewInterface
{
    private $dateTime;
    private $formatted;
    private $tag;

    public function __construct(string $formatted, \DateTime $dateTime, string $tag = null)
    {
        $this->formatted = $formatted;
        $this->dateTime = $dateTime;
        $this->tag = $tag;
    }

    public function getTag(): string
    {
        return $this->tag;
    }

    public function getFormatted(): string
    {
        return $this->formatted;
    }

    public function getDateTime(): \DateTime
    {
        return $this->dateTime;
    }
}
