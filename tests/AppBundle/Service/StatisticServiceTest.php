<?php

namespace Tests\AppBundle\Service;

use AppBundle\Service\StatisticService;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;

class StatisticServiceTest extends KernelTestCase
{
    /** @var StatisticService $statisticService */
    private $statisticService;

    public function setUp(): void
    {
        $kernel = $this->bootKernel();
        $container = $kernel->getContainer();
        $this->statisticService = $container->get(StatisticService::class);
    }

    public function testGetNumberOfVisitedPages(): void
    {
        $result = $this->statisticService->getNumberOfVisitedPages();
        $this->assertEquals(3, $result);
    }

    // Getting the number of unique visitors
    public function testGetSubscribers(): void
    {
        /** @var int $result */
        $result = $this->statisticService->getNumberOfUniqueVisitors();
        $this->assertEquals(2, $result);
    }
}