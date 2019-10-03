<?php

namespace AppBundle\EventListener;

use AppBundle\Entity\Comment;
use AppBundle\Entity\Contact;
use AppBundle\Event\CommentEvent;
use AppBundle\Event\ContactEvent;
use AppBundle\Service\CommentService;
use AppBundle\Service\ContactService;

/**
 * Class ContactListener
 * @package AppBundle\EventListener
 */
class ContactListener
{
    /** @var ContactService $contactService */
    private $contactService;

    /**
     * ContactListener constructor.
     * @param ContactService $contactService
     */
    public function __construct(ContactService $contactService)
    {
        $this->contactService = $contactService;
    }

    /**
     * @param ContactEvent $contactEvent
     */
    public function notify(ContactEvent $contactEvent): void
    {
        /** @var Contact $contact */
        $contact = $contactEvent->getContact();
        /** @var int $status */
        $status = $this->contactService->notify($contact);
        $contactEvent->setStatus($status);
    }
}