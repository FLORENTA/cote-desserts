<?php

namespace AppBundle\Service;

use AppBundle\Entity\Comment;
use Swift_Mailer;
use Swift_Message;
use Symfony\Component\Translation\TranslatorInterface;

/**
 * Class CommentService
 * @package AppBundle\Service
 */
class CommentService
{
    const NO_ERROR = 0;
    const ERROR = 1;

    /** @var TranslatorInterface $translator */
    private $translator;

    /** @var Swift_Mailer $mailer */
    private $mailer;

    /** @var string $mailerUser */
    private $mailerUser;

    /** @var string $emailToInform */
    private $emailToInform;

    /**
     * CommentService constructor.
     * @param TranslatorInterface $translator
     * @param Swift_Mailer $swift_Mailer
     * @param $mailerUser
     * @param $emailToInform
     */
    public function __construct(
        TranslatorInterface $translator,
        Swift_Mailer $swift_Mailer,
        $mailerUser,
        $emailToInform
    )
    {
        $this->translator = $translator;
        $this->mailer = $swift_Mailer;
        $this->mailerUser = $mailerUser;
        $this->emailToInform = $emailToInform;
    }

    /**
     * @param Comment $comment
     * @return int
     */
    public function notify(Comment $comment): int
    {
        $message = new Swift_Message();
        $message->setSubject($this->translator->trans('comment.subject'))
            ->setFrom($this->mailerUser)
            ->setTo($this->emailToInform)
            ->setBody(
                '<p>' . $comment->getUsername() . ' [ ' . $comment->getEmail() . ' ] ' . '<br>' .
                      $comment->getComment() .
                    '</p>'
            );

        $this->mailer->send($message);

        return self::NO_ERROR;
    }
}