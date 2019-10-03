<?php

namespace AppBundle\Service;

use AppBundle\Entity\Contact;
use Swift_Mailer;
use Symfony\Component\Translation\TranslatorInterface;

/**
 * Class ContactService
 * @package AppBundle\Service
 */
class ContactService extends AbstractMailService
{
    const NO_ERROR = 0;
    const ERROR = 1;

    /** @var TranslatorInterface $translator */
    private $translator;

    /** @var string $emailToInform */
    private $emailToInform;

    /**
     * ContactService constructor.
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
        $this->emailToInform = $emailToInform;

        parent::__construct($swift_Mailer, $mailerUser);
    }

    /**
     * @param Contact $contact
     * @return int
     */
    public function notify(Contact $contact): int
    {
        $subject = $this->translator->trans('contact.subject');
        $to = $this->emailToInform;
        $body = '<p>[ ' . $contact->getEmail() . ' ]<br>' . $contact->getMessage() . '</p>';

        $this->sendMessage($subject, $to, $body);

        return self::NO_ERROR;
    }
}