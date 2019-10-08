<?php

namespace AppBundle\Event;

use AppBundle\Entity\Comment;
use Symfony\Component\EventDispatcher\Event;

/**
 * Class CommentEvent
 * @package AppBundle\Event
 */
final class CommentEvent extends Event
{
    const APP_BUNDLE_NEW_COMMENT = 'app_bundle.comment.new';

    /** @var Comment|null $comment */
    private $comment;

    /** @var int $status */
    private $status;

    /**
     * CommentEvent constructor.
     * @param Comment|null $comment
     */
    public function __construct(Comment $comment = null)
    {
        $this->comment = $comment;
    }

    /**
     * @return Comment
     */
    public function getComment(): Comment
    {
        return $this->comment;
    }

    /**
     * @param int $status
     * @return CommentEvent
     */
    public function setStatus(int $status): CommentEvent
    {
        $this->status = $status;

        return $this;
    }

    /**
     * @return int
     */
    public function getStatus(): int
    {
        return $this->status;
    }
}