<?php

declare(strict_types=1);

namespace Psi\Component\ContentType\Standard\Field;

use Psi\Component\ContentType\FieldInterface;
use Psi\Component\ContentType\OptionsResolver\FieldOptionsResolver;
use Psi\Component\ContentType\Standard\Storage;
use Psi\Component\ContentType\Standard\View;
use Symfony\Component\Form\Extension\Core\Type as Form;

class DateTimeField implements FieldInterface
{
    public function getViewType(): string
    {
        return View\DateTimeType::class;
    }

    public function getFormType(): string
    {
        return Form\DateTimeType::class;
    }

    public function getStorageType(): string
    {
        return Storage\DateTimeType::class;
    }

    public function configureOptions(FieldOptionsResolver $options)
    {
    }
}
