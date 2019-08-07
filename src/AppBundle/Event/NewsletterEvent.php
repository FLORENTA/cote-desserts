<?php

namespace AppBundle\Event;

use AppBundle\Entity\Article;
use Symfony\Component\EventDispatcher\Event;

/**
 * Class NewsletterEvent
 * @package AppBundle\Event
 */
class NewsletterEvent extends Event
{
    const APP_BUNDLE_NEWSLETTER_CONFIRM_SUBSCRIPTION = 'app_bundle.newsletter.confirm_subscription';
    const APP_BUNDLE_NEWSLETTER_CONFIRM_UNSUBSCRIPTION = 'app_bundle.newsletter.confirm_unsubscription';
    const APP_BUNDLE_NEWSLETTER_SEND_NEWSLETTER = 'app_bundle.newsletter.send_newsletter';

    /** @var Article|null */
    private $article;

    /** @var string|null */
    private $email;

    /** @var string|null $token */
    private $token;

    /** @var int $status */
    private $status;

    /**
     * NewsletterEvent constructor.
     * @param Article|null $article
     * @param string|null $email
     * @param string|null $token
     */
    public function __construct(
        Article $article = null,
        string $email = null,
        string $token = null
    )
    {
        $this->article = $article;
        $this->email = $email;
        $this->token = $token;
    }

    /**
     * @return Article|null
     */
    public function getArticle(): ?Article
    {
        return $this->article;
    }

    /**
     * @return string|null
     */
    public function getEmail(): ?string
    {
        return $this->email;
    }

    /**
     * @return string|null
     */
    public function getToken(): ?string
    {
        return $this->token;
    }

    /**
     * @param int $status
     * @return NewsletterEvent
     */
    public function setStatus(int $status): NewsletterEvent
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