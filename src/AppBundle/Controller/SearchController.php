<?php

namespace AppBundle\Controller;

use AppBundle\Entity\Article;
use AppBundle\Entity\Statistic;
use AppBundle\Event\StatisticEvent;
use AppBundle\Form\SearchType;
use AppBundle\Manager\StatisticManager;
use AppBundle\Model\SearchModel;
use AppBundle\Manager\ArticleManager;
use AppBundle\Service\Serializor;
use Psr\Log\LoggerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Routing\RouterInterface;
use Symfony\Component\Translation\TranslatorInterface;
use Exception;

/**
 * Class SearchController
 * @package AppBundle\Controller
 */
class SearchController extends Controller
{
    /**
     * @Route("/search/fetch-form", name="fetch_search_form", methods={"GET"})
     * @param RouterInterface $router
     * @return JsonResponse
     */
    public function searchForm(RouterInterface $router): JsonResponse
    {
        $searchModel = new SearchModel();
        $form = $this->createForm(SearchType::class, $searchModel, [
            'action' => $router->generate('search_fetch_results')
        ]);

        return new JsonResponse(
            $this->renderView('form/search_form.html.twig', [
                'form' => $form->createView()
            ]),
            JsonResponse::HTTP_OK
        );
    }

    /**
     * Function to get Articles by keyword
     *
     * @Route("/search/fetch-results", name="search_fetch_results", methods={"POST"})
     * @param Request $request
     * @param EventDispatcherInterface $eventDispatcher
     * @param ArticleManager $articleManager
     * @param LoggerInterface $logger
     * @param Serializor $serializor
     * @param TranslatorInterface $translator
     * @return JsonResponse
     */
    public function search(
        Request $request,
        EventDispatcherInterface $eventDispatcher,
        ArticleManager $articleManager,
        LoggerInterface $logger,
        Serializor $serializor,
        TranslatorInterface $translator
    ): JsonResponse
    {
        $searchModel = new SearchModel();
        $form = $this->createForm(SearchType::class, $searchModel);

        $form->handleRequest($request);

        /** @var string|null $search */
        $search = $searchModel->getSearch();

        if (null === $search) {
            $logger->error('Search is null', ['_method' => __METHOD__]);

            return new JsonResponse(
                $translator->trans('query.no_suggestion'),
                JsonResponse::HTTP_BAD_REQUEST
            );
        }

        $statisticEvent = new StatisticEvent($search, Statistic::SEARCH_TYPE);
        $eventDispatcher->dispatch(StatisticEvent::APP_BUNDLE_STATISTICS_NEW, $statisticEvent);

        if ($statisticEvent->getStatus() === StatisticManager::ERROR) {
            $logger->error('An error occurred when trying to save user search', [
                '_method' => __METHOD__
            ]);
        }

        /** @var Article|null $article */
        $article = $articleManager->getArticleByTitle($search);

        if ($article instanceof Article) {
            try {
                return new JsonResponse($serializor->getSerializer()->normalize($article, 'json', [
                    'groups' => ['article']
                ]), JsonResponse::HTTP_OK);
            } catch (Exception $exception) {
                $logger->error($exception->getMessage(), ['_method' => __METHOD__]);

                return new JsonResponse(
                    $translator->trans('query.no_suggestion'),
                    JsonResponse::HTTP_BAD_REQUEST
                );
            }
        }

        $logger->warning(sprintf('No article title matching %s.', $search), [
            '_method' => __METHOD__
        ]);

        /** @var array $titles */
        $titles = $articleManager->getTitlesByKeyword($search);

        if (empty($titles)) {
            $logger->warning(sprintf('No article found for search %s', $search), [
                '_method' => __METHOD__
            ]);

            return new JsonResponse(
                $translator->trans('query.no_suggestion'),
                JsonResponse::HTTP_BAD_REQUEST
            );
        }

        return new JsonResponse($this->renderView('search/results.html.twig', [
            'results' => $titles
        ]), JsonResponse::HTTP_OK);
    }
}