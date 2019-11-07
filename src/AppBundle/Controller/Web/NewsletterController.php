<?php

namespace AppBundle\Controller\Web;

use AppBundle\Entity\Newsletter;
use AppBundle\Form\NewsletterType;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\RouterInterface;

/**
 * Class NewsletterController
 * @package AppBundle\Controller
 */
class NewsletterController extends Controller
{
    /**
     * @Route("/newsletter/form", name="get_newsletter_form", methods={"GET"})
     * @param RouterInterface $router
     * @return JsonResponse
     */
    public function getNewsletterForm(RouterInterface $router): JsonResponse
    {
        $newsletter = new Newsletter();
        $form = $this->createForm(NewsletterType::class, $newsletter, [
            'action' => $router->generate('create_newsletter')
        ]);

        return new JsonResponse($this->renderView('form/newsletter_form.html.twig', [
            'form' => $form->createView()
        ]), JsonResponse::HTTP_OK);
    }
}