<?php

namespace AppBundle\EventListener;

use AppBundle\Entity\Comment;
use AppBundle\Event\CommentEvent;
use AppBundle\Service\CommentService;

/**
 * Class CommentListener
 * @package AppBundle\EventListener
 */
class CommentListener
{
    /** @var CommentService $commentService */
    private $commentService;

    /**
     * CommentListener constructor.
     * @param CommentService $commentService
     */
    public function __construct(CommentService $commentService)
    {
        $this->commentService = $commentService;
    }

    /**
     * @param CommentEvent $commentEvent
     */
    public function notify(CommentEvent $commentEvent): void
    {
        /** @var Comment $comment */
        $comment = $commentEvent->getComment();
        /** @var int $status */
        $status = $this->commentService->notify($comment);
        $commentEvent->setStatus($status);
    }
}