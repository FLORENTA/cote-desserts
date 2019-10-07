<?php

namespace AppBundle\Service;

use AppBundle\Manager\NewsletterManager;
use Psr\Log\LoggerInterface;
use Swift_Image;
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

    /** @var EngineInterface $templating */
    protected $templating;

    /** @var RouterInterface $router */
    protected $router;

    /** @var LoggerInterface $logger */
    protected $logger;

    /** @var TranslatorInterface $translator */
    protected $translator;

    /** @var string $imagesDirectory */
    protected $imagesDirectory;

    /** @var NewsletterManager $newsletterManager */
    protected $newsletterManager;

    /** @var string $emailToInform */
    protected $emailToInform;

    /**
     * AbstractMailService constructor.
     * @param Swift_Mailer $mailer
     * @param EngineInterface $engine
     * @param RouterInterface $router
     * @param LoggerInterface $logger
     * @param TranslatorInterface $translator
     * @param NewsletterManager $newsletterManager
     * @param string $mailerUser
     * @param string $emailToInform
     * @param string $imagesDirectory
     */
    public function __construct(
        Swift_Mailer $mailer,
        EngineInterface $engine,
        RouterInterface $router,
        LoggerInterface $logger,
        TranslatorInterface $translator,
        NewsletterManager $newsletterManager,
        string $mailerUser,
        string $emailToInform,
        string $imagesDirectory
    )
    {
        $this->mailer = $mailer;
        $this->mailerUser = $mailerUser;
        $this->templating = $engine;
        $this->router = $router;
        $this->logger = $logger;
        $this->translator = $translator;
        $this->imagesDirectory = $imagesDirectory;
        $this->newsletterManager = $newsletterManager;
        $this->emailToInform = $emailToInform;
    }

    /**
     * @param string $subject
     * @param string $to
     * @param string $body
     * @param string|null $src
     */
    protected function sendMessage(
        string $subject,
        string $to,
        string $body,
        string $src = null
    )
    {
        $message = new Swift_Message();
        $message->setSubject($subject)
            ->setTo($to)
            ->setFrom($this->mailerUser)
            ->setBody($body, 'text/html', 'UTF-8');

        if (null !== $src) {
            $message->embed(Swift_Image::fromPath($src));
        }

        $this->mailer->send($message);
    }
}