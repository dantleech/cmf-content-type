<?php

namespace Psi\Component\ContentType\Tests\Functional\Standard\Field;

class ChoiceFieldTest extends FieldTestCase
{
    protected function getFieldName(): string
    {
        return 'choice';
    }

    protected function getDefaultData()
    {
        return 'one';
    }

    public function provideValidConfigs(): array
    {
        return [
            [
                [],
            ],
        ];
    }
}
