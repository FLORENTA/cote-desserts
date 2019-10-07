<?php

namespace AppBundle\Service;

use AppBundle\Entity\Contact;

/**
 * Class ContactService
 * @package AppBundle\Service
 */
class ContactService extends AbstractMailService
{
    /**
     * @param Contact $contact
     * @return int
     */
    public function notify(Contact $contact): int
    {
        if (null === $contact->getMessage()) {
            return self::ERROR;
        }

        $this->sendMessage(
            $this->translator->trans('contact.notification.subject', [
                '%email%' => $contact->getEmail()
            ]),
            $this->emailToInform,
            $contact->getMessage()
        );

        return self::NO_ERROR;
    }
}