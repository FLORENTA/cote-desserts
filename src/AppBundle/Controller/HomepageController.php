<?php

namespace AppBundle\Controller;

use AppBundle\Entity\Statistic;
use AppBundle\Event\StatisticEvent;
use AppBundle\Manager\ArticleManager;
use AppBundle\Manager\StatisticManager;
use Psr\Log\LoggerInterface;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Translation\TranslatorInterface;

/**
 * Class DefaultController
 * @package AppBundle\Controller
 */
class HomepageController extends Controller
{
    /**
     * @Route("/", name="homepage", methods={"GET"})
     * @param TranslatorInterface $translator
     * @param EventDispatcherInterface $eventDispatcher
     * @param LoggerInterface $logger
     * @param ArticleManager $articleManager
     * @return Response
     */
    public function index(
        TranslatorInterface $translator,
        EventDispatcherInterface $eventDispatcher,
        LoggerInterface $logger,
        ArticleManager $articleManager
    ): Response
    {
        $statisticEvent = new StatisticEvent('/', Statistic::NAVIGATION_TYPE);

        $eventDispatcher->dispatch(StatisticEvent::APP_BUNDLE_STATISTICS_NEW, $statisticEvent);

        /** @var int $status */
        $status = $statisticEvent->getStatus();

        if ($status === StatisticManager::ERROR) {
            $logger->error('An error occurred when trying to register data', [
                '_method' => __METHOD__,
                '_status' => $status
            ]);
        }

        /** @var array $articles */
        $articles = $articleManager->getArticles();

        if (empty($articles)) {
            $logger->error(sprintf('No article found'), ['_method' => __METHOD__]);

            return new JsonResponse(
                $translator->trans('query.no_article'),
                JsonResponse::HTTP_BAD_REQUEST
            );
        }

        return $this->render('homepage/index.html.twig', [
            'articles' => $articles
        ]);
    }
}
