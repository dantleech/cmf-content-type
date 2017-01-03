<?php

namespace Psi\Component\ContentType\Tests\Functional;

use Doctrine\Common\Annotations\AnnotationReader;
use Metadata\Driver\DriverChain;
use Metadata\MetadataFactory;
use Pimple\Container as PimpleContainer;
use Psi\Component\ContentType\FieldLoader;
use Psi\Component\ContentType\FieldRegistry;
use Psi\Component\ContentType\Form\Extension\FieldExtension;
use Psi\Component\ContentType\Metadata\Driver\AnnotationDriver as CTAnnotationDriver;
use Psi\Component\ContentType\Metadata\Driver\ArrayDriver;
use Psi\Component\ContentType\Standard\Field as Field;
use Psi\Component\ContentType\Standard\Storage as StdStorage;
use Psi\Component\ContentType\Standard\View as StdView;
use Psi\Component\ContentType\Storage;
use Psi\Component\ContentType\Tests\Functional\Example\Field\ImageField;
use Psi\Component\ContentType\Tests\Functional\Example\Field\ObjectReferenceField;
use Psi\Component\ContentType\Tests\Functional\Example\View\ImageType;
use Psi\Component\View;
use Psi\Component\View\ViewFactory;
use Symfony\Component\Form\Forms;

class Container extends PimpleContainer
{
    public function __construct(array $config = [])
    {
        $this['config'] = $config;
        $this->loadGeneral();
        $this->loadPsiContentType();
        $this->loadSymfonyForm();
        $this->loadView();
    }

    public function get($serviceId)
    {
        return $this[$serviceId];
    }

    private function loadGeneral()
    {
        $this['annotation_reader'] = function () {
            return new AnnotationReader();
        };
    }

    private function loadPsiContentType()
    {
        $this['psi_content_type.metadata.driver.array'] = function ($container) {
            return new ArrayDriver($container['config']['mapping']);
        };
        $this['psi_content_type.metadata.driver.annotation'] = function ($container) {
            return new CTAnnotationDriver($container['annotation_reader']);
        };
        $this['psi_content_type.metadata.driver.chain'] = function ($container) {
            return new DriverChain([
                $container['psi_content_type.metadata.driver.array'],
                $container['psi_content_type.metadata.driver.annotation'],
            ]);
        };

        $this['psi_content_type.metadata.factory'] = function ($container) {
            return new MetadataFactory(
                $container['psi_content_type.metadata.driver.chain']
            );
        };

        $this['psi_content_type.registry.field'] = function ($container) {
            $registry = new FieldRegistry();
            $registry->register('birthday', new Field\BirthdayField());
            $registry->register('checkbox', new Field\CheckboxField());
            $registry->register('currency', new Field\CurrencyField());
            $registry->register('language', new Field\LanguageField());
            $registry->register('locale', new Field\LocaleField());
            $registry->register('money', new Field\MoneyField());
            $registry->register('number', new Field\NumberField());
            $registry->register('percent', new Field\PercentField());
            $registry->register('range', new Field\RangeField());
            $registry->register('textarea', new Field\TextareaField());
            $registry->register('time', new Field\TimeField());
            $registry->register('timezone', new Field\TimezoneField());
            $registry->register('url', new Field\UrlField());
            $registry->register('email', new Field\EmailField());
            $registry->register('date', new Field\DateField());
            $registry->register('country', new Field\CountryField());
            $registry->register('text', new Field\TextField());
            $registry->register('integer', new Field\IntegerField());
            $registry->register('datetime', new Field\DateTimeField());
            $registry->register('image', new ImageField());
            $registry->register('object_reference', new ObjectReferenceField());
            $registry->register('collection', new Field\CollectionField($registry));
            $registry->register('choice', new Field\ChoiceField($registry));

            return $registry;
        };

        $this['psi_content_type.registry.type'] = function ($container) {
            $registry = new Storage\TypeRegistry();
            $registry->register(StdStorage\StringType::class, new StdStorage\StringType());
            $registry->register(StdStorage\IntegerType::class, new StdStorage\IntegerType());
            $registry->register(StdStorage\DateTimeType::class, new StdStorage\DateTimeType());
            $registry->register(StdStorage\ReferenceType::class, new StdStorage\ReferenceType());
            $registry->register(StdStorage\ObjectType::class, new StdStorage\ObjectType());
            $registry->register(StdStorage\BooleanType::class, new StdStorage\BooleanType());
            $registry->register(StdStorage\DoubleType::class, new StdStorage\DoubleType());
            $registry->register(StdStorage\CollectionType::class, new StdStorage\CollectionType());

            return $registry;
        };

        $this['psi_content_type.field_loader'] = function ($container) {
            return new FieldLoader(
                $container->get('psi_content_type.registry.field')
            );
        };

        $this['psi_content_type.storage.type_factory'] = function ($container) {
            return new Storage\TypeFactory($container->get('psi_content_type.registry.type'));
        };
    }

    private function loadSymfonyForm()
    {
        $this['symfony.form_factory'] = function ($container) {
            return Forms::createFormFactoryBuilder()
                ->addExtension(new FieldExtension(
                    $container['psi_content_type.metadata.factory'],
                    $container['psi_content_type.registry.field']
                ))
                ->getFormFactory();
        };
    }

    private function loadView()
    {
        $this['psi_content_type.view.factory'] = function ($container) {
            return new ViewFactory($container['psi_content_type.view.type_registry']);
        };

        $this['psi_content_type.view.type_registry'] = function ($container) {
            $registry = new View\TypeRegistry();
            $registry->register(ImageType::class, new ImageType());
            $registry->register(StdView\NullType::class, new StdView\NullType());
            $registry->register(StdView\UrlType::class, new StdView\UrlType());
            $registry->register(StdView\ScalarType::class, new StdView\ScalarType());
            $registry->register(StdView\DateTimeType::class, new StdView\DateTimeType());
            $registry->register(StdView\CollectionType::class, new StdView\CollectionType(
                $container->get('psi_content_type.field_loader')
            ));
            $registry->register(StdView\BooleanType::class, new StdView\BooleanType());
            $registry->register(StdView\ObjectType::class, new StdView\ObjectType(
                $container->get('psi_content_type.metadata.factory'),
                $container->get('psi_content_type.field_loader')
            ));

            return $registry;
        };

        $this['psi_content_type.view_factory'] = function ($container) {
            return new View\ViewFactory(
                $container['psi_content_type.metadata.factory'],
                $container['psi_content_type.field_loader'],
                $container['psi_content_type.view.type_registry']
            );
        };
    }
}
