<?php

/*
 * This file is part of the Symfony CMF package.
 *
 * (c) 2011-2016 Symfony CMF
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Symfony\Cmf\Component\ContentType\Tests\Unit\Storage\Doctrine\PhpcrOdm;

use Symfony\Cmf\Component\ContentType\Storage\Doctrine\PhpcrOdm\PropertyEncoder;

class PropertyEncoderTest extends \PHPUnit_Framework_TestCase
{
    private $encoder;

    public function setUp()
    {
        $this->encoder = new PropertyEncoder('prefix', 'https://example.com');
    }

    /**
     * It should encode a field name.
     */
    public function testEncode()
    {
        $propertyName = $this->encoder->encode('hello');

        $this->assertEquals('prefix:hello', $propertyName);
    }

    /**
     * Encode field name with key.
     */
    public function testEncodeWithKey()
    {
        $propertyName = $this->encoder->encode('hello', 'goodbye');

        $this->assertEquals('prefix:hello-goodbye', $propertyName);
    }
}
