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
class NewsletterService
{
    const NO_ERROR = 0;
    const ERROR = 1;

    /** @var \Swift_Mailer $mailer */
    private $mailer;

    /** @var EngineInterface $templating */
    private $templating;

    /** @var RouterInterface $router */
    private $router;

    /** @var LoggerInterface $logger */
    private $logger;

    /** @var string $mailerUser */
    private $mailerUser;

    /** @var string $imagesDirectory */
    private $imagesDirectory;

    /** @var TranslatorInterface $translator */
    private $translator;

    /** @var NewsletterManager $newsletterManager */
    private $newsletterManager;

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
     */
    public function __construct(
        Swift_Mailer $mailer,
        EngineInterface $engine,
        RouterInterface $router,
        LoggerInterface $logger,
        TranslatorInterface $translator,
        NewsletterManager $newsletterManager,
        $mailerUser,
        $imagesDirectory
    )
    {
        $this->mailer = $mailer;
        $this->templating = $engine;
        $this->router = $router;
        $this->logger = $logger;
        $this->mailerUser = $mailerUser;
        $this->imagesDirectory = $imagesDirectory;
        $this->translator = $translator;
        $this->newsletterManager = $newsletterManager;
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

            $message->setSubject($this->translator->trans('newsletter.subject') . $article->getTitle())
                ->setTo($subscriber->getEmail())
                ->setFrom($this->mailerUser)
                ->setBody(
                    $this->templating->render('newsletter/newsletter.html.twig', [
                        'article' => $article,
                        'content' => $image->getContent(),
                        'src' => $src ?? null,
                        'title' => $image->getTitle(),
                        'articleUrl' => $articleUrl,
                        'unsubscribeUrl' => $unsubscribeUrl
                    ]), 'text/html', 'UTF-8');

            $this->mailer->send($message);
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

        /** @var Swift_Message $message */
        $message = new Swift_Message();

        $message->setSubject($this->translator->trans('subscription.title'))
            ->setTo($email)
            ->setFrom($this->mailerUser)
            ->setBody(
                $this->templating->render("newsletter/confirm_subscription.html.twig", ['unsubscribeUrl' => $unsubscribeUrl]),
                'text/html',
                'UTF-8'
            );

        $this->mailer->send($message);

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

        /** @var Swift_Message $message */
        $message = new Swift_Message();

        $message->setSubject($this->translator->trans('unsubscription.title'))
            ->setTo($email)
            ->setFrom($this->mailerUser)
            ->setBody(
                $this->templating->render('newsletter/confirm_unsubscription.html.twig'),
                'text/html',
                'UTF-8'
            );

        $this->mailer->send($message);

        $this->logger->info(sprintf('Sending unsubscription confirmation to %s.', $email), [
            '_method' => __METHOD__
        ]);

        return self::NO_ERROR;
    }
}