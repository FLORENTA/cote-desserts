<?php

namespace AppBundle\Service;

use AppBundle\Entity\Article;
use AppBundle\Entity\Image;
use AppBundle\Entity\Newsletter;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Swift_Message;

/**
 * Class NewsletterService
 * @package AppBundle\Service
 */
class NewsletterService extends AbstractMailService
{
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

            /** @var Image|null $image */
            $image = $article->getImages()->get(0);

            $src = null;

            if (null !== $image) {
                /** @var string $src */
                $src = $this->imagesDirectory . '/' . $image->getSrc();
            }

            $subject = $this->translator->trans('newsletter.subject') . $article->getTitle();
            $to = $subscriber->getEmail();
            $body = $this->templating->render('newsletter/newsletter.html.twig', [
                'article' => $article,
                'content' => $image->getContent(),
                'src' => $src,
                'title' => $image->getTitle(),
                'articleUrl' => $articleUrl,
                'unsubscribeUrl' => $unsubscribeUrl
            ]);

            $this->sendMessage($subject, $to, $body, $src);
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