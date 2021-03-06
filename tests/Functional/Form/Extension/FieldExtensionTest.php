<?php

namespace Psi\Component\ContentType\Tests\Functional\Form\Extension;

use Psi\Component\ContentType\Tests\Functional\BaseTestCase;
use Psi\Component\ContentType\Tests\Functional\Example\Model\Article;
use Psi\Component\ContentType\Tests\Functional\Example\Model\Image;

class FieldExtensionTest extends BaseTestCase
{
    private $formFactory;

    public function setUp()
    {
        $this->formFactory = $this->getContainer([
            'mapping' => [
                Article::class => [
                    'alias' => 'article',
                    'fields' => [
                        'title' => [
                            'type' => 'text',
                        ],
                        'image' => [
                            'type' => 'image',
                        ],
                        'slideshow' => [
                            'type' => 'collection',
                            'shared' => [
                                'field_type' => 'image',
                            ],
                        ],
                    ],
                ],
            ],
        ])->get('symfony.form_factory');
    }

    /**
     * It should dynamically create form types for content objects.
     */
    public function testCreate()
    {
        $builder = $this->formFactory->createBuilder(Article::class);
        $form = $builder->getForm();

        $imageData = [
            'height' => 100,
            'width' => 100,
            'mimetype' => 'image/jpeg',
            'path' => 'path/to/foo.png',
        ];
        $data = [
            'title' => 'Hello',
            'image' => $imageData,
        ];

        $form->submit($data);
        $article = $form->getData();

        $this->assertTrue($form->isValid());

        $this->assertInstanceOf(Article::class, $article);
        $this->assertEquals('Hello', $article->title);
        $this->assertInstanceOf(Image::class, $article->image);
        $this->assertEquals('path/to/foo.png', $article->image->path);
    }

    /**
     * It should process collections.
     */
    public function testCreateWithCollection()
    {
        $builder = $this->formFactory->createBuilder(Article::class);
        $form = $builder->getForm();

        $imageData1 = $imageData2 = [
            'height' => 100,
            'width' => 100,
            'mimetype' => 'image/jpeg',
            'path' => 'path/to/foo1.png',
        ];
        $imageData2['path'] = 'path/to/foo2.png';

        $data = [
            'title' => 'Hello',
            'slideshow' => [
                $imageData1,
                $imageData2,
            ],
        ];

        $form->submit($data);
        $article = $form->getData();

        $this->assertTrue($form->isValid());

        $this->assertInstanceOf(Article::class, $article);
        $this->assertCount(2, $article->slideshow);
        $this->assertEquals('path/to/foo1.png', $article->slideshow[0]->path);
        $this->assertEquals('path/to/foo2.png', $article->slideshow[1]->path);
    }
}
