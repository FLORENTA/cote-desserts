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
    /** @var EntityManagerInterface $em */
    private $em;

    /** @var ContactRepository $contactRepository */
    private $contactRepository;

    /**
     * ContactManager constructor.
     * @param EntityManagerInterface $entityManager
     */
    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->em = $entityManager;
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

    /**
     * @param Contact $contact
     */
    public function createContact(Contact $contact): void
    {
        $this->em->persist($contact);
        $this->em->flush();
    }

    /**
     * @param Contact $contact
     */
    public function removeContact(Contact $contact): void
    {
        $this->em->remove($contact);
        $this->em->flush();
    }
}