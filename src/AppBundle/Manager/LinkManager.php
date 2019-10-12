<?php

namespace AppBundle\Manager;

use AppBundle\Entity\Link;
use Doctrine\ORM\EntityManagerInterface;

/**
 * Class LinkManager
 * @package AppBundle\Manager
 */
class LinkManager
{
    /** @var EntityManagerInterface $em */
    private $em;

    /**
     * LinkManager constructor.
     * @param EntityManagerInterface $entityManager
     */
    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->em = $entityManager;
    }

    /**
     * @param Link $link
     */
    public function createLink(Link $link): void
    {
        $this->em->persist($link);
        $this->em->flush();
    }

    /**
     * @param Link $link
     */
    public function deleteLink(Link $link): void
    {
        $this->em->remove($link);
        $this->em->flush();
    }
}