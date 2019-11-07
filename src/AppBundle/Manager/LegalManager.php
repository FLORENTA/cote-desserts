<?php

namespace AppBundle\Manager;

use AppBundle\Entity\Legal;
use AppBundle\Repository\LegalRepository;
use Doctrine\ORM\EntityManagerInterface;

/**
 * Class LegalManager
 * @package AppBundle\Manager
 */
class LegalManager
{
    /** @var EntityManagerInterface $em */
    private $em;

    /** @var LegalRepository $legalRepository */
    private $legalRepository;

    /**
     * LegalManager constructor.
     * @param EntityManagerInterface $entityManager
     */
    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->em = $entityManager;
        $this->legalRepository = $entityManager->getRepository(Legal::class);
    }

    /**
     * @return Legal|null
     */
    public function getLegalMentions(): ?Legal
    {
        return $this->legalRepository->findOneBy([]);
    }

    /**
     * @param Legal $legal
     */
    public function createLegalMentions(Legal $legal)
    {
        $this->em->persist($legal);
        $this->em->flush();
    }
}