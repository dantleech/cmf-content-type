<?php

namespace Psi\Component\ContentType\Tests\Unit\Standard\View;

use Psi\Component\ContentType\Standard\View\ScalarType;
use Psi\Component\ContentType\Standard\View\ScalarView;
use Psi\Component\View\View;

class ScalarTypeTest extends TypeTestCase
{
    protected function getType()
    {
        return new ScalarType();
    }

    /**
     * It should allow null values.
     */
    public function testAllowNullValues()
    {
        $this->getType()->createView($this->factory->reveal(), null, $this->resolveOptions());
    }

    /**
     * It should allow scalar values.
     */
    public function testAllowScalarValues()
    {
        $view = $this->getType()->createView($this->factory->reveal(), 'hello', $this->resolveOptions());
        $this->assertInstanceOf(ScalarView::class, $view);
        $this->assertEquals('hello', $view->getValue());
    }

    /**
     * It should throw exceptions if a non-scalar and non-null value is given.
     *
     * @expectedException \InvalidArgumentException
     * @expectedExceptionMessage Scalar view only accepts scalar values! Got "array"
     */
    public function testDisallowNonScalarAndNonNullValues()
    {
        $this->getType()->createView($this->factory->reveal(), ['invalid'], []);
    }
}
