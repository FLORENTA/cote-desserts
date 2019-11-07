<?php

namespace AppBundle\Controller\Web;

use AppBundle\Entity\Comment;
use AppBundle\Form\CommentType;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Annotation\Route;

/**
 * Class CommentController
 * @package AppBundle\Controller
 */
class CommentController extends Controller
{
    /**
     * @Route("/comment/create/form", name="get_comment_form", methods={"GET"})
     * @return JsonResponse
     */
    public function getCommentForm(): JsonResponse
    {
        $comment = new Comment();

        $form = $this->createForm(CommentType::class, $comment);

        return new JsonResponse(
            $this->renderView('form/comment_form.html.twig', [
                'form' => $form->createView(),
            ]),
            JsonResponse::HTTP_OK
        );
    }
}