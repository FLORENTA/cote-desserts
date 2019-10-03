<?php

namespace AppBundle\Service;

use AppBundle\Manager\NewsletterManager;
use Psr\Log\LoggerInterface;
use Swift_Mailer;
use Swift_Message;
use Symfony\Component\Routing\RouterInterface;
use Symfony\Component\Templating\EngineInterface;
use Symfony\Component\Translation\TranslatorInterface;

/**
 * Class AbstractMailService
 * @package AppBundle\Service
 */
abstract class AbstractMailService
{
    const NO_ERROR = 0;
    const ERROR = 1;

    /** @var Swift_Mailer $mailer */
    private $mailer;

    /** @var string $mailerUser */
    private $mailerUser;

    /** @var string $emailToInform */
    private $emailToInform;

    /**
     * AbstractMailService constructor.
     * @param Swift_Mailer $mailer
     * @param string $mailerUser
     */
    public function __construct(Swift_Mailer $mailer, string $mailerUser)
    {
        $this->mailer = $mailer;
        $this->mailerUser = $mailerUser;
    }

    /**
     * @param string $subject
     * @param string $to
     * @param string $body
     */
    protected function sendMessage(
        string $subject,
        string $to,
        string $body
    )
    {
        $message = new Swift_Message();
        $message->setSubject($subject)
            ->setTo($to)
            ->setFrom($this->mailerUser)
            ->setBody($body, 'text/html', 'UTF-8');

        $this->mailer->send($message);
    }
}