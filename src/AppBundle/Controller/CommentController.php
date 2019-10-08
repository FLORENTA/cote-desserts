<?php

namespace AppBundle\Controller;

use AppBundle\Entity\Article;
use AppBundle\Entity\Comment;
use AppBundle\Form\CommentType;
use AppBundle\Manager\CommentManager;
use AppBundle\Service\Serializor;
use Doctrine\ORM\EntityManagerInterface;
use Psr\Log\LoggerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Routing\RouterInterface;
use Symfony\Component\Translation\TranslatorInterface;
use Exception;

/**
 * Class CommentController
 * @package AppBundle\Controller
 */
class CommentController extends Controller
{
    /**
     * @Route("/comment/fetch-form/article/{id}", name="fetch_comment_form", methods={"GET"})
     * @param EntityManagerInterface $entityManager
     * @param TranslatorInterface $translator
     * @param RouterInterface $router
     * @param string $id
     * @return JsonResponse
     */
    public function commentForm(
        EntityManagerInterface $entityManager,
        TranslatorInterface $translator,
        RouterInterface $router,
        string $id
    ): JsonResponse
    {
        /** @var Article|null $article */
        $article = $entityManager->getPartialReference(Article::class, $id);

        if (null === $article) {
            return new JsonResponse(
                $translator->trans('query.no_article_for_id', [
                        '%id%' => $id
                    ]
                ), JsonResponse::HTTP_BAD_REQUEST);
        }

        $comment = new Comment();

        $form = $this->createForm(CommentType::class, $comment, [
            'action' => $router->generate('comment_new', [
                'id' => $id
            ])
        ]);

        return new JsonResponse(
            $this->renderView('form/comment_form.html.twig', [
                'form' => $form->createView(),
                'title' => $article->getTitle()
            ]),
            JsonResponse::HTTP_OK
        );
    }

    /**
     * @Route("/comment/new/article/{id}", name="comment_new", methods={"POST"})
     * @param Request $request
     * @param TranslatorInterface $translator
     * @param EntityManagerInterface $entityManager
     * @param LoggerInterface $logger
     * @param CommentManager $commentManager
     * @param string $id
     * @return JsonResponse
     */
    public function newComment(
        Request $request,
        TranslatorInterface $translator,
        EntityManagerInterface $entityManager,
        LoggerInterface $logger,
        CommentManager $commentManager,
        $id
    ): JsonResponse
    {
        $comment = new Comment();
        $form = $this->createForm(CommentType::class, $comment);
        $form->handleRequest($request);

        /** @var Article|null $article */
        $article = $entityManager->getRepository(Article::class)->find($id);

        if (null === $article) {
            $logger->warning(sprintf('Unknown article for id %s', $id), [
                '_method' => __METHOD__
            ]);

            return new JsonResponse(
                $translator->trans('query.no_article_for_id', [
                    '%id%' => $id
                ]
            ), JsonResponse::HTTP_BAD_REQUEST);
        }

        if ($form->isSubmitted() && $form->isValid()) {
            try {
                /** @var int $status */
                $status = $commentManager->createComment($comment, $article);

                if ($status === CommentManager::ERROR) {
                    $logger->warning(sprintf('An error occurred when trying to save a new comment from %s.', $comment->getEmail()), [
                        '_method' => __METHOD__
                    ]);

                    return new JsonResponse(
                        $translator->trans('comment.sent.failure'),
                        JsonResponse::HTTP_OK
                    );
                }

                return new JsonResponse(
                    $translator->trans('comment.sent.success'),
                    JsonResponse::HTTP_OK
                );
            } catch (Exception $exception) {
                $logger->error($exception->getMessage(), [
                    '_method' => __METHOD__
                ]);

                return new JsonResponse(
                    $translator->trans('generic.error'),
                    JsonResponse::HTTP_BAD_REQUEST
                );
            }
        }

        return new JsonResponse($translator->trans('generic.form.invalid'), JsonResponse::HTTP_BAD_REQUEST);
    }

    /**
     * @Route("/admin/comments", name="fetch_comments", methods={"GET"})
     * @param CommentManager $commentManager
     * @param Serializor $serializor
     * @param LoggerInterface $logger
     * @param TranslatorInterface $translator
     * @return JsonResponse
     */
    public function fetchComments(
        CommentManager $commentManager,
        Serializor $serializor,
        LoggerInterface $logger,
        TranslatorInterface $translator
    ): JsonResponse
    {
        /** @var array $comments */
        $comments = $commentManager->getComments();

        if (empty($comments)) {
            $logger->info('No comment found.', ['_method' => __METHOD__]);

            return new JsonResponse(
                $translator->trans('query.no_comment'),
                JsonResponse::HTTP_BAD_REQUEST
            );
        }

        try {
            return new JsonResponse(
                $serializor->getSerializer()->normalize($comments, 'json', [
                    'groups' => ['comment']
                ]),
                JsonResponse::HTTP_OK
            );
        } catch (Exception $exception) {
            $logger->error($exception->getMessage(), ['_method' => __METHOD__]);

            return new JsonResponse(
                $translator->trans('query.no_comment'),
                JsonResponse::HTTP_BAD_REQUEST
            );
        }
    }

    /**
     * @Route("/admin/comment/delete/{token}", name="delete_comment", methods={"DELETE"})
     * @param CommentManager $commentManager
     * @param LoggerInterface $logger
     * @param TranslatorInterface $translator
     * @param $token
     * @return JsonResponse
     */
    public function deleteCommentAction(
        CommentManager $commentManager,
        LoggerInterface $logger,
        TranslatorInterface $translator,
        $token
    ): JsonResponse
    {
        /** @var Comment|null $comment */
        $comment = $commentManager->getCommentByToken($token);

        if (null === $comment) {
            $logger->error(
                sprintf('No comment found for token %s.', $token),
                ['_method' => __METHOD__]
            );

            return new JsonResponse(
                $translator->trans('comment.not_found'),
                JsonResponse::HTTP_BAD_REQUEST
            );
        }

        /** @var string $email */
        $email = $comment->getEmail();

        $commentManager->deleteComment($comment);

        $logger->info(
             sprintf('Deleted comment from %s', $email),
             ['_method' => __METHOD__]
        );

        return new JsonResponse(
            $translator->trans('comment.deletion.success'),
            JsonResponse::HTTP_OK
        );
    }

    /**
     * @Route("/admin/comment/{token}/status/{status}", name="change_comment_status", methods={"PUT"})
     * @param CommentManager $commentManager
     * @param EntityManagerInterface $entityManager
     * @param LoggerInterface $logger
     * @param TranslatorInterface $translator
     * @param string $token
     * @param string $status
     * @return JsonResponse
     */
    public function changeCommentStatus(
        CommentManager $commentManager,
        EntityManagerInterface $entityManager,
        LoggerInterface $logger,
        TranslatorInterface $translator,
        string $token,
        string $status
    ): JsonResponse
    {
        /** @var Comment|null $comment */
        $comment = $commentManager->getCommentByToken($token);

        if (null === $comment) {
            $logger->error(
                sprintf('No comment found for token %s.', $token),
                ['_method' => __METHOD__]
            );

            return new JsonResponse(
                $translator->trans('comment.not_found'),
                JsonResponse::HTTP_BAD_REQUEST
            );
        }

        $comment->setPublished('true' === $status);
        $entityManager->flush();

        $logger->info(
            sprintf('Updated status of comment from %s, published: %s', $comment->getEmail(), $status),
            ['_method' => __METHOD__]
        );

        return new JsonResponse(
            $translator->trans('comment.update.success'),
            JsonResponse::HTTP_OK
        );
    }

    /**
     * @Route("/comments/article/{id}", name="fetch_comments_by_article", methods={"GET"})
     * @param Serializor $serializor
     * @param CommentManager $commentManager
     * @param EntityManagerInterface $entityManager
     * @param LoggerInterface $logger
     * @param TranslatorInterface $translator
     * @param string $id
     * @return JsonResponse
     */
    public function fetchCommentsByArticle(
        Serializor $serializor,
        CommentManager $commentManager,
        EntityManagerInterface $entityManager,
        LoggerInterface $logger,
        TranslatorInterface $translator,
        $id
    ): JsonResponse
    {
        /** @var Article|null $article */
        $article = $entityManager->getRepository(Article::class)->findOneBy([
            'id' => $id
        ]);

        if (null === $article) {
            $logger->error(sprintf('Unknown article for id %s', $id), [
                '_method' => __METHOD__
            ]);

            return new JsonResponse($translator->trans('article.not_found'), JsonResponse::HTTP_BAD_REQUEST);
        }

        try {
            /** @var Comment[] $comments */
            $comments = $commentManager->getCommentsByArticle($article);
        } catch (Exception $exception) {
            $logger->error($exception->getMessage(), ['_method' => __METHOD__]);
            $comments = [];
        }

        return new JsonResponse($this->renderView('comments/article_comments.html.twig', [
                'comments' => $comments
        ]), JsonResponse::HTTP_OK);
    }
}