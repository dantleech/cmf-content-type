<?php

namespace Psi\Bridge\ContentType\Doctrine\PhpcrOdm\Tests\Functional;

use Psi\Bridge\ContentType\Doctrine\PhpcrOdm\Tests\Functional\Example\Article;

class ScalarTest extends PhpcrOdmTestCase
{
    public function setUp()
    {
        $container = $this->getContainer([
            'mapping' => [
                Article::class => [
                    'fields' => [
                        'paragraphs' => [
                            'type' => 'collection',
                            'options' => [
                                'field_type' => 'text',
                                'field_options' => [],
                            ],
                        ],
                        'numbers' => [
                            'type' => 'collection',
                            'options' => [
                                'field_type' => 'integer',
                            ],
                        ],
                    ],
                ],
            ],
        ]);
        $this->documentManager = $container->get('doctrine_phpcr.document_manager');
        $this->initPhpcr($this->documentManager);
    }

    public function testStringCollection()
    {
        $article = new Article();
        $article->id = '/test/article';
        $article->title = 'Foo';
        $article->paragraphs = ['one', 'two', 'three'];

        $this->documentManager->persist($article);
        $this->documentManager->flush();
        $this->documentManager->clear();

        $article = $this->documentManager->find(null, '/test/article');
        $this->assertSame(['one', 'two', 'three'], $article->paragraphs);
    }

    public function testIntegerCollection()
    {
        $article = new Article();
        $article->id = '/test/article';
        $article->title = 'Foo';
        $article->numbers = ['12', '13', '14'];

        $this->documentManager->persist($article);
        $this->documentManager->flush();
        $this->documentManager->clear();

        $article = $this->documentManager->find(null, '/test/article');
        $this->assertSame([12, 13, 14], $article->numbers);
    }
}