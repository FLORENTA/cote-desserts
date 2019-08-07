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
    /** @var LegalRepository $legalRepository */
    private $legalRepository;

    /**
     * LegalManager constructor.
     * @param EntityManagerInterface $entityManager
     */
    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->legalRepository = $entityManager->getRepository(Legal::class);
    }

    /**
     * @return Legal|null
     */
    public function getLegalMentions(): ?Legal
    {
        return $this->legalRepository->findOneBy([]);
    }
}