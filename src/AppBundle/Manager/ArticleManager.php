<?php

namespace AppBundle\Manager;

use AppBundle\Entity\Article;
use AppBundle\Repository\ArticleRepository;
use DateTime;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\NonUniqueResultException;
use Doctrine\ORM\NoResultException;
use Exception;
use Psr\Log\LoggerInterface;
use Symfony\Component\HttpFoundation\JsonResponse;

/**
 * Class ArticleManager
 * @package AppBundle\Manager
 */
class ArticleManager
{
    const NO_ERROR = 0;
    const ERROR = -1;

    /** @var EntityManagerInterface $em */
    private $em;

    /** @var ArticleRepository $articleRepository */
    private $articleRepository;

    /** @var LoggerInterface */
    private $logger;

    /** @var int $searchMaxResults */
    private $searchMaxResults;

    /**
     * ArticleService constructor.
     * @param EntityManagerInterface $entityManager
     * @param LoggerInterface $logger
     * @param int $searchMaxResults
     */
    public function __construct(
        EntityManagerInterface $entityManager,
        LoggerInterface $logger,
        int $searchMaxResults
    )
    {
        $this->em = $entityManager;
        $this->articleRepository = $entityManager->getRepository(Article::class);
        $this->logger = $logger;
        $this->searchMaxResults = $searchMaxResults;
    }

    /**
     * @return int
     */
    public function getMaxArticleId(): int
    {
        try {
            /** @var int $maxId */
            $maxId = $this->articleRepository->getMaxArticleId() ?? 0;
        } catch (NonUniqueResultException|Exception $exception) {
            $this->logger->error($exception->getMessage(), ['_method' => __METHOD__]);
            $maxId = 0;
        }

        return $maxId;
    }

    /**
     * @param string $title
     * @return Article|null
     */
    public function getArticleByTitle(string $title): ?Article
    {
        return $this->articleRepository->findOneBy(['title' => $title]);
    }

    /**
     * @return array
     */
    public function getArticles(): array
    {
        return $this->articleRepository->getArticles();
    }

    /**
     * @param $token
     * @return Article
     */
    public function getArticleByToken($token): ?Article
    {
        return $this->articleRepository->findOneBy(['token' => $token]);
    }

    /**
     * @param string $pdf
     * @return Article
     */
    public function getArticleByPdf(string $pdf): ?Article
    {
        return $this->articleRepository->findOneBy(['pdf' => $pdf]);
    }

    /**
     * @param string $slug
     * @return Article|JsonResponse
     */
    public function getArticleBySlug(string $slug): ?Article
    {
        return $this->articleRepository->findOneBy(['slug' => $slug]);
    }

    /**
     * @param array $categories
     * @return array
     */
    public function getArticlesByCategory(array $categories): array
    {
        return $this->articleRepository->getArticlesByCategory($categories);
    }

    /**
     * @param string $slug
     * @return array
     */
    public function getNumberOfArticlesBySlug(string $slug): array
    {
        try {
            return $this->articleRepository->getArticlesBySlug($slug);
        } catch (NonUniqueResultException|NoResultException|Exception $exception) {
            return ['count_same_slug' => 0];
        }
    }

    /**
     * @param string $keyword
     * @return array
     */
    public function getTitlesByKeyword(string $keyword): array
    {
        return $this->articleRepository->getTitlesByKeyword($keyword, $this->searchMaxResults);
    }

    /**
     * @param Article $article
     */
    public function createArticle(Article $article): void
    {
        $this->em->persist($article);
        $this->em->flush();
    }

    /**
     * @param Article $article
     */
    public function deleteArticle(Article $article): void
    {
        $this->em->remove($article);
        $this->em->flush();
    }

    /**
     * @param Article $article
     * @return int
     */
    public function updateArticle(Article $article): int
    {
        try {
            $article->setUpdateAt(new DateTime());
            $this->em->flush();

            return self::NO_ERROR;
        } catch (Exception $exception) {
            $this->logger->error($exception->getMessage(), [
                '_method' => __METHOD__
            ]);

            return self::ERROR;
        }
    }

    /**
     * @param Article $article
     */
    public function unsetPdf(Article $article): void
    {
        $article->setPdf(null);
        $this->em->flush();
    }

    /**
     * @return array
     */
    public function getArticlesWithNewsletter(): array
    {
        return $this->articleRepository->getArticlesWithNewsletter();
    }
}