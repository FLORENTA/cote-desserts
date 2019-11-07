<?php

namespace AppBundle\Controller\Rest;

use AppBundle\Entity\Article;
use AppBundle\Entity\Comment;
use AppBundle\Form\CommentType;
use AppBundle\Manager\CommentManager;
use Doctrine\ORM\EntityManagerInterface;
use Exception;
use FOS\RestBundle\Controller\AbstractFOSRestController;
use FOS\RestBundle\Controller\Annotations as Rest;
use FOS\RestBundle\View\View;
use Psr\Log\LoggerInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Translation\TranslatorInterface;

/**
 * Class CommentController
 * @package AppBundle\Controller\Rest
 */
class CommentController extends AbstractFOSRestController
{
    /** @var LoggerInterface $logger */
    private $logger;

    /** @var TranslatorInterface $translator */
    private $translator;

    /** @var EntityManagerInterface $em */
    private $em;

    /** @var CommentManager $commentManager */
    private $commentManager;

    /**
     * CommentController constructor.
     * @param LoggerInterface $logger
     * @param TranslatorInterface $translator
     * @param EntityManagerInterface $entityManager
     * @param CommentManager $commentManager
     */
    public function __construct(
        LoggerInterface $logger,
        TranslatorInterface $translator,
        EntityManagerInterface $entityManager,
        CommentManager $commentManager
    )
    {
        $this->logger = $logger;
        $this->translator = $translator;
        $this->em = $entityManager;
        $this->commentManager = $commentManager;
    }

    /**
     * @Rest\Post()
     * @param Request $request
     * @return View
     */
    public function createCommentAction(Request $request)
    {
        $token = $request->get('articleToken');

        $comment = new Comment();
        $form = $this->createForm(CommentType::class, $comment);
        $form->handleRequest($request);

        /** @var Article|null $article */
        $article = $this->em->getRepository(Article::class)->findOneBy([
            'token' => $token
        ]);

        if (null === $article) {
            $this->logger->warning(sprintf('Unknown article for token %s', $token), [
                '_method' => __METHOD__
            ]);

            return $this->view($this->translator->trans('generic.form.invalid'), JsonResponse::HTTP_BAD_REQUEST);
        }

        if ($form->isSubmitted() && $form->isValid()) {
            try {
                /** @var int $status */
                $status = $this->commentManager->createComment($comment, $article);

                if ($status === CommentManager::ERROR) {
                    $this->logger->warning(sprintf('An error occurred when trying to save a new comment from %s.', $comment->getEmail()), [
                        '_method' => __METHOD__
                    ]);

                    return $this->view(
                        $this->translator->trans('comment.sent.failure'),
                        JsonResponse::HTTP_OK
                    );
                }

                return $this->view(
                    $this->translator->trans('comment.sent.success'),
                    JsonResponse::HTTP_OK
                );
            } catch (Exception $exception) {
                $this->logger->error($exception->getMessage(), [
                    '_method' => __METHOD__
                ]);

                return $this->view(
                    $this->translator->trans('generic.error'),
                    JsonResponse::HTTP_BAD_REQUEST
                );
            }
        }

        return $this->view($this->translator->trans('generic.form.invalid'), JsonResponse::HTTP_BAD_REQUEST);
    }

    /**
     * @Rest\View(serializerGroups={"comment"}, serializerEnableMaxDepthChecks=true)
     * @return Response|View
     */
    public function getCommentsAction()
    {
        /** @var array $comments */
        $comments = $this->commentManager->getComments();

        if (empty($comments)) {
            $this->logger->info('No comment found.', ['_method' => __METHOD__]);

            return $this->view(
                $this->translator->trans('query.no_comment'),
                JsonResponse::HTTP_BAD_REQUEST
            );
        }

        try {
            return $this->handleView($this->view($comments, JsonResponse::HTTP_OK));
        } catch (Exception $exception) {
            $this->logger->error($exception->getMessage(), ['_method' => __METHOD__]);

            return $this->view(
                $this->translator->trans('query.no_comment'),
                JsonResponse::HTTP_BAD_REQUEST
            );
        }
    }

    /**
     * @param string $id
     * @return Response|View
     */
    public function getArticleCommentsAction(string $id)
    {
        /** @var Article|null $article */
        $article = $this->em->getRepository(Article::class)->findOneBy([
            'id' => $id
        ]);

        if (null === $article) {
            $this->logger->error(sprintf('Unknown article for id %s', $id), [
                '_method' => __METHOD__
            ]);

            return $this->view($this->translator->trans('article.not_found'), JsonResponse::HTTP_BAD_REQUEST);
        }

        try {
            /** @var Comment[] $comments */
            $comments = $this->commentManager->getCommentsByArticle($article);
        } catch (Exception $exception) {
            $this->logger->error($exception->getMessage(), ['_method' => __METHOD__]);
            $comments = [];
        }

        $view = View::create($comments, JsonResponse::HTTP_OK);

        return $this->handleView($view);
    }

    /**
     * @param string $token
     * @return View
     */
    public function deleteCommentAction(string $token): View
    {
        /** @var Comment|null $comment */
        $comment = $this->commentManager->getCommentByToken($token);

        if (null === $comment) {
            $this->logger->error(
                sprintf('No comment found for token %s.', $token),
                ['_method' => __METHOD__]
            );

            return $this->view(
                $this->translator->trans('comment.not_found'),
                JsonResponse::HTTP_BAD_REQUEST
            );
        }

        /** @var string $email */
        $email = $comment->getEmail();

        $this->commentManager->deleteComment($comment);

        $this->logger->info(
            sprintf('Deleted comment from %s', $email),
            ['_method' => __METHOD__]
        );

        return $this->view(
            $this->translator->trans('comment.deletion.success'),
            JsonResponse::HTTP_OK
        );
    }

    /**
     * @param Request $request
     * @param string $token
     * @return View
     */
    public function updateCommentAction(Request $request, string $token): View
    {
        $status  = $request->get('status');

        /** @var Comment|null $comment */
        $comment = $this->commentManager->getCommentByToken($token);

        if (null === $comment) {
            $this->logger->error(
                sprintf('No comment found for token %s.', $token),
                ['_method' => __METHOD__]
            );

            return $this->view(
                $this->translator->trans('comment.not_found'),
                JsonResponse::HTTP_BAD_REQUEST
            );
        }

        $comment->setPublished('true' === $status);
        $this->em->flush();

        $this->logger->info(
            sprintf('Updated status of comment from %s, published: %s', $comment->getEmail(), $status),
            ['_method' => __METHOD__]
        );

        return $this->view(
            $this->translator->trans('comment.update.success'),
            JsonResponse::HTTP_OK
        );
    }
}