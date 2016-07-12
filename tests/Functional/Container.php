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

use Metadata\MetadataFactory;
use Pimple\Container as PimpleContainer;
use Symfony\Cmf\Component\ContentType\ContentViewBuilder;
use Symfony\Cmf\Component\ContentType\Field\TextField;
use Symfony\Cmf\Component\ContentType\FieldRegistry;
use Symfony\Cmf\Component\ContentType\Form\FormBuilder;
use Symfony\Cmf\Component\ContentType\Metadata\Driver\ArrayDriver;
use Symfony\Cmf\Component\ContentType\View\ScalarView;
use Symfony\Cmf\Component\ContentType\ViewRegistry;
use Symfony\Component\Form\Forms;
use Symfony\Cmf\Component\ContentType\Tests\Functional\Example\Field\ImageField;

class Container extends PimpleContainer
{
    public function __construct(array $config = [])
    {
        $this['config'] = array_merge([
            'mapping' => [],
        ], $config);

        $this->loadCmfContentType();
        $this->loadSymfonyForm();
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

            return $registry;
        };

        $this['cmf_content_type.registry.view'] = function ($container) {
            $registry = new ViewRegistry();
            $registry->register('scalar', new ScalarView());

            return $registry;
        };

        $this['cmf_content_type.form_builder'] = function ($container) {
            return new FormBuilder(
                $container['cmf_content_type.metadata.factory'],
                $container['symfony.form_factory'],
                $container['cmf_content_type.registry.field']
            );
        };

        $this['cmf_content_type.view_builder'] = function ($container) {
            return new ContentViewBuilder(
                $container['cmf_content_type.metadata.factory'],
                $container['cmf_content_type.registry.field'],
                $container['cmf_content_type.registry.view']
            );
        };
    }

    private function loadSymfonyForm()
    {
        $this['symfony.form_factory'] = function ($container) {
            return Forms::createFormFactoryBuilder()
                ->getFormFactory();
        };
    }
}
