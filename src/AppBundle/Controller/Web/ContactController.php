<?php

namespace AppBundle\Controller\Web;

use AppBundle\Entity\Contact;
use AppBundle\Form\ContactType;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Routing\RouterInterface;

/**
 * Class ContactController
 * @package AppBundle\Controller
 */
class ContactController extends Controller
{
    /**
     * @Route("/contact/form", name="get_contact_form", methods={"GET"})
     * @param RouterInterface $router
     * @return JsonResponse
     */
    public function getContactForm(RouterInterface $router): JsonResponse
    {
        $contact = new Contact();

        $form = $this->createForm(ContactType::class, $contact, [
            'action' => $router->generate('create_contact')
        ]);

        return new JsonResponse($this->renderView('form/contact_form.html.twig', [
            'form' => $form->createView()
        ]), JsonResponse::HTTP_OK);
    }
}