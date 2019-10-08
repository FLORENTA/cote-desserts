<?php

namespace AppBundle\Controller;

use AppBundle\Entity\Article;
use AppBundle\Entity\Category;
use AppBundle\Event\NewsletterEvent;
use AppBundle\Event\PdfEvent;
use AppBundle\Form\ArticleType;
use AppBundle\Form\CategoryType;
use AppBundle\Manager\ArticleManager;
use AppBundle\Manager\CategoryManager;
use AppBundle\Service\NewsletterService;
use AppBundle\Service\PdfService;
use AppBundle\Service\Serializor;
use Doctrine\Common\Collections\ArrayCollection;
use Exception;
use Psr\Log\LoggerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Routing\RouterInterface;
use Symfony\Component\Translation\TranslatorInterface;

/**
 * Class ArticleController
 * @package AppBundle\Controller
 */
class ArticleController extends Controller
{
    /**
     * @Route("/admin/article/create/fetch-form", name="fetch_create_article_form", methods={"GET"})
     * @param RouterInterface $router
     * @return JsonResponse
     */
    public function createArticleForm(RouterInterface $router): JsonResponse
    {
        $article = new Article();

        $form = $this->createForm(ArticleType::class, $article, [
            'action' => $router->generate('create_article')
        ]);

        return new JsonResponse(
            $this->renderView('form/article_form.html.twig', [
                'form' => $form->createView()
            ]),
            JsonResponse::HTTP_OK
        );
    }

    /**
     * @Route("/admin/article/create", name="create_article", methods={"POST"})
     * @param Request $request
     * @param TranslatorInterface $translator
     * @param EventDispatcherInterface $eventDispatcher
     * @param LoggerInterface $logger
     * @param ArticleManager $articleManager
     * @return JsonResponse
     */
    public function createArticle(
        Request $request,
        TranslatorInterface $translator,
        EventDispatcherInterface $eventDispatcher,
        LoggerInterface $logger,
        ArticleManager $articleManager
    ): JsonResponse
    {
        $article = new Article();
        $form = $this->createForm(ArticleType::class, $article);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $articleManager->createArticle($article);

            if ($article->getNewsletter()) {
                $newsletterEvent = new NewsletterEvent($article);

                $eventDispatcher->dispatch(
                    NewsletterEvent::APP_BUNDLE_NEWSLETTER_SEND_NEWSLETTER,
                    $newsletterEvent
                );

                if ($newsletterEvent->getStatus() === NewsletterService::ERROR) {
                    $logger->error(
                        'An error occurred when trying to send article %s newsletter', [
                        '_method' => __METHOD__,
                        '_args' => $article->getTitle()
                    ]);
                }
            }
        }

        return new JsonResponse(
            $translator->trans('article.creation.success', [
                '%title%' => $article->getTitle()
            ]),
            JsonResponse::HTTP_OK
        );
    }

    /**
     * @Route("/admin/article/edit/{token}/fetch-form", name="fetch_edit_article_form", methods={"GET"})
     * @param ArticleManager $articleManager
     * @param LoggerInterface $logger
     * @param TranslatorInterface $translator
     * @param RouterInterface $router
     * @param string $token
     * @return JsonResponse
     */
    public function editArticleForm(
        ArticleManager $articleManager,
        LoggerInterface $logger,
        TranslatorInterface $translator,
        RouterInterface $router,
        string $token
    )
    {
        /** @var Article|null $article */
        $article = $articleManager->getArticleByToken($token);

        if (null === $article) {
            $logger->warning(sprintf('No article found for token %s', $token), [
                '_method' => __METHOD__
            ]);

            return new JsonResponse(
                $translator->trans('query.no_article'),
                JsonResponse::HTTP_BAD_REQUEST
            );
        }

        $form = $this->createForm(ArticleType::class, $article, [
            'action' => $router->generate('edit_article', [
                'token' => $token
            ])
        ]);

        return new JsonResponse($this->renderView('form/article_form.html.twig', [
            'form' => $form->createView(),
            'pdf' => $article->getPdf()
        ]), JsonResponse::HTTP_OK);
    }

    /**
     * @Route("/admin/articles/edit/{token}", name="edit_article", methods={"POST"})
     * @param ArticleManager $articleManager
     * @param TranslatorInterface $translator
     * @param Request $request
     * @param LoggerInterface $logger
     * @param string $token
     * @return JsonResponse
     */
    public function editArticle(
        ArticleManager $articleManager,
        TranslatorInterface $translator,
        Request $request,
        LoggerInterface $logger,
        $token
    ): JsonResponse
    {
        /** @var Article|null $article */
        $article = $articleManager->getArticleByToken($token);

        if (null === $article) {
            $logger->warning(sprintf('No article found for token %s', $token), [
                '_method' => __METHOD__
            ]);

            return new JsonResponse(
                $translator->trans('query.no_article'),
                JsonResponse::HTTP_BAD_REQUEST
            );
        }

        $form = $this->createForm(ArticleType::class, $article);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            /** @var int $status */
            $status = $articleManager->updateArticle($article);

            if ($status === ArticleManager::ERROR) {
                $logger->error(sprintf('Article %s could not be updated.', $article->getTitle()), [
                    '_method' => __METHOD__
                ]);

                return new JsonResponse(
                    $translator->trans('article.update.failure', [
                        '%title%' => $article->getTitle()
                    ]),
                    JsonResponse::HTTP_OK
                );
            }

            $logger->info(sprintf('Article %s has been updated.', $article->getTitle()), [
                '_method' => __METHOD__
            ]);

            return new JsonResponse(
                $translator->trans('article.update.success', [
                    '%title%' => $article->getTitle()
                ]),
                JsonResponse::HTTP_OK
            );
        }

        return new JsonResponse(
            $translator->trans('generic.form.invalid'),
            JsonResponse::HTTP_BAD_REQUEST
        );
    }

    /**
     * @Route("/admin/articles/delete/{token}", name="delete_article", methods={"DELETE"})
     * @param ArticleManager $articleManager
     * @param LoggerInterface $logger
     * @param TranslatorInterface $translator
     * @param $token
     * @return JsonResponse
     */
    public function deleteArticle(
        ArticleManager $articleManager,
        LoggerInterface $logger,
        TranslatorInterface $translator,
        $token
    ): JsonResponse
    {
        /** @var Article|null $article */
        $article = $articleManager->getArticleByToken($token);

        if (null === $article) {
            $logger->error(
                sprintf('No article found for token %s.', $token),
                ['_method' => __METHOD__]
            );

            return new JsonResponse(
                $translator->trans('article.not_found'),
                JsonResponse::HTTP_BAD_REQUEST
            );
        }

        $articleManager->deleteArticle($article);

        $logger->info(
            sprintf('Article %s has been successfully removed.', $article->getTitle()),
            ['_method' => __METHOD__]
        );

        return new JsonResponse(
            $translator->trans('article.deletion.success', ['%title%' => $article->getTitle()]),
            JsonResponse::HTTP_OK
        );
    }

    /**
     * Function to find articles linked to categories sent in form
     *
     * @Route("/article/category", name="fetch_articles_by_category", methods={"POST"})
     * @param ArticleManager $articleManager
     * @param Request $request
     * @param LoggerInterface $logger
     * @param TranslatorInterface $translator
     * @return JsonResponse
     */
    public function getArticlesByCategory(
        ArticleManager $articleManager,
        Request $request,
        LoggerInterface $logger,
        TranslatorInterface $translator
    ): JsonResponse
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
            $articles = $articleManager->getArticlesByCategory($categories);

            if (empty($articles)) {
                $logger->error(
                    'No article found for category.',
                    ['_method' => __METHOD__, '_args' => serialize($categories)]
                );

                return new JsonResponse(
                    $translator->trans('query.no_article'),
                    JsonResponse::HTTP_BAD_REQUEST
                );
            }

            $categories = array_map(function($category) {
                return $category->getCategory();
            }, $categories);

            sort($categories);

            return new JsonResponse([
                'articles' => $articles,
                'categories' => $categories
            ], JsonResponse::HTTP_OK);
        }

        return new JsonResponse($translator->trans('generic.form.invalid'), JsonResponse::HTTP_BAD_REQUEST);
    }

    /**
     * Function to fetch an article for vue-router route category/:category
     * @Route("/article/category/{category}", name="fetch_article_for_category", methods={"GET"})
     * @param ArticleManager $articleManager
     * @param CategoryManager $categoryManager
     * @param TranslatorInterface $translator
     * @param LoggerInterface $logger
     * @param string $category
     * @return JsonResponse
     */
    public function getArticleForCategory(
        ArticleManager $articleManager,
        CategoryManager $categoryManager,
        TranslatorInterface $translator,
        LoggerInterface $logger,
        string $category
    ): JsonResponse
    {
        /** @var Category|null $category */
        $category = $categoryManager->getCategoryByName($category);

        if (null === $category) {
            $logger->warning('No category found.', ['_method' => __METHOD__]);

            return new JsonResponse(
                $translator->trans('query.no_category'),
                JsonResponse::HTTP_BAD_REQUEST
            );
        }

        /** @var array $articles */
        $articles = $articleManager->getArticlesByCategory([$category]);

        if (empty($articles)) {
            $logger->warning('No article found.', ['_method' => __METHOD__]);

            return new JsonResponse(
                $translator->trans('query.no_article'),
                JsonResponse::HTTP_BAD_REQUEST
            );
        }

        return new JsonResponse($articles, JsonResponse::HTTP_OK);
    }

    /**
     * Function to return a target article
     *
     * @Route("/article/{slug}", name="article_fetch", methods={"GET"})
     * @param ArticleManager $articleManager
     * @param Serializor $serializor
     * @param LoggerInterface $logger
     * @param TranslatorInterface $translator
     * @param string $slug
     * @return JsonResponse
     */
    public function getArticle(
        ArticleManager $articleManager,
        Serializor $serializor,
        LoggerInterface $logger,
        TranslatorInterface $translator,
        $slug
    ): JsonResponse
    {
        /** @var Article|null $article */
        $article = $articleManager->getArticleBySlug($slug);

        if (null === $article) {
            $logger->error(
                sprintf('No article found for slug %s.', $slug),
                ['_method' => __METHOD__]
            );

            return new JsonResponse(
                $translator->trans('article.not_found'),
                JsonResponse::HTTP_BAD_REQUEST
            );
        }

        try {
            return new JsonResponse(
                $serializor->getSerializer()->normalize($article, 'json', [
                    'groups' => ['article']
                ]), JsonResponse::HTTP_OK);
        } catch (Exception $exception) {
            $logger->error($exception->getMessage(), ['_method' => __METHOD__]);

            return new JsonResponse(
                $translator->trans('generic.error'),
                JsonResponse::HTTP_BAD_REQUEST
            );
        }
    }

    /**
     * @Route("/delete/pdf/{pdf}", name="delete_pdf", methods={"DELETE"})
     * @param EventDispatcherInterface $eventDispatcher
     * @param ArticleManager $articleManager
     * @param LoggerInterface $logger
     * @param TranslatorInterface $translator
     * @param string $pdf
     * @return JsonResponse
     */
    public function deletePDF(
        EventDispatcherInterface $eventDispatcher,
        ArticleManager $articleManager,
        LoggerInterface $logger,
        TranslatorInterface $translator,
        string $pdf
    ): JsonResponse
    {
        /** @var Article|null $article */
        $article = $articleManager->getArticleByPdf($pdf);

        if (null === $article) {
            $logger->error(
                sprintf('No article found for pdf %s.', $pdf),
                ['_method' => __METHOD__]
            );

            return new JsonResponse(
                $translator->trans('article.not_found'),
                JsonResponse::HTTP_BAD_REQUEST
            );
        }

        $pdfEvent = new PdfEvent($pdf);
        $eventDispatcher->dispatch(PdfEvent::APP_BUNDLE_PDF_REMOVE, $pdfEvent);

        /** @var int $status */
        $status = $pdfEvent->getStatus();

        if ($status === PdfService::NO_ERROR) {
            $articleManager->unsetPdf($article);

            return new JsonResponse(
                $translator->trans('pdf.deletion.success', ['%pdf%' => $pdf]),
                JsonResponse::HTTP_OK
            );
        }

        return new JsonResponse(
            $translator->trans('pdf.deletion.failure', ['%pdf%' => $pdf]),
            JsonResponse::HTTP_BAD_REQUEST
        );
    }

    /**
     * @Route("/admin/articles/newsletter", name="fetch_articles_with_newsletter", methods={"GET"})
     * @param ArticleManager $articleManager
     * @return JsonResponse
     */
    public function fetchArticlesWithNewsletter(ArticleManager $articleManager): JsonResponse
    {
        /** @var array $articles */
        $articles = $articleManager->getArticlesWithNewsletter();

        return new JsonResponse($articles, JsonResponse::HTTP_OK);
    }
}