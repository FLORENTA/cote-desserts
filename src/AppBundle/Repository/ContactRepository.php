<?php

namespace AppBundle\Repository;

use Doctrine\ORM\EntityRepository;

/**
 * ContactRepository
 *
 * This class was generated by the Doctrine ORM. Add your own custom
 * repository methods below.
 */
class ContactRepository extends EntityRepository
{
    /**
     * @return array
     */
    public function getContacts(): array
    {
        $qb = $this->createQueryBuilder('contact')
            ->orderBy('contact.date', 'DESC');

        return $qb->getQuery()->getResult();
    }
}
