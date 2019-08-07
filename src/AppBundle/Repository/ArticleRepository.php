<?php

namespace AppBundle\Repository;

use Doctrine\DBAL\Connection;
use Doctrine\ORM\EntityRepository;
use Doctrine\ORM\NonUniqueResultException;
use Doctrine\ORM\NoResultException;
use Doctrine\ORM\QueryBuilder;

/**
 * ArticleRepository
 *
 * This class was generated by the Doctrine ORM. Add your own custom
 * repository methods below.
 */
class ArticleRepository extends EntityRepository
{
    /**
     * @return int
     * @throws NonUniqueResultException
     */
    public function getMaxArticleId(): int
    {
        $qb = $this->createQueryBuilder('article')
            ->select('MAX(article.id) as max_id');

        return (int) $qb->getQuery()->getSingleScalarResult();
    }

    /**
     * @return int
     * @throws NonUniqueResultException
     */
    public function getNumberOfArticles(): int
    {
        /** @var QueryBuilder $qb */
        $qb = $this->createQueryBuilder('article')
            ->select('COUNT(article.id)');

        return (int) $qb->getQuery()->getSingleScalarResult();
    }

    /**
     * @param int $id
     * @return array
     */
    public function getArticles(int $id): array
    {
        /** @var Connection $connection */
        $connection = $this->_em->getConnection();

        $query ="SELECT a.id, a.slug, a.title, a.token,
                  (SELECT i.src FROM image i WHERE i.article_id = a.id ORDER BY i.id LIMIT 1) as image_src
                 FROM article a
                 WHERE a.id <= '$id'
                 ORDER BY a.id DESC
                 LIMIT 9
        ";

        return $connection->fetchAll($query);
    }

    /**
     * Function to get the number of articles having the same slug
     * If one or more, must change the slug end value (slug-2, slug-3, ...slug-n)
     *
     * @param string $slug
     * @return array
     * @throws NonUniqueResultException
     * @throws NoResultException
     */
    public function getArticlesBySlug(string $slug): array
    {
        /** @var QueryBuilder $qb */
        $qb = $this->createQueryBuilder('a')
            ->select('count(a.id) as count_same_slug')
            ->where('a.slug LIKE :slug')
            ->setParameter('slug', "%$slug%");

        return $qb->getQuery()->getSingleResult();
    }

    /**
     * @param string $keyword
     * @param int $searchMaxResults
     * @return array
     */
    public function getTitlesByKeyword(string $keyword, int $searchMaxResults = 10): array
    {
        $keyword = preg_replace("/'/", "\'", $keyword);

        /** @var QueryBuilder $qb */
        $qb = $this->createQueryBuilder('article')
            ->select('article.title', 'article.slug')
            ->join('article.images', 'images')
            ->join('article.categories', 'categories')
            ->where('article.title LIKE :keyword')
            ->orWhere('images.title LIKE :keyword')
            ->orWhere('images.content LIKE :keyword')
            ->orWhere('categories.category LIKE :keyword')
            ->setParameter('keyword', '%' . $keyword . '%')
            ->orderBy('article.title')
            ->groupBy('article.title')
            ->setMaxResults($searchMaxResults);

        return $qb->getQuery()->getResult();
    }

    /**
     * @param array $categories
     * @return array
     */
    public function getArticlesByCategory(array $categories): array
    {
        /** @var Connection $connection */
        $connection = $this->_em->getConnection();

        $categories = array_map(function($category) use ($connection) {
            return $connection->quote($category->getCategory(), 'string');
        }, $categories);

        $categories = implode(',', $categories);

        $query = "SELECT DISTINCT a.id, a.slug, a.title,
                  (SELECT i.src FROM image i WHERE i.article_id = a.id ORDER BY i.id LIMIT 1) as image_src
                  FROM article a
                  JOIN article_category ac ON ac.article_id = a.id
                  JOIN category c ON c.id = ac.category_id
                  WHERE c.category IN ($categories)
        ";

        return $connection->fetchAll($query);
    }

    /**
     * @return array
     */
    public function getArticlesWithNewsletter(): array
    {
        /** @var QueryBuilder $qb */
        $qb = $this->createQueryBuilder('article')
            ->select('article.title')
            ->where('article.newsletter = true')
            ->orderBy('article.title');

        return $qb->getQuery()->getResult();
    }
}
