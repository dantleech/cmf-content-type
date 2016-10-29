<?php

namespace Psi\Component\ContentType\Tests\Functional\Standard\Field;

use Psi\Component\ContentType\Tests\Functional\BaseTestCase;
use Psi\Component\ContentType\View\ViewInterface;
use Symfony\Component\Form\Form;
use Symfony\Component\OptionsResolver\OptionsResolver;

abstract class FieldTestCase extends BaseTestCase
{
    private $fieldLoader;
    private $viewFactory;
    private $formFactory;
    private $storageRegistry;

    public function setUp()
    {
        $container = $this->getContainer([
            'mapping' => [],
        ]);
        $this->fieldLoader = $container->get('psi_content_type.field_loader');
        $this->viewFactory = $container->get('psi_content_type.view.factory');
        $this->formFactory = $container->get('symfony.form_factory');
        $this->storageRegistry = $container->get('psi_content_type.registry.type');
    }

    abstract protected function getFieldName(): string;

    abstract public function provideValidConfigs(): array;

    abstract protected function getDefaultData();

    protected function getField(array $config)
    {
        return $this->fieldLoader->load(
            $this->getFieldName(),
            $config
        );
    }

    /**
     * The view can be built.
     *
     * @dataProvider provideValidConfigs
     */
    public function testBuildView(array $config)
    {
        $field = $this->getField($config);
        $viewOptions = $field->getViewOptions();
        $view = $this->viewFactory->create(
            $field->getViewType(),
            $this->getDefaultData(),
            $viewOptions
        );
        $this->assertInstanceOf(ViewInterface::class, $view);
    }

    /**
     * The view can be built.
     *
     * @dataProvider provideValidConfigs
     */
    public function testBuildForm(array $config)
    {
        $field = $this->getField($config);
        $formOptions = $field->getFormOptions();
        $form = $this->formFactory->create(
            $field->getFormType(),
            $this->getDefaultData(),
            $formOptions
        );
        $this->assertInstanceOf(Form::class, $form);
    }

    /**
     * The storage can be configured.
     *
     * @dataProvider provideValidConfigs
     */
    public function testBuildStorage(array $config)
    {
        $field = $this->getField($config);
        $storageOptions = $field->getStorageOptions();
        $resolver = new OptionsResolver();

        $type = $this->storageRegistry->get($field->getStorageType());
        $type->configureOptions($resolver);
        $resolver->resolve($storageOptions);
    }
}
