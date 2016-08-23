<?php

/*
 * This file is part of the Symfony CMF package.
 *
 * (c) 2011-2016 Symfony CMF
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Symfony\Cmf\Component\ContentType\Tests\Functional\Storage\Doctrine\PhpcrOdm;

use Doctrine\ODM\PHPCR\ChildrenCollection;
use Symfony\Cmf\Component\ContentType\Tests\Functional\Example\Model\Image;
use Symfony\Cmf\Component\ContentType\Tests\Functional\Example\Storage\Doctrine\PhpcrOdm\Article;

class ContentTypeDriverTest extends PhpcrOdmTestCase
{
    private $documentManager;

    public function setUp()
    {
        $container = $this->getContainer([
            'mapping' => [
                Article::class => [
                    'properties' => [
                        'title' => [
                            'type' => 'text',
                        ],
                        'image' => [
                            'type' => 'image',
                        ],
                        'slideshow' => [
                            'type' => 'collection',
                        ],
                    ],
                ],
            ],
        ]);
        $this->documentManager = $container->get('doctrine_phpcr.document_manager');
        $this->initPhpcr($this->documentManager);
    }

    /**
     * It should persist a mapped content type document.
     */
    public function testFieldMapping()
    {
        $image = new Image();
        $image->id = '/test/image';
        $image->path = '/path/to/image';
        $image->width = 100;
        $image->height = 200;
        $image->mimetype = 'image/jpeg';

        $this->documentManager->persist($image);
        $this->documentManager->flush();
    }

    /**
     * The user document should be persisted with the content-type data.
     */
    public function testUserMapping()
    {
        $article = new Article();
        $article->id = '/test/article';
        $article->title = 'Hello';

        $article->image = $this->createImage(
            '/path/to/image', 100, 200, 'image/jpeg'
        );

        $this->documentManager->persist($article);
        $this->documentManager->flush();

        $this->documentManager->clear();

        $article = $this->documentManager->find(null, '/test/article');

        $this->assertInstanceOf(Article::class, $article);
        $this->assertEquals('Hello', $article->title);
        $this->assertInstanceOf(Image::class, $article->image);
        $this->assertEquals(100, $article->image->width);
        $this->assertEquals('image/jpeg', $article->image->mimetype);
    }

    /**
     * It should persist colletion types.
     */
    public function testCollectionType()
    {
        $this->createArticleSlideshow();

        $article = $this->documentManager->find(null, '/test/article');
        $slideshow = $article->slideshow;
        $this->assertInstanceOf(ChildrenCollection::class, $slideshow);
        $slideshow = iterator_to_array($slideshow);
        $this->assertCount(3, $slideshow);

        $image1 = array_shift($slideshow);
        $image2 = array_shift($slideshow);
        $image3 = array_shift($slideshow);

        $this->assertEquals('/path/to/image1', $image1->path);
        $this->assertEquals('/path/to/image2', $image2->path);
        $this->assertEquals('/path/to/image3', $image3->path);
    }

    /**
     * Children should be removed from collections.
     *
     * @depends testCollectionType
     */
    public function testCollectionTypeChildrenRemoved()
    {
        $this->createArticleSlideshow();

        $article = $this->documentManager->find(null, '/test/article');
        $slideshow = $article->slideshow;
        $slideshow->removeElement($slideshow->first());
        $slideshow->removeElement($slideshow->first());

        $this->documentManager->persist($article);
        $this->documentManager->flush();
        $this->documentManager->clear();

        $image0 = $this->documentManager->find(null, '/test/article/cmfct:slideshow-0');
        $image1 = $this->documentManager->find(null, '/test/article/cmfct:slideshow-1');
        $image2 = $this->documentManager->find(null, '/test/article/cmfct:slideshow-2');

        $this->assertNull($image0);
        $this->assertNull($image1);
        $this->assertNotNull($image2);
    }

    /**
     * It should not preserve array keys.
     */
    public function testCollectionTypeArrayKeys()
    {
        $article = new Article();
        $article->id = '/test/article';
        $article->title = 'Foo';

        $image0 = $this->createImage('/path/to/image1', 100, 200, 'image/jpeg');
        $image1 = $this->createImage('/path/to/image2', 100, 200, 'image/jpeg');

        $article->slideshow = [
            'image_0' => $image0,
            'image_1' => $image1,
        ];

        $this->documentManager->persist($article);
        $this->documentManager->flush();
        $this->documentManager->clear();

        $image0 = $this->documentManager->find(null, '/test/article/cmfct:slideshow-0');
        $image1 = $this->documentManager->find(null, '/test/article/cmfct:slideshow-1');

        $this->assertNotNull($image0);
        $this->assertNotNull($image1);
    }

    private function createImage($path, $width, $height, $mimeType)
    {
        $image = new Image();
        $image->path = $path;
        $image->width = $width;
        $image->height = $height;
        $image->mimetype = $mimeType;

        return $image;
    }

    private function createArticleSlideshow()
    {
        $article = new Article();
        $article->id = '/test/article';
        $article->title = 'Hello';
        $article->image = $this->createImage(
            '/path/to/image', 100, 200, 'image/jpeg'
        );

        $image1 = $this->createImage('/path/to/image1', 100, 200, 'image/jpeg');
        $image2 = $this->createImage('/path/to/image2', 100, 200, 'image/jpeg');
        $image3 = $this->createImage('/path/to/image3', 100, 200, 'image/jpeg');

        $article->slideshow = [
            $image1,
            $image2,
            $image3,
        ];

        $this->documentManager->persist($article);
        $this->documentManager->flush();

        $this->documentManager->clear();
    }
}
