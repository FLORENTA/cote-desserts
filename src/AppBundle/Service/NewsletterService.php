<?php

namespace AppBundle\Service;

use AppBundle\Entity\Article;
use AppBundle\Entity\Image;
use AppBundle\Entity\Newsletter;
use AppBundle\Manager\NewsletterManager;
use Psr\Log\LoggerInterface;
use Swift_Image;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Component\Routing\RouterInterface;
use Symfony\Component\Templating\EngineInterface;
use Symfony\Component\Translation\TranslatorInterface;
use Swift_Message;
use Swift_Mailer;

/**
 * Class NewsletterService
 * @package AppBundle\Service
 */
class NewsletterService extends AbstractMailService
{
    /** @var EngineInterface $templating */
    private $templating;

    /** @var RouterInterface $router */
    private $router;

    /** @var LoggerInterface $logger */
    private $logger;

    /** @var TranslatorInterface $translator */
    private $translator;

    /** @var string $imagesDirectory */
    private $imagesDirectory;

    /** @var NewsletterManager $newsletterManager */
    private $newsletterManager;

    /** @var string $emailToInform */
    private $emailToInform;

    /**
     * MailSender constructor.
     * @param Swift_Mailer $mailer
     * @param EngineInterface $engine
     * @param RouterInterface $router
     * @param LoggerInterface $logger
     * @param TranslatorInterface $translator
     * @param NewsletterManager $newsletterManager
     * @param string $mailerUser
     * @param string $imagesDirectory
     * @param string $emailToInform
     */
    public function __construct(
        Swift_Mailer $mailer,
        EngineInterface $engine,
        RouterInterface $router,
        LoggerInterface $logger,
        TranslatorInterface $translator,
        NewsletterManager $newsletterManager,
        string $mailerUser,
        string $imagesDirectory,
        string $emailToInform
    )
    {
        $this->templating = $engine;
        $this->router = $router;
        $this->logger = $logger;
        $this->translator = $translator;
        $this->imagesDirectory = $imagesDirectory;
        $this->newsletterManager = $newsletterManager;
        $this->emailToInform = $emailToInform;

        parent::__construct($mailer, $mailerUser);
    }

    /**
     * @param Article|null $article
     * @return int
     */
    public function send(?Article $article): int
    {
        if (null === $article) {
            return self::ERROR;
        }

        /** @var Newsletter[] $subscribers */
        $subscribers = $this->newsletterManager->getSubscribers();

        $articleUrl = $this->router->generate('pull_in', [
            'slug' => $article->getSlug()
        ], UrlGeneratorInterface::ABSOLUTE_PATH);

        $articleUrl = substr($articleUrl, 1); // to remove "/"

        $articleUrl = $_SERVER['HTTP_REFERER'] . $articleUrl;

        foreach ($subscribers as $subscriber) {
            /** @var string $unsubscribeUrl */
            $unsubscribeUrl = $this->router->generate('user_unsubscribe', [
                'token' => $subscriber->getToken()
            ], UrlGeneratorInterface::ABSOLUTE_URL);

            /** @var Swift_Message $message */
            $message = new Swift_Message();

            /** @var Image $image */
            $image = $article->getImages()->get(0);

            if (null !== $image) {
                /** @var string $src */
                $src = $message->embed(Swift_Image::fromPath(
                    $this->imagesDirectory . '/' . $image->getSrc())
                );
            }

            $subject = $this->translator->trans('newsletter.subject') . $article->getTitle();
            $to = $subscriber->getEmail();
            $body = $this->templating->render('newsletter/newsletter.html.twig', [
                'article' => $article,
                'content' => $image->getContent(),
                'src' => $src ?? null,
                'title' => $image->getTitle(),
                'articleUrl' => $articleUrl,
                'unsubscribeUrl' => $unsubscribeUrl
            ]);

            $this->sendMessage($subject, $to, $body);
        }

        $this->logger->info(
            sprintf('Sent newsletter for new article %s.', $article->getTitle()),
            ['_method' => __METHOD__]
        );

        return self::NO_ERROR;
    }

    /**
     * @param string|null $email
     * @param string|null $token
     * @return int
     */
    public function confirmSubscription(?string $email, ?string $token): int
    {
        if (null === $email || null === $token) {
            return self::ERROR;
        }

        /** @var string $unsubscribeUrl */
        $unsubscribeUrl = $this->router->generate(
            'user_unsubscribe',
            ['token' => $token],
            UrlGeneratorInterface::ABSOLUTE_URL
        );

        $subject = $this->translator->trans('subscription.title');
        $body = $this->templating->render("newsletter/confirm_subscription.html.twig", [
            'unsubscribeUrl' => $unsubscribeUrl
        ]);

        $this->sendMessage($subject, $email, $body);
        $this->sendMessage(
            $subject,
            $this->emailToInform,
            $this->translator->trans('subscription.notification', [
                '%email%' => $email
            ])
        );

        $this->logger->info(sprintf('Sending subscription confirmation to %s.', $email), [
            '_method' => __METHOD__
        ]);

        return self::NO_ERROR;
    }

    /**
     * @param string|null $email
     * @return int
     */
    public function confirmUnsubscription(?string $email): int
    {
        if (null === $email) {
            return self::ERROR;
        }

        $subject = $this->translator->trans('unsubscription.title');
        $body = $this->templating->render('newsletter/confirm_unsubscription.html.twig');

        $this->sendMessage($subject, $email, $body);
        $this->sendMessage(
            $subject,
            $this->emailToInform,
            $this->translator->trans('unsubscription.notification', [
                '%email%' => $email
            ])
        );

        $this->logger->info(sprintf('Sending unsubscription confirmation to %s.', $email), [
            '_method' => __METHOD__
        ]);

        return self::NO_ERROR;
    }
}