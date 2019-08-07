<?php

namespace Tests\AppBundle\Repository;

use AppBundle\Entity\Statistic;
use AppBundle\Repository\StatisticRepository;
use Doctrine\ORM\NonUniqueResultException;
use Doctrine\ORM\NoResultException;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;

/**
 * Class NewsletterRepositoryTest
 * @package Tests\AppBundle\Repository
 */
class StatisticRepositoryTest extends KernelTestCase
{
    /** @var StatisticRepository $statisticRepository */
    private $statisticRepository;

    public function setUp()
    {
        $kernel = $this->bootKernel();
        $container = $kernel->getContainer();
        $this->statisticRepository = $container->get('doctrine.orm.entity_manager')->getRepository(Statistic::class);
    }

    // Function to get Admin test related page data depending on input received
    public function testGetStats(): void
    {
        $result = $this->statisticRepository->getStats('navigation', false);
        $this->assertCount(3, $result);

        $result = $this->statisticRepository->getStats('navigation', true);
        $this->assertCount(1, $result);

        $result = $this->statisticRepository->getStats('search', false);
        $this->assertEquals('tart', $result[0]['data'] ?? null);
    }

    // Other statistic repository methods tested with tests\AppBundle\Service\StatisticServiceTest.php
}