<?php

namespace AppBundle\Controller\Web;

use AppBundle\Entity\User;
use AppBundle\Form\UpdatePasswordType;
use AppBundle\Model\PasswordModel;
use Doctrine\ORM\EntityManagerInterface;
use Psr\Log\LoggerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Routing\RouterInterface;
use Symfony\Component\Translation\TranslatorInterface;

/**
 * Class PasswordController
 * @package AppBundle\Controller
 */
class PasswordController extends Controller
{
    /**
     * @Route("/admin/password/fetch-form", name="fetch_password_form", methods={"GET"})
     * @param RouterInterface $router
     * @return JsonResponse
     */
    public function fetchPasswordForm(RouterInterface $router): JsonResponse
    {
        $passwordModel = new PasswordModel();

        $form = $this->createForm(UpdatePasswordType::class, $passwordModel, [
            'action' => $router->generate('update_password')
        ]);

        return new JsonResponse($this->renderView('form/password_form.html.twig', [
            'form' => $form->createView()
        ]), JsonResponse::HTTP_OK);
    }
}