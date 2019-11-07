<?php

namespace AppBundle\Controller\Rest;

use AppBundle\Entity\User;
use AppBundle\Form\UpdatePasswordType;
use AppBundle\Model\PasswordModel;
use Doctrine\ORM\EntityManagerInterface;
use FOS\RestBundle\Controller\AbstractFOSRestController;
use FOS\RestBundle\Controller\Annotations as Rest;
use FOS\RestBundle\View\View;
use Psr\Log\LoggerInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Translation\TranslatorInterface;

/**
 * Class PasswordController
 * @package AppBundle\Controller\Rest
 */
class PasswordController extends AbstractFOSRestController
{
    /** @var EntityManagerInterface $em */
    private $em;

    /** @var LoggerInterface $logger */
    private $logger;

    /** @var TranslatorInterface $translator */
    private $translator;

    /**
     * PasswordController constructor.
     * @param EntityManagerInterface $entityManager
     * @param LoggerInterface $logger
     * @param TranslatorInterface $translator
     */
    public function __construct(
        EntityManagerInterface $entityManager,
        LoggerInterface $logger,
        TranslatorInterface $translator
    )
    {
        $this->em = $entityManager;
        $this->logger = $logger;
        $this->translator = $translator;
    }

    /**
     * @Rest\Post()
     * @param Request $request
     * @return View
     */
    public function updatePasswordAction(Request $request): View
    {
        $passwordModel = new PasswordModel();

        $form = $this->createForm(UpdatePasswordType::class, $passwordModel);

        $form->handleRequest($request);

        /** @var string $username */
        $username = $passwordModel->getUsername();

        /** @var User|null $user */
        $user = $this->em->getRepository(User::class)->findOneBy([
            'username' => $username
        ]);

        if (null === $user) {
            $this->logger->error(sprintf('Unknown user for username %s', $username));

            return $this->view(
                $this->translator->trans('login.form.unknown_user'),
                JsonResponse::HTTP_BAD_REQUEST
            );
        }

        if ($form->isSubmitted() && $form->isValid()) {
            if (!password_verify($passwordModel->getLastPassword(), $user->getPassword())) {
                $this->logger->error("Wrong password.", ['_method' => __METHOD__]);

                return $this->view(
                    $this->translator->trans('admin.password.form.last_password.check.failure'),
                    JsonResponse::HTTP_BAD_REQUEST
                );
            }

            /** @var string $newPassword */
            $newPassword = $passwordModel->getNewPassword();

            if ($passwordModel->getNewPassword() === $passwordModel->getConfirmNewPassword()) {
                $newPassword = password_hash($newPassword, PASSWORD_DEFAULT);
                $user->setPassword($newPassword);
                $this->em->flush();

                return $this->view($this->translator->trans('admin.password.update.success'), JsonResponse::HTTP_OK);
            }

            return $this->view(
                $this->translator->trans('admin.password.form.new_password.matching.failure'),
                JsonResponse::HTTP_BAD_REQUEST
            );
        }

        return $this->view($this->translator->trans('generic.form.invalid'), JsonResponse::HTTP_BAD_REQUEST);
    }
}