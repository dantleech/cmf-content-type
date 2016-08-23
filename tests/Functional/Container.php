<?php

/*
 * This file is part of the Symfony CMF package.
 *
 * (c) 2011-2016 Symfony CMF
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Symfony\Cmf\Component\ContentType\Tests\Functional;

use Doctrine\Common\Annotations\AnnotationReader;
use Doctrine\Common\Persistence\Mapping\Driver\MappingDriverChain;
use Doctrine\DBAL\DriverManager;
use Doctrine\ODM\PHPCR\Configuration;
use Doctrine\ODM\PHPCR\DocumentManager;
use Doctrine\ODM\PHPCR\Mapping\Driver\AnnotationDriver;
use Doctrine\ODM\PHPCR\NodeTypeRegistrator;
use Jackalope\RepositoryFactoryDoctrineDBAL;
use Jackalope\Transport\DoctrineDBAL\RepositorySchema;
use Metadata\MetadataFactory;
use PHPCR\SimpleCredentials;
use Pimple\Container as PimpleContainer;
use Symfony\Cmf\Component\ContentType\ContentViewBuilder;
use Symfony\Cmf\Component\ContentType\Field\CollectionField;
use Symfony\Cmf\Component\ContentType\Field\TextField;
use Symfony\Cmf\Component\ContentType\FieldRegistry;
use Symfony\Cmf\Component\ContentType\Form\Extension\FieldExtension;
use Symfony\Cmf\Component\ContentType\Mapping\IntegerMapping;
use Symfony\Cmf\Component\ContentType\Mapping\StringMapping;
use Symfony\Cmf\Component\ContentType\MappingRegistry;
use Symfony\Cmf\Component\ContentType\MappingResolver;
use Symfony\Cmf\Component\ContentType\Metadata\Driver\ArrayDriver;
use Symfony\Cmf\Component\ContentType\Storage\Doctrine\PhpcrOdm\ContentTypeDriver;
use Symfony\Cmf\Component\ContentType\Storage\Doctrine\PhpcrOdm\FieldMapper;
use Symfony\Cmf\Component\ContentType\Storage\Doctrine\PhpcrOdm\NodeTypeRegistrator as CtNodeTypeRegistrator;
use Symfony\Cmf\Component\ContentType\Storage\Doctrine\PhpcrOdm\PropertyEncoder;
use Symfony\Cmf\Component\ContentType\Storage\Doctrine\PhpcrOdm\Subscriber\CollectionSubscriber;
use Symfony\Cmf\Component\ContentType\Storage\Doctrine\PhpcrOdm\Subscriber\MetadataSubscriber;
use Symfony\Cmf\Component\ContentType\Tests\Functional\Example\Field\ImageField;
use Symfony\Cmf\Component\ContentType\Tests\Functional\Example\View\ImageView;
use Symfony\Cmf\Component\ContentType\View\ScalarView;
use Symfony\Cmf\Component\ContentType\ViewRegistry;
use Symfony\Component\Form\Forms;

class Container extends PimpleContainer
{
    public function __construct(array $config = [])
    {
        $this['config'] = array_merge([
            'mapping' => [],
            'db_path' => __DIR__ . '/../../cache/test.sqlite',
        ], $config);

        $this->loadCmfContentType();
        $this->loadSymfonyForm();
        $this->loadDoctrineDbal();
        $this->loadPhpcrOdm();
    }

    public function get($serviceId)
    {
        return $this[$serviceId];
    }

    private function loadCmfContentType()
    {
        $this['cmf_content_type.metadata.driver.array'] = function ($container) {
            return new ArrayDriver($container['config']['mapping']);
        };

        $this['cmf_content_type.metadata.factory'] = function ($container) {
            return new MetadataFactory(
                $container['cmf_content_type.metadata.driver.array']
            );
        };

        $this['cmf_content_type.registry.field'] = function ($container) {
            $registry = new FieldRegistry();
            $registry->register('text', new TextField());
            $registry->register('image', new ImageField());
            $registry->register('collection', new CollectionField());

            return $registry;
        };

        $this['cmf_content_type.registry.view'] = function ($container) {
            $registry = new ViewRegistry();
            $registry->register(ScalarView::class, new ScalarView());
            $registry->register(ImageView::class, new ImageView());

            return $registry;
        };

        $this['cmf_content_type.registry.mapping'] = function ($container) {
            $registry = new MappingRegistry();
            $registry->register('string', new StringMapping());
            $registry->register('integer', new IntegerMapping());

            return $registry;
        };

        $this['cmf_content_type.view_builder'] = function ($container) {
            return new ContentViewBuilder(
                $container['cmf_content_type.metadata.factory'],
                $container['cmf_content_type.registry.field'],
                $container['cmf_content_type.registry.view']
            );
        };

        $this['cmf_content_type.mapping_resolver'] = function ($container) {
            return new MappingResolver(
                $container['cmf_content_type.registry.mapping']
            );
        };
    }

    private function loadSymfonyForm()
    {
        $this['symfony.form_factory'] = function ($container) {
            return Forms::createFormFactoryBuilder()
                ->addExtension(new FieldExtension(
                    $container['cmf_content_type.metadata.factory'],
                    $container['cmf_content_type.registry.field']
                ))
                ->getFormFactory();
        };
    }

    private function loadDoctrineDbal()
    {
        $this['dbal.connection'] = function () {
            return DriverManager::getConnection([
                'driver'    => 'pdo_sqlite',
                'path' => $this['config']['db_path'],
            ]);
        };
    }

    private function loadPhpcrOdm()
    {
        $this['cmf_content_type.storage.doctrine.phpcr_odm.property_encoder'] = function ($container) {
            return new PropertyEncoder('cmfct', 'https://github.com/symfony-cmf/content-type');
        };

        $this['cmf_content_type.storage.doctrine.phpcr_odm.field_mapper'] = function ($container) {
            return new FieldMapper($container['cmf_content_type.storage.doctrine.phpcr_odm.property_encoder']);
        };

        $this['doctrine_phpcr.document_manager'] = function ($container) {
            $registerNodeTypes = false;

            // automatically setup the schema if the db doesn't exist yet.
            if (!file_exists($container['config']['db_path'])) {
                if (!file_exists($dir = dirname($container['config']['db_path']))) {
                    mkdir($dir);
                }

                $connection = $container['dbal.connection'];

                $schema = new RepositorySchema();
                foreach ($schema->toSql($connection->getDatabasePlatform()) as $sql) {
                    $connection->exec($sql);
                }

                $registerNodeTypes = true;
            }

            // register the phpcr session
            $factory = new RepositoryFactoryDoctrineDBAL();
            $repository = $factory->getRepository([
                'jackalope.doctrine_dbal_connection' => $container['dbal.connection'],
            ]);
            $session = $repository->login(new SimpleCredentials(null, null), 'default');

            if ($registerNodeTypes) {
                $typeRegistrator = new NodeTypeRegistrator();
                $typeRegistrator->registerNodeTypes($session);
                $ctTypeRegistrator = new CtNodeTypeRegistrator(
                    $container['cmf_content_type.storage.doctrine.phpcr_odm.property_encoder']
                );
                $ctTypeRegistrator->registerNodeTypes($session);
            }

            // content type driver
            $contentTypeDriver = new ContentTypeDriver(
                $container['cmf_content_type.registry.field'],
                $container['cmf_content_type.registry.mapping'],
                $container['cmf_content_type.mapping_resolver'],
                $container['cmf_content_type.storage.doctrine.phpcr_odm.field_mapper']
            );

            // annotation driver
            $reader = new AnnotationReader();
            $annotationDriver = new AnnotationDriver($reader, [
                __DIR__ . '/../../vendor/doctrine/phpcr-odm/lib/Doctrine/ODM/PHPCR/Document',
                __DIR__ . '/Example/Storage/Doctrine/PhpcrOdm',
            ]);
            $chain = new MappingDriverChain();
            $chain->addDriver($annotationDriver, 'Symfony\Cmf\Component\ContentType\Tests\Functional\Example\Storage\Doctrine\PhpcrOdm');
            $chain->addDriver($contentTypeDriver, 'Symfony');
            $chain->addDriver($annotationDriver, 'Doctrine');


            $config = new Configuration();
            $config->setMetadataDriverImpl($chain);

            $manager = DocumentManager::create($session, $config);
            $manager->getEventManager()->addEventSubscriber(new MetadataSubscriber(
                $container['cmf_content_type.metadata.factory'],
                $container['cmf_content_type.registry.field'],
                $container['cmf_content_type.mapping_resolver'],
                $container['cmf_content_type.storage.doctrine.phpcr_odm.field_mapper']
            ));
            $manager->getEventManager()->addEventSubscriber(new CollectionSubscriber(
                $container['cmf_content_type.metadata.factory'],
                $container['cmf_content_type.registry.field'],
                $container['cmf_content_type.storage.doctrine.phpcr_odm.property_encoder']
            ));

            return $manager;
        };
    }
}
