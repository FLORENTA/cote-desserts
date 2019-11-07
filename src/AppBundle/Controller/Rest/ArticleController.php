<?php

namespace AppBundle\Controller\Rest;

use AppBundle\Entity\Article;
use AppBundle\Entity\Category;
use AppBundle\Entity\Pdf;
use AppBundle\Entity\Statistic;
use AppBundle\Event\NewsletterEvent;
use AppBundle\Event\StatisticEvent;
use AppBundle\Form\ArticleType;
use AppBundle\Form\CategoryType;
use AppBundle\Manager\ArticleManager;
use AppBundle\Manager\CategoryManager;
use AppBundle\Manager\StatisticManager;
use AppBundle\Service\NewsletterService;
use Doctrine\Common\Collections\ArrayCollection;
use Exception;
use FOS\RestBundle\Controller\AbstractFOSRestController;
use FOS\RestBundle\Controller\Annotations as Rest;
use FOS\RestBundle\View\View;
use Psr\Log\LoggerInterface;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\RouterInterface;
use Symfony\Component\Translation\TranslatorInterface;

/**
 * Class ArticleController
 * @package AppBundle\Controller\Rest
 */
class ArticleController extends AbstractFOSRestController
{
    /** @var ArticleManager $articleManager */
    private $articleManager;

    /** @var CategoryManager $categoryManager */
    private $categoryManager;

    /** @var LoggerInterface $logger */
    private $logger;

    /** @var TranslatorInterface $translator */
    private $translator;

    /** @var EventDispatcherInterface $eventDispatcher */
    private $eventDispatcher;

    /** @var RouterInterface $router */
    private $router;

    /**
     * ArticleController constructor.
     * @param ArticleManager $articleManager
     * @param CategoryManager $categoryManager
     * @param LoggerInterface $logger
     * @param TranslatorInterface $translator
     * @param EventDispatcherInterface $eventDispatcher
     * @param RouterInterface $router
     */
    public function __construct(
        ArticleManager $articleManager,
        CategoryManager $categoryManager,
        LoggerInterface $logger,
        TranslatorInterface $translator,
        EventDispatcherInterface $eventDispatcher,
        RouterInterface $router
    )
    {
        $this->articleManager = $articleManager;
        $this->categoryManager = $categoryManager;
        $this->logger = $logger;
        $this->translator = $translator;
        $this->eventDispatcher = $eventDispatcher;
        $this->router = $router;
    }

    /**
     * @Rest\Post()
     * @param Request $request
     * @return View
     */
    public function createArticleAction(Request $request): View
    {
        $article = new Article();
        $form = $this->createForm(ArticleType::class, $article);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->articleManager->createArticle($article);

            if ($article->getNewsletter()) {
                $newsletterEvent = new NewsletterEvent($article);

                $this->eventDispatcher->dispatch(
                    NewsletterEvent::APP_BUNDLE_NEWSLETTER_SEND_NEWSLETTER,
                    $newsletterEvent
                );

                if ($newsletterEvent->getStatus() === NewsletterService::ERROR) {
                    $this->logger->error(
                        'An error occurred when trying to send article %s newsletter', [
                        '_method' => __METHOD__,
                        '_args' => $article->getTitle()
                    ]);
                }
            }

            return $this->view($this->translator->trans('article.creation.success', [
                '%title%' => $article->getTitle()
            ]), JsonResponse::HTTP_OK);
        }

        return $this->view($this->translator->trans('generic.form.invalid'), JsonResponse::HTTP_BAD_REQUEST);
    }

    /**
     * @return Response
     */
    public function getArticlesAction(): Response
    {
        /** @var array $articles */
        $articles = $this->articleManager->getArticles();

        return $this->handleView($this->view($articles, JsonResponse::HTTP_OK));
    }

    /**
     * @Rest\View(serializerGroups={"article"}, serializerEnableMaxDepthChecks=true)
     * @param string $slug
     * @return View|Response
     */
    public function getArticleAction(string $slug)
    {
        /** @var Article|null $article */
        $article = $this->articleManager->getArticleBySlug($slug);

        if (null === $article) {
            $this->logger->error(
                sprintf('No article found for slug %s.', $slug),
                ['_method' => __METHOD__]
            );

            return $this->view($this->translator->trans('article.not_found'), JsonResponse::HTTP_BAD_REQUEST);
        }

        return $this->handleView($this->view($article, JsonResponse::HTTP_OK));
    }

    /**
     * @Rest\Post()
     * @param Request $request
     * @param string $token
     * @return View
     */
    public function updateArticleAction(Request $request, string $token): View
    {
        /** @var Article|null $article */
        $article = $this->getArticleByToken($token);

        if (null === $article) {
            $this->logger->warning(sprintf('No article found for token %s', $token), [
                '_method' => __METHOD__
            ]);

            return $this->view($this->translator->trans('query.no_article'), JsonResponse::HTTP_BAD_REQUEST);
        }

        $form = $this->createForm(ArticleType::class, $article);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            /** @var int $status */
            $status = $this->articleManager->updateArticle($article);

            if ($status === ArticleManager::ERROR) {
                $this->logger->error(sprintf('Article %s could not be updated.', $article->getTitle()), [
                    '_method' => __METHOD__
                ]);

                return $this->view($this->translator->trans('article.update.failure', [
                    '%title%' => $article->getTitle()
                ]), JsonResponse::HTTP_OK);
            }

            $this->logger->info(sprintf('Article %s has been updated.', $article->getTitle()), [
                '_method' => __METHOD__
            ]);

            $data['alert'] = $this->translator->trans('article.update.success', [
                '%title%' => $article->getTitle()
            ]);

            /** @var Article|null $article */
            $article = $this->getArticleByToken($token);

            /** @var Pdf|null $pdf */
            $pdf = $article->getPdf();

            if (null !== $pdf) {
                /** @var string|null $src */
                $src = $pdf->getSrc();

                if (null !== $src) {
                    $data['delete_pdf_url'] = $this->router->generate('delete_pdf', [
                        'src' => $src
                    ]);
                }
            }

            return $this->view($data, JsonResponse::HTTP_OK);
        }

        return $this->view($this->translator->trans('generic.form.invalid'), JsonResponse::HTTP_BAD_REQUEST);
    }

    /**
     * @param string $token
     * @return View
     */
    public function deleteArticleAction(string $token): View
    {
        /** @var Article|null $article */
        $article = $this->getArticleByToken($token);

        if (null === $article) {
            $this->logger->error(
                sprintf('No article found for token %s.', $token),
                ['_method' => __METHOD__]
            );

            return View::create($this->translator->trans('article.not_found'), JsonResponse::HTTP_BAD_REQUEST);
        }

        $this->articleManager->deleteArticle($article);

        $this->logger->info(
            sprintf('Article %s has been successfully removed.', $article->getTitle()),
            ['_method' => __METHOD__]
        );

        return $this->view($this->translator->trans('article.deletion.success', [
            '%title%' => $article->getTitle()
        ]), JsonResponse::HTTP_OK);
    }

    /**
     * @Rest\Get("/articles/categories/{category}")
     * @Rest\View(serializerGroups={"article"}, serializerEnableMaxDepthChecks=true)
     * @param string $category
     * @return Response|View
     */
    public function getArticlesForCategoryAction(string $category)
    {
        /** @var Category|null $category */
        $category = $this->categoryManager->getCategoryByName($category);

        if (null === $category) {
            $this->logger->warning('No category found.', ['_method' => __METHOD__]);

            return $this->view($this->translator->trans('query.no_category'), JsonResponse::HTTP_BAD_REQUEST);
        }

        /** @var array $articles */
        $articles = $this->articleManager->getArticlesByCategory([$category]);

        if (empty($articles)) {
            $this->logger->warning('No article found.', ['_method' => __METHOD__]);

            return $this->view($this->translator->trans('query.no_article'), JsonResponse::HTTP_BAD_REQUEST);
        }

        return $this->handleView($this->view($articles, JsonResponse::HTTP_OK));
    }

    /**
     * Function to find articles linked to target categories
     * @Rest\Post("/articles/categories")
     * @param Request $request
     * @return Response|View
     */
    public function getArticlesByCategoriesAction(Request $request)
    {
        $category = new Category();
        $form = $this->createForm(CategoryType::class, $category);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            /** @var ArrayCollection $c */
            $c = $category->getCategory();

            /** @var array $categories */
            $categories = $c->toArray();

            /** @var array $result */
            $articles = $this->articleManager->getArticlesByCategory($categories);

            if (empty($articles)) {
                $this->logger->error(
                    'No article found for category.',
                    ['_method' => __METHOD__, '_args' => serialize($categories)]
                );

                return $this->view(
                    $this->translator->trans('query.no_article'),
                    JsonResponse::HTTP_BAD_REQUEST
                );
            }

            $categories = array_map(function($category) {
                return $category->getCategory();
            }, $categories);

            sort($categories);

            return $this->handleView($this->view([
                'articles' => $articles,
                'categories' => $categories
            ], JsonResponse::HTTP_OK));
        }

        return $this->view(
            $this->translator->trans('generic.form.invalid'),
            JsonResponse::HTTP_BAD_REQUEST
        );
    }

    /**
     * @Rest\View(serializerGroups={"article"}, serializerEnableMaxDepthChecks=true)
     * @return Response
     */
    public function getArticlesNewsletterAction(): Response
    {
        /** @var array $articles */
        $articles = $this->articleManager->getArticlesWithNewsletter();

        return $this->handleView($this->view($articles, JsonResponse::HTTP_OK));
    }

    /**
     * @Rest\Get()
     * @param Request $request
     * @param string $slug
     * @return RedirectResponse
     */
    public function consultArticleAction(Request $request, $slug): RedirectResponse
    {
        $url = $_SERVER['HTTP_HOST'] . "/#/" . "article/$slug";

        if (!preg_match("/^(http|https):\/\//", $url)) {
            $url = 'http://' . $url;
        }

        /** @var bool|string $data */
        $data = substr($request->getRequestUri(), 7);

        if (!empty($data)) {
            $statisticEvent = new StatisticEvent($data, Statistic::NEWSLETTER_TYPE);

            try {
                $this->eventDispatcher->dispatch(
                    StatisticEvent::APP_BUNDLE_STATISTICS_NEW,
                    $statisticEvent
                );

                /** @var int $status */
                $status = $statisticEvent->getStatus();

                if ($status === StatisticManager::ERROR) {
                    $this->logger->error('An error occurred when trying to register data', [
                        '_method' => __METHOD__,
                        '_status' => $status
                    ]);
                }

            } catch (Exception $exception) {
                $this->logger->error($exception->getMessage(), [
                    '_method' => __METHOD__
                ]);
            }
        }

        return new RedirectResponse($url);
    }

    /**
     * @param string $token
     * @return Article|null
     */
    private function getArticleByToken(string $token): ?Article
    {
        return $this->articleManager->getArticleByToken($token);
    }
}
