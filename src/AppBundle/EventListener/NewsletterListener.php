<?php

namespace AppBundle\EventListener;

use AppBundle\Event\NewsletterEvent;
use AppBundle\Service\NewsletterService;

/**
 * Class NewsletterListener
 * @package AppBundle\EventListener
 */
class NewsletterListener
{
    /** @var NewsletterService $newsletterService */
    private $newsletterService;

    /**
     * NewsletterListener constructor.
     * @param NewsletterService $newsletterService
     */
    public function __construct(NewsletterService $newsletterService)
    {
        $this->newsletterService = $newsletterService;
    }

    /**
     * @param NewsletterEvent $newsletterEvent
     */
    public function confirmSubscription(NewsletterEvent $newsletterEvent): void
    {
        /** @var int $status */
        $status = $this->newsletterService->confirmSubscription(
            $newsletterEvent->getEmail(),
            $newsletterEvent->getToken()
        );

        $newsletterEvent->setStatus($status);
    }

    /**
     * @param NewsletterEvent $newsletterEvent
     */
    public function confirmUnsubscription(NewsletterEvent $newsletterEvent): void
    {
        /** @var int $status */
        $status = $this->newsletterService->confirmUnsubscription($newsletterEvent->getEmail());

        $newsletterEvent->setStatus($status);
    }

    /**
     * @param NewsletterEvent $newsletterEvent
     */
    public function sendNewsletter(NewsletterEvent $newsletterEvent): void
    {
        /** @var int $status */
        $status = $this->newsletterService->send($newsletterEvent->getArticle());

        $newsletterEvent->setStatus($status);
    }
}