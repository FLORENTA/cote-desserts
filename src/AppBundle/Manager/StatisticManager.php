<?php

namespace AppBundle\Manager;

use AppBundle\Entity\Article;
use AppBundle\Entity\Statistic;
use AppBundle\Entity\StatisticType;
use AppBundle\Repository\ArticleRepository;
use AppBundle\Repository\StatisticRepository;
use AppBundle\Service\StatisticService;
use Doctrine\ORM\EntityManagerInterface;
use Psr\Log\LoggerInterface;
use Exception;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use DateTime;

/**
 * Class StatisticManager
 * @package AppBundle\Manager
 */
class StatisticManager
{
    const NO_ERROR = 0;
    const ERROR = -1;

    /** @var EntityManagerInterface $em */
    private $em;

    /** @var SessionInterface $session */
    private $session;

    /** @var StatisticRepository $statisticRepository */
    private $statisticRepository;

    /** @var ArticleRepository $articleRepository */
    private $articleRepository;

    /** @var LoggerInterface $logger */
    private $logger;

    /** @var StatisticService $statisticService */
    private $statisticService;

    /** @var StatisticTypeManager $statisticTypeManager */
    private $statisticTypeManager;

    /**
     * StatisticManager constructor.
     * @param EntityManagerInterface $entityManager
     * @param SessionInterface $session
     * @param LoggerInterface $logger
     * @param StatisticService $statisticService
     * @param StatisticTypeManager $statisticTypeManager
     */
    public function __construct(
        EntityManagerInterface $entityManager,
        SessionInterface $session,
        LoggerInterface $logger,
        StatisticService $statisticService,
        StatisticTypeManager $statisticTypeManager
    )
    {
        $this->em = $entityManager;
        $this->session = $session;
        $this->statisticRepository = $entityManager->getRepository(Statistic::class);
        $this->statisticTypeManager = $statisticTypeManager;
        $this->articleRepository = $entityManager->getRepository(Article::class);
        $this->logger = $logger;
        $this->statisticService = $statisticService;
    }

    /**
     * @param StatisticType $statisticType
     * @param bool $bot
     * @param null $startTime
     * @param null $endTime
     * @return array
     */
    public function getStats(
        StatisticType $statisticType,
        $bot = false,
        $startTime = null,
        $endTime = null
    ): array
    {
        return $this->statisticRepository->getStats($statisticType, $bot, $startTime, $endTime);
    }

    /**
     * @param string $data
     * @param string $statisticType
     * @return int
     */
    public function registerData(string $data, string $statisticType): int
    {
        /** @var bool $isBot */
        $isBot = $this->statisticService->checkIfBot();

        /** @var StatisticType|null $statisticType */
        $statisticType = $this->statisticTypeManager->getStatisticType($statisticType);

        if (null === $statisticType) {
            $this->logger->error(sprintf('Unknown statistic type %s', $statisticType), [
                '_method' => __METHOD__
            ]);

            return self::ERROR;
        }

        try {
            /** @var Statistic $statistic */
            $statistic = (new Statistic())
                ->setData($data)
                ->setDate(new DateTime())
                ->setBot($isBot)
                ->setStatisticType($statisticType)
                ->setSessionId($this->session->getId());

            $this->em->persist($statistic);
            $this->em->flush();

            return self::NO_ERROR;
        } catch (Exception $exception) {
            $this->logger->error($exception->getMessage(), [
                '_method' => __METHOD__,
                '_args' => [
                    'data' => $data,
                    'isBot' => $isBot,
                    'type' => $statisticType
                ]
            ]);

            return self::ERROR;
        }
    }

    public function deleteStatistics(): void
    {
        $this->statisticRepository->deleteStatistics();
    }
}