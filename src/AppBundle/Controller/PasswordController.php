<?php

namespace AppBundle\Controller;

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

    /**
     * @Route("/admin/password/update", name="update_password", methods={"POST"})
     * @param Request $request
     * @param EntityManagerInterface $entityManager
     * @param LoggerInterface $logger
     * @param TranslatorInterface $translator
     * @return JsonResponse
     */
    public function updatePassword(
        Request $request,
        EntityManagerInterface $entityManager,
        LoggerInterface $logger,
        TranslatorInterface $translator
    ): JsonResponse
    {
        $passwordModel = new PasswordModel();

        $form = $this->createForm(UpdatePasswordType::class, $passwordModel);

        $form->handleRequest($request);

        /** @var string $username */
        $username = $passwordModel->getUsername();

        /** @var User|null $user */
        $user = $entityManager->getRepository(User::class)->findOneBy([
            'username' => $username
        ]);

        if (null === $user) {
            $logger->error(sprintf('Unkwown user for username %s', $username));

            return new JsonResponse(
                $translator->trans('login.form.unknown_user'),
                JsonResponse::HTTP_BAD_REQUEST
            );
        }

        if ($form->isSubmitted() && $form->isValid()) {
            if (!password_verify($passwordModel->getLastPassword(), $user->getPassword())) {
                $logger->error("Wrong password.", ['_method' => __METHOD__]);

                return new JsonResponse(
                    $translator->trans('admin.password.form.last_password.check.failure'),
                    JsonResponse::HTTP_BAD_REQUEST
                );
            }

            /** @var string $newPassword */
            $newPassword = $passwordModel->getNewPassword();

            if ($passwordModel->getNewPassword() === $passwordModel->getConfirmNewPassword()) {
                $newPassword = password_hash($newPassword, PASSWORD_DEFAULT);
                $user->setPassword($newPassword);
                $entityManager->flush();

                return new JsonResponse($translator->trans('admin.password.update.success'), JsonResponse::HTTP_OK);
            }

            return new JsonResponse(
                $translator->trans('admin.password.form.new_password.matching.failure'),
                JsonResponse::HTTP_BAD_REQUEST
            );
        }

        return new JsonResponse(
            $translator->trans('generic.form.invalid'),
            JsonResponse::HTTP_BAD_REQUEST
        );
    }
}