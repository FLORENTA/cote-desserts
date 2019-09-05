<?php

namespace Tests\AppBundle\Repository;

use AppBundle\Entity\Article;
use AppBundle\Repository\ArticleRepository;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;

/**
 * Class ArticleRepositoryTest
 * @package Tests\AppBundle\Repository
 */
class ArticleRepositoryTest extends KernelTestCase
{
    /** @var ArticleRepository $articleRepository */
    private $articleRepository;

    public function setUp(): void
    {
        $kernel = $this->bootKernel();
        $container = $kernel->getContainer();
        $this->articleRepository = $container->get('doctrine.orm.entity_manager')->getRepository(Article::class);
    }

    // Getting max article id in db
    // Max id in db = 20
    public function testGetMaxArticleId(): void
    {
        $maxId = $this->articleRepository->getMaxArticleId();
        $this->assertNotEquals(19, $maxId);
        $this->assertEquals(20, $maxId);
    }

    // Getting article by slug
    public function testGetArticlesBySlug(): void
    {
        /** @var array $result */
        $result = $this->articleRepository->getArticlesBySlug('invalid_slug');
        $this->assertEquals(0, $result['count_same_slug']);

        /** @var array $result */
        $result = $this->articleRepository->getArticlesBySlug('slug_10');
        $this->assertEquals('1', $result['count_same_slug']);
    }

    // Getting articles and images linked to a category
    public function testGetArticlesByCategory(): void
    {
        /** @var array $result */
        $result = $this->articleRepository->getArticlesByCategory('banane');
        $expectedResult = [
            [
            "id" => "1",
            "slug" => "slug_1",
            "title" => "article_1",
            "image_src" => "image_1.jpg",
            ], [
            "id" => "2",
            "slug" => "slug_2",
            "title" => "article_2",
            "image_src" => null,
            ], [
            "id" => "3",
            "slug" => "slug_3",
            "title" => "article_3",
            "image_src" => null,
            ], [
            "id" => "4",
            "slug" => "slug_4",
            "title" => "article_4",
            "image_src" => null
            ], [
            "id" => "5",
            "slug" => "slug_5",
            "title" => "article_5",
            "image_src" => null,
            ], [
            "id" => "6",
            "slug" => "slug_6",
            "title" => "article_6",
            "image_src" => null
            ], [
            "id" => "7",
            "slug" => "slug_7",
            "title" => "article_7",
            "image_src" => null
            ], [
            "id" => "8",
            "slug" => "slug_8",
            "title" => "article_8",
            "image_src" => null
            ], [
            "id" => "9",
            "slug" => "slug_9",
            "title" => "article_9",
            "image_src" => null
            ], [
            "id" => "10",
            "slug" => "slug_10",
            "title" => "article_10",
            "image_src" => "image_2.jpg"
            ]
        ];

        $this->assertEquals($expectedResult, $result);

        /** @var array $result */
        $result = $this->articleRepository->getArticlesByCategory('unknwn_category');
        $this->assertEquals([], $result);
    }

    // Getting article titles by specific keyword
    public function testGetTitlesByKeyword(): void
    {
        /** @var array $result */
        $result = $this->articleRepository->getTitlesByKeyword("article_15");
        $this->assertEquals("article_15", $result[0]['title']);

        // Located in the image content field
        /** @var array $result */
        $result = $this->articleRepository->getTitlesByKeyword("tart");
        $this->assertEquals("article_1", $result[0]['title']);

        // Located in the image content field
        /** @var array $result */
        $result = $this->articleRepository->getTitlesByKeyword("mousse cake");
        $this->assertEquals("article_10", $result[0]['title']);
    }
}