<?php

namespace AppBundle\Controller\Web;

use AppBundle\Entity\Statistic;
use AppBundle\Entity\StatisticType as StatisticTypeEntity;
use AppBundle\Event\StatisticEvent;
use AppBundle\Form\StatisticType;
use AppBundle\Manager\StatisticManager;
use AppBundle\Model\StatisticModel;
use AppBundle\Service\StatisticService;
use DateTime;
use Psr\Log\LoggerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Routing\RouterInterface;
use Symfony\Component\Translation\TranslatorInterface;

/**
 * Class StatisticController
 * @package AppBundle\Controller
 */
class StatisticController extends Controller
{
    /**
     * @Route("/admin/statistics/fetch-form", name="fetch_statistic_form", methods={"GET"})
     * @param RouterInterface $router
     * @return JsonResponse
     */
    public function fetchStatisticForm(RouterInterface $router): JsonResponse
    {
        $form = $this->createForm(StatisticType::class, new StatisticModel(), [
            'action' => $router->generate('admin_fetch_statistics')
        ]);

        return new JsonResponse(
            $this->renderView('form/statistic_form.html.twig', [
                'form' => $form->createView()
            ]),
            JsonResponse::HTTP_OK
        );
    }

    /**
     * Function to return user visited urls based on criteria
     *
     * @Route("/admin/fetch-statistics", name="admin_fetch_statistics", methods={"POST"})
     * @param Request $request
     * @param StatisticManager $statisticManager
     * @param StatisticService $statisticService
     * @param TranslatorInterface $translator
     * @param LoggerInterface $logger
     * @return JsonResponse
     */
    public function AdminStatistics(
        Request $request,
        StatisticManager $statisticManager,
        StatisticService $statisticService,
        TranslatorInterface $translator,
        LoggerInterface $logger
    ): Response
    {
        $statisticModel = new StatisticModel();
        $form = $this->createForm(StatisticType::class, $statisticModel);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            /** @var StatisticTypeEntity $statisticType */
            $statisticType = $statisticModel->getStatisticType();

            /** @var bool $isBot */
            $isBot = $statisticModel->isBot();

            /** @var DateTime|null $startTime */
            $startTime = $statisticModel->getStartTime();

            if ($startTime instanceof DateTime) {
                $startTime = $startTime->format('Y-m-d');
            }

            /** @var DateTime|null $endTime */
            $endTime = $statisticModel->getEndTime();

            if ($endTime instanceof DateTime) {
                $endTime = $endTime->format('Y-m-d');
            }

            /** @var array $statistics */
            $statistics = $statisticManager->getStats($statisticType, $isBot, $startTime, $endTime);

            if (empty($statistics)) {
                $logger->info('No stats found.', [
                    '_method' => __METHOD__,
                    '_args' => [
                        'statistic_type' => $statisticType,
                        'bot' => $isBot,
                        'start_time' => $startTime,
                        'end_time' => $endTime,
                    ]
                ]);

                return new JsonResponse(
                    $translator->trans('query.no_stats'),
                    JsonResponse::HTTP_BAD_REQUEST
                );
            }

            $statisticService->processAdminStatistics($statistics);

            /** @var array $statistics */
            $statistics = $statisticService->gets();

            return new JsonResponse($statistics, JsonResponse::HTTP_OK);
        }

        $logger->warning('The form is not valid.', [
            '_method' => __METHOD__,
        ]);

        return new JsonResponse(
            $translator->trans('generic.form.invalid'),
            JsonResponse::HTTP_BAD_REQUEST
        );
    }

    /**
     * Function to save user visited path
     *
     * @Route("/statistics/new", name="statistic_new")
     * @param EventDispatcherInterface $eventDispatcher
     * @param LoggerInterface $logger
     * @param Request $request
     * @return JsonResponse
     */
    public function addStatistic(
        EventDispatcherInterface $eventDispatcher,
        LoggerInterface $logger,
        Request $request
    ): JsonResponse
    {
        /** @var string $data */
        $data = htmlspecialchars($request->get("data"));

        $statisticEvent = new StatisticEvent(
            $data,
            Statistic::NAVIGATION_TYPE
        );

        $eventDispatcher->dispatch(
            StatisticEvent::APP_BUNDLE_STATISTICS_NEW,
            $statisticEvent
        );

        /** @var int $status */
        $status = $statisticEvent->getStatus();

        if ($status !== StatisticManager::NO_ERROR) {
            $logger->error('An error occurred when trying to register data', [
                '_method' => __METHOD__,
                '_status' => $status
            ]);
        }

        return new JsonResponse($data, JsonResponse::HTTP_OK);
    }

    /**
     * @Route("/statistics/delete", name="delete_statistics", methods={"DELETE"})
     * @param StatisticManager $statisticManager
     * @param TranslatorInterface $translator
     * @return JsonResponse
     */
    public function deleteStatistics(
        StatisticManager $statisticManager,
        TranslatorInterface $translator
    ): JsonResponse
    {
        $statisticManager->deleteStatistics();

        return new JsonResponse(
            $translator->trans('admin.statistics.delete.success'),
            JsonResponse::HTTP_OK
        );
    }
}