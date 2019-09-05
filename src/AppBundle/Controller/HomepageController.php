<?php

namespace AppBundle\Controller;

use AppBundle\Entity\Statistic;
use AppBundle\Event\StatisticEvent;
use AppBundle\Manager\ArticleManager;
use AppBundle\Manager\StatisticManager;
use Psr\Log\LoggerInterface;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Response;

/**
 * Class DefaultController
 * @package AppBundle\Controller
 */
class HomepageController extends Controller
{
    /**
     * @Route("/", name="homepage", methods={"GET"})
     * @param EventDispatcherInterface $eventDispatcher
     * @param ArticleManager $articleManager
     * @param LoggerInterface $logger
     * @return Response
     */
    public function index(
        EventDispatcherInterface $eventDispatcher,
        ArticleManager $articleManager,
        LoggerInterface $logger
    ): Response
    {
        $statisticEvent = new StatisticEvent('/', Statistic::NAVIGATION_TYPE);

        $eventDispatcher->dispatch(
            StatisticEvent::APP_BUNDLE_STATISTICS_NEW,
            $statisticEvent
        );

        /** @var int $status */
        $status = $statisticEvent->getStatus();

        if ($status === StatisticManager::ERROR) {
            $logger->error('An error occurred when trying to register data', [
                '_method' => __METHOD__,
                '_status' => $status
            ]);
        }

        return $this->render('homepage/index.html.twig');
    }
}
