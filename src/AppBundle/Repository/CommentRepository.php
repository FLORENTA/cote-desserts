<?php

namespace AppBundle\Repository;

use AppBundle\Entity\Article;
use AppBundle\Entity\Comment;
use Doctrine\Common\Collections\Criteria;
use Doctrine\ORM\EntityRepository;
use Doctrine\ORM\Mapping\ClassMetadataInfo;

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
}
