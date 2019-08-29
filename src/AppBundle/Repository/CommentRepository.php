<?php

namespace AppBundle\Repository;

use AppBundle\Entity\Article;
use Doctrine\ORM\EntityRepository;
use Doctrine\ORM\QueryBuilder;

/**
 * CommentRepository
 *
 * This class was generated by the Doctrine ORM. Add your own custom
 * repository methods below.
 */
class CommentRepository extends EntityRepository
{
    /**
     * @return array
     */
    public function getComments(): array
    {
        $qb = $this->createQueryBuilder('comment')
            ->select('comment', 'article')
            ->join('comment.article', 'article')
            ->orderBy('comment.date', 'DESC');

        return $qb->getQuery()->getArrayResult();
    }

    /**
     * @param Article $article
     * @return array
     */
    public function getCommentsByArticle(Article $article): array
    {
        /** @var QueryBuilder $qb */
        $qb = $this->createQueryBuilder('comment')
            ->select('comment.id', 'comment.comment', 'comment.published', 'comment.token', 'article.token as article_token')
            ->join('comment.article', 'article')
            ->where('comment.article = :article')
            ->setParameter('article', $article->getId())
            ->orderBy('comment.date', 'DESC');

        return $qb->getQuery()->getResult();
    }
}
