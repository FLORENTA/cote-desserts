<?php

namespace AppBundle\Controller\Rest;

use AppBundle\Entity\Article;
use AppBundle\Entity\Statistic;
use AppBundle\Event\StatisticEvent;
use AppBundle\Form\SearchType;
use AppBundle\Manager\ArticleManager;
use AppBundle\Manager\StatisticManager;
use AppBundle\Model\SearchModel;
use AppBundle\Service\Serializor;
use Exception;
use FOS\RestBundle\Controller\AbstractFOSRestController;
use FOS\RestBundle\Controller\Annotations as Rest;
use FOS\RestBundle\View\View;
use Psr\Log\LoggerInterface;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Translation\TranslatorInterface;

class SearchController extends AbstractFOSRestController
{
    /** @var ArticleManager $articleManager */
    private $articleManager;

    /** @var EventDispatcherInterface $eventDispatcher */
    private $eventDispatcher;

    /** @var LoggerInterface $logger */
    private $logger;

    /** @var TranslatorInterface $translator */
    private $translator;

    /**
     * SearchController constructor.
     * @param ArticleManager $articleManager
     * @param EventDispatcherInterface $eventDispatcher
     * @param LoggerInterface $logger
     * @param TranslatorInterface $translator
     */
    public function __construct(
        ArticleManager $articleManager,
        EventDispatcherInterface $eventDispatcher,
        LoggerInterface $logger,
        TranslatorInterface$translator
    )
    {
        $this->articleManager = $articleManager;
        $this->eventDispatcher = $eventDispatcher;
        $this->logger = $logger;
        $this->translator = $translator;
    }
    /**
     * Function to get Articles by keyword
     *
     * @Rest\Post()
     * @param Request $request
     * @Rest\View(serializerGroups={"article"}, serializerEnableMaxDepthChecks=true)
     * @return View|Response
     */
    public function handleSearchAction(Request $request)
    {
        $searchModel = new SearchModel();
        $form = $this->createForm(SearchType::class, $searchModel);

        $form->handleRequest($request);

        /** @var string|null $search */
        $search = $searchModel->getSearch();

        if (null === $search) {
            $this->logger->error('Search is null', ['_method' => __METHOD__]);

            return $this->view(
                $this->translator->trans('query.no_suggestion'),
                JsonResponse::HTTP_BAD_REQUEST
            );
        }

        $statisticEvent = new StatisticEvent($search, Statistic::SEARCH_TYPE);
        $this->eventDispatcher->dispatch(StatisticEvent::APP_BUNDLE_STATISTICS_NEW, $statisticEvent);

        if ($statisticEvent->getStatus() === StatisticManager::ERROR) {
            $this->logger->error('An error occurred when trying to save user search', [
                '_method' => __METHOD__
            ]);
        }

        /** @var Article|null $article */
        $article = $this->articleManager->getArticleByTitle($search);

        if ($article instanceof Article) {
            try {
                return $this->handleView($this->view(['article' => $article], Response::HTTP_OK));
            } catch (Exception $exception) {
                $this->logger->error($exception->getMessage(), ['_method' => __METHOD__]);

                return $this->view(
                    $this->translator->trans('query.no_suggestion'),
                    JsonResponse::HTTP_BAD_REQUEST
                );
            }
        }

        $this->logger->warning(sprintf('No article title matching %s.', $search), [
            '_method' => __METHOD__
        ]);

        /** @var array $titles */
        $titles = $this->articleManager->getTitlesByKeyword($search);

        if (empty($titles)) {
            $this->logger->warning(sprintf('No article found for search %s', $search), [
                '_method' => __METHOD__
            ]);

            return $this->view(
                $this->translator->trans('query.no_suggestion'),
                JsonResponse::HTTP_BAD_REQUEST
            );
        }

        return $this->view(['titles' => $titles], JsonResponse::HTTP_OK);
    }
}