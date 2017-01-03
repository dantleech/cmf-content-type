<?php

namespace Psi\Component\ContentType\Tests\Unit\Standard\View;

use Psi\Component\ContentType\Standard\View\CollectionView;
use Psi\Component\View\ViewFactory;
use Psi\Component\View\ViewInterface;

class CollectionViewTest extends \PHPUnit_Framework_TestCase
{
    private $view1;
    private $view2;
    private $factory;

    public function setUp()
    {
        $this->factory = $this->prophesize(ViewFactory::class);
        $this->view1 = $this->prophesize(ViewInterface::class);
        $this->view2 = $this->prophesize(ViewInterface::class);
    }

    /**
     * It should iterate over a collection of objects and "yield" views.
     */
    public function testIterate()
    {
        $data = new \ArrayObject([
            $first = new \stdClass(),
            new \stdClass(),
        ]);

        $viewType = 'type';
        $viewOptions = ['foo' => 'bar'];

        $this->factory->create(
            $viewType,
            $first,
            $viewOptions
        )->shouldBeCalledTimes(2)->willReturn(
            $this->view1->reveal(),
            $this->view2->reveal()
        );

        $collection = new CollectionView(
            $this->factory->reveal(),
            $data,
            $viewType,
            $viewOptions
        );

        $collection = iterator_to_array($collection);
        $this->assertCount(2, $collection);
        $this->assertSame($this->view1->reveal(), $collection[0]);
        $this->assertSame($this->view2->reveal(), $collection[1]);
    }
}
