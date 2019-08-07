<?php

namespace AppBundle\Manager;

use AppBundle\Entity\Contact;
use AppBundle\Repository\ContactRepository;
use Doctrine\ORM\EntityManagerInterface;

/**
 * Class ContactManager
 * @package AppBundle\Manager
 */
class ContactManager
{
    /** @var ContactRepository $contactRepository */
    private $contactRepository;

    /**
     * ContactManager constructor.
     * @param EntityManagerInterface $entityManager
     */
    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->contactRepository = $entityManager->getRepository(Contact::class);
    }

    /**
     * @return array
     */
    public function getContacts(): array
    {
        return $this->contactRepository->getContacts();
    }

    /**
     * @param string $token
     * @return Contact|null
     */
    public function getContactByToken(string $token): ?Contact
    {
        return $this->contactRepository->findOneBy(['token' => $token]);
    }
}