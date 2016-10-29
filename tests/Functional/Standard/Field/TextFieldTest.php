<?php

namespace Psi\Component\ContentType\Tests\Functional\Standard\Field;

class TextFieldTest extends FieldTestCase
{
    protected function getFieldName(): string
    {
        return 'text';
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
