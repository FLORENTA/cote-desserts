<?php

namespace AppBundle\Controller\Web;

use AppBundle\Entity\User;
use AppBundle\Form\LoginType;
use AppBundle\Manager\UserManager;
use AppBundle\Model\LoginModel;
use Psr\Log\LoggerInterface;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\RouterInterface;
use Symfony\Component\Translation\TranslatorInterface;

/**
 * Class LoginController
 * @package AppBundle\Controller
 */
class LoginController extends Controller
{
    /**
     * @Route("/login/fetch-form", name="fetch_login_form", methods={"GET"})
     * @param RouterInterface $router
     * @return Response|JsonResponse
     */
    public function loginForm(
        RouterInterface $router
    ): Response
    {
        $loginModel = new LoginModel();
        $form = $this->createForm(LoginType::class, $loginModel, [
            'action' => $router->generate('check_login_form')
        ]);

        return new JsonResponse($this->renderView('form/login_form.html.twig', [
            'form' => $form->createView()
        ]), JsonResponse::HTTP_OK);
    }

    /**
     * @Route("/login/form/check", name="check_login_form", methods={"POST"})
     * @param TranslatorInterface $translator
     * @param UserManager $userManager
     * @param SessionInterface $session
     * @param Request $request
     * @return JsonResponse|Response
     */
    public function checkLoginForm(
        TranslatorInterface $translator,
        UserManager $userManager,
        SessionInterface $session,
        Request $request
    ): Response
    {
        $loginModel = new LoginModel();
        $form = $this->createForm(LoginType::class, $loginModel);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            /** @var User|null $user */
            $user = $userManager->getUserByUsername($loginModel->getUsername());

            if (null === $user) {
                return new JsonResponse(
                    $translator->trans('login.form.unknown_user'),
                    JsonResponse::HTTP_BAD_REQUEST
                );
            }

            if (password_verify($loginModel->getPassword(), $user->getPassword())) {
                $token = hash('sha256', time() . $user->getUsername());
                $session->set('token', $token);

                return new JsonResponse([
                    'token' => $token
                ], JsonResponse::HTTP_OK);
            }

            return new JsonResponse(
                $translator->trans('login.form.password.failure'),
                JsonResponse::HTTP_BAD_REQUEST
            );
        }

        return new Response(
            $translator->trans('generic.form.invalid'),
            JsonResponse::HTTP_BAD_REQUEST
        );
    }

    /**
     * @Route("/session/fetch-token", name="fetch_session_token", methods={"GET"})
     * @param SessionInterface $session
     * @param LoggerInterface $logger
     * @param TranslatorInterface $translator
     * @return Response|JsonResponse
     */
    public function fetchToken(
        SessionInterface $session,
        LoggerInterface $logger,
        TranslatorInterface $translator
    ): JsonResponse
    {
        if (null === ($token = $session->get('token'))) {
            $logger->critical('Unknown token.', ['_method' => __METHOD__]);

            return new JsonResponse($translator->trans('login.token.unknown'), JsonResponse::HTTP_BAD_REQUEST);
        }

        return new JsonResponse($token, JsonResponse::HTTP_OK);
    }
}