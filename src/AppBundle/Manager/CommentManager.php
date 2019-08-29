<?php

namespace AppBundle\Manager;

use AppBundle\Entity\Article;
use AppBundle\Entity\Comment;
use AppBundle\Repository\CommentRepository;
use DateTime;
use Doctrine\ORM\EntityManagerInterface;
use Exception;
use Psr\Log\LoggerInterface;

/**
 * Class CommentManager
 * @package AppBundle\Manager
 */
class CommentManager
{
    const NO_ERROR = 0;
    const ERROR = -1;

    /** @var EntityManagerInterface $em */
    private $em;

    /** @var CommentRepository $commentRepository */
    private $commentRepository;

    /** @var LoggerInterface $logger */
    private $logger;

    /**
     * CommentManager constructor.
     * @param EntityManagerInterface $entityManager
     * @param LoggerInterface $logger
     */
    public function __construct(EntityManagerInterface $entityManager, LoggerInterface $logger)
    {
        $this->em = $entityManager;
        $this->commentRepository = $entityManager->getRepository(Comment::class);
        $this->logger = $logger;
    }

    /**
     * @return array
     */
    public function getComments(): array
    {
        return $this->commentRepository->getComments();
    }

    /**
     * @param string $token
     * @return Comment|null
     */
    public function getCommentByToken(string $token): ?Comment
    {
        return $this->commentRepository->findOneBy(['token' => $token]);
    }

    /**
     * @param Comment $comment
     * @param Article $article
     * @return int
     */
    public function createComment(Comment $comment, Article $article): int
    {
        try {
            $comment
                ->setArticle($article)
                ->setDate(new DateTime())
                ->setPublished(false)
                ->setToken(md5(uniqid()));

            $article->addComment($comment);

            $this->em->persist($comment);
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
     * @param Comment $comment
     */
    public function deleteComment(Comment $comment): void
    {
        $this->em->remove($comment);
        $this->em->flush();
    }


    /**
     * @param Article $article
     * @return array
     */
    public function getCommentsByArticle(Article $article): array
    {
        return $this->commentRepository->getCommentsByArticle($article);
    }
}