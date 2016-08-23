<?php

/*
 * This file is part of the Symfony CMF package.
 *
 * (c) 2011-2016 Symfony CMF
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Symfony\Cmf\Component\ContentType\Tests\Functional\Example\Storage\Doctrine\PhpcrOdm;

use Doctrine\ODM\PHPCR\Mapping\Annotations as PHPCR;

/**
 * @PHPCR\Document()
 */
class Article
{
    /**
     * @PHPCR\Id()
     */
    public $id;

    // mapped via. the content-type metadata
    public $title;
    public $image;
    public $slideshow;
}
