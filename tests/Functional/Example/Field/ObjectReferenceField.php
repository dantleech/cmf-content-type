<?php

namespace Psi\Component\ContentType\Tests\Functional\Example\Field;

use Psi\Component\ContentType\FieldInterface;
use Psi\Component\ContentType\OptionsResolver\FieldOptionsResolver;
use Psi\Component\ContentType\Standard\Storage\ReferenceType;
use Psi\Component\View\ScalarView;
use Symfony\Component\Form\Extension\Core\Type\TextType;

class ObjectReferenceField implements FieldInterface
{
    public function getViewType(): string
    {
        return ScalarView::class;
    }

    public function getFormType(): string
    {
        return TextType::class;
    }

    public function getStorageType(): string
    {
        return ReferenceType::class;
    }

    public function configureOptions(FieldOptionsResolver $options)
    {
        $options->setDefault('class', null);
        $options->setStorageMapper(function ($options, $shared) {
            return [
                'class' => $shared['class'],
            ];
        });
    }
}
