<?php

namespace AppBundle\Service;

use AppBundle\Entity\Comment;

/**
 * Class CommentService
 * @package AppBundle\Service
 */
class CommentService extends AbstractMailService
{
    /**
     * @param Comment $comment
     * @return int
     */
    public function notify(Comment $comment): int
    {
        if (null === $comment->getComment()) {
            return self::ERROR;
        }

        $this->sendMessage(
            $this->translator->trans('comment.notification.subject', [
                '%username%' => $comment->getUsername(),
                '%email%' => $comment->getEmail()
            ]),
            $this->emailToInform,
            sprintf('Article : %s', $comment->getArticle()->getTitle()) .  '<br>' .
            $comment->getComment()
        );

        return self::NO_ERROR;
    }
}