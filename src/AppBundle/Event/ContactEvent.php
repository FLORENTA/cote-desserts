<?php

namespace AppBundle\Event;

use AppBundle\Entity\Contact;
use Symfony\Component\EventDispatcher\Event;

/**
 * Class ContactEvent
 * @package AppBundle\Event
 */
class ContactEvent extends Event
{
    const APP_BUNDLE_NEW_CONTACT = 'app_bundle.contact.new';

    /** @var Contact $contact */
    private $contact;

    /** @var int $status */
    private $status;

    /**
     * ContactEvent constructor.
     * @param Contact|null $contact
     */
    public function __construct(Contact $contact = null)
    {
        $this->contact = $contact;
    }

    /**
     * @return Contact
     */
    public function getContact(): Contact
    {
        return $this->contact;
    }

    /**
     * @param int $status
     * @return ContactEvent
     */
    public function setStatus(int $status): ContactEvent
    {
        $this->status = $status;

        return $this;
    }

    /**
     * @return int
     */
    public function getStatus()
    {
        return $this->status;
    }
}