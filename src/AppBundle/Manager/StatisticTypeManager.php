<?php

namespace AppBundle\Manager;

use AppBundle\Entity\StatisticType;
use AppBundle\Repository\StatisticTypeRepository;
use Doctrine\ORM\EntityManagerInterface;

/**
 * Class StatisticTypeManager
 * @package AppBundle\Manager
 */
class StatisticTypeManager
{
    /** @var StatisticTypeRepository $statisticTypeRepository */
    private $statisticTypeRepository;

    /**
     * StatisticTypeManager constructor.
     * @param EntityManagerInterface $entityManager
     */
    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->statisticTypeRepository = $entityManager->getRepository(StatisticType::class);
    }

    /**
     * @param string $type
     * @return StatisticType|null
     */
    public function getStatisticType(string $type): ?StatisticType
    {
        return $this->statisticTypeRepository->findOneBy(['type' => $type]);
    }
}