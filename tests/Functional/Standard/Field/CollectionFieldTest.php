<?php

namespace Psi\Component\ContentType\Tests\Functional\Standard\Field;

class CollectionFieldTest extends FieldTestCase
{
    private $registry;

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
