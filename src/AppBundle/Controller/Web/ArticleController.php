<?php

namespace AppBundle\Controller\Web;

use AppBundle\Entity\Article;
use AppBundle\Entity\Pdf;
use AppBundle\Form\ArticleType;
use AppBundle\Manager\ArticleManager;
use AppBundle\Service\AppTools;
use Psr\Log\LoggerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Routing\RouterInterface;
use Symfony\Component\Translation\TranslatorInterface;

/**
 * Class ArticleController
 * @package AppBundle\Controller\Web
 */
class ArticleController extends Controller
{
    /**
     * @Route("/articles/create/form", name="get_article_create_form")
     * @param RouterInterface $router
     * @return JsonResponse
     */
    public function getArticleCreateForm(RouterInterface $router): JsonResponse
    {
        $article = new Article();
        $form = $this->createForm(ArticleType::class, $article, [
            'action' => $router->generate('create_article')
        ]);

        return new JsonResponse($this->renderView('form/article_form.html.twig', [
            'form' => $form->createView()
        ]), JsonResponse::HTTP_OK);
    }

    /**
     * @Route("/articles/{token}/edit/form", name="get_article_edit_form")
     * @param ArticleManager $articleManager
     * @param LoggerInterface $logger
     * @param TranslatorInterface $translator
     * @param RouterInterface $router
     * @param Request $request
     * @param string $token
     * @return JsonResponse
     */
    public function getArticleEditForm(
        ArticleManager $articleManager,
        LoggerInterface $logger,
        TranslatorInterface $translator,
        RouterInterface $router,
        Request $request,
        string $token
    ): JsonResponse
    {
        /** @var Article|null $article */
        $article = $articleManager->getArticleByToken($token);

        if (null === $article) {
            $logger->warning(sprintf('No article found for token %s', $token), [
                '_method' => __METHOD__
            ]);

            return new JsonResponse($translator->trans('query.no_article'), JsonResponse::HTTP_BAD_REQUEST);
        }

        $form = $this->createForm(ArticleType::class, $article, [
            'action' => $router->generate('update_article', [
                'token' => $article->getToken()
            ])
        ]);

        $form->handleRequest($request);

        $data = ['form' => $form->createView()];

        /** @var Pdf|null $src */
        $pdf = $article->getPdf();

        if (null !== $pdf) {
            /** @var string $src */
            $src = $pdf->getSrc();

            if (null !== $src) {
                $data['delete_pdf_url'] = $router->generate('delete_pdf', [
                    'src' => $src
                ]);
            }
        }

        return new JsonResponse($this->renderView('form/article_form.html.twig', $data), JsonResponse::HTTP_OK);
    }

    /**
     * @Route("/slugify", name="slugify")
     * @param AppTools $appTools
     * @param $title
     * @return Response
     */
    public function slugifyAction(Request $request, AppTools $appTools)
    {
        return new Response($appTools->toolish($request->get('deviceId')));
    }
}