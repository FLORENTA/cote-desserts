<?php

namespace AppBundle\Controller\Web;

use AppBundle\Entity\Legal;
use AppBundle\Form\LegalType;
use AppBundle\Manager\LegalManager;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Routing\RouterInterface;

/**
 * Class LegalController
 * @package AppBundle\Controller
 */
class LegalController extends Controller
{
    /**
     * Entry point for getting create / update form
     * @Route("/admin/legal/create/form", name="get_legal_create_form", methods={"GET"})
     * @param RouterInterface $router
     * @param LegalManager $legalManager
     * @return JsonResponse
     */
    public function getLegalForm(
        RouterInterface $router,
        LegalManager $legalManager
    ): JsonResponse
    {
        /** @var Legal|null $legal */
        $legal = $legalManager->getLegalMentions();

        $data = [];

        if (null === $legal) {
            $legal = new Legal();
            $data['action'] = $router->generate('create_legal');
        } else {
            $data['action'] = $router->generate('update_legal');
        }

        $form = $this->createForm(LegalType::class, $legal, $data);

        return new JsonResponse(
            $this->renderView('form/legal_form.html.twig', [
                'form' => $form->createView()
            ]),
            JsonResponse::HTTP_OK
        );
    }
}