<?php

namespace Psi\Component\ContentType\Tests\Unit\Standard\View;

use Psi\Component\View\ViewFactory;
use Symfony\Component\OptionsResolver\OptionsResolver;

abstract class TypeTestCase extends \PHPUnit_Framework_TestCase
{
    protected $factory;

    public function setUp()
    {
        $this->factory = $this->prophesize(ViewFactory::class);
    }

    abstract protected function getType();

    protected function resolveOptions($options = [])
    {
        $resolver = new OptionsResolver();
        $this->getType()->configureOptions($resolver);

        return $resolver->resolve($options);
    }
}
