<?php

namespace AppBundle\Controller;

use AppBundle\Entity\Legal;
use AppBundle\Form\LegalType;
use AppBundle\Manager\LegalManager;
use Doctrine\ORM\EntityManagerInterface;
use Psr\Log\LoggerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Routing\RouterInterface;
use Symfony\Component\Translation\TranslatorInterface;

/**
 * Class LegalController
 * @package AppBundle\Controller
 */
class LegalController extends Controller
{
    /**
     * @Route("/legal", name="fetch_legal_mentions", methods={"GET"})
     * @param LegalManager $legalManager
     * @param TranslatorInterface $translator
     * @param LoggerInterface $logger
     * @return JsonResponse
     */
    public function fetchLegalMentions(
        LegalManager $legalManager,
        TranslatorInterface $translator,
        LoggerInterface $logger
    ): JsonResponse
    {
        /** @var Legal|null $legalMentions */
        $legalMentions = $legalManager->getLegalMentions();

        if (null === $legalMentions || empty($legalMentions->getContent())) {
            $logger->warning('No legal mentions', ['_method' => __METHOD__]);

            return new JsonResponse(
                $translator->trans('query.no_legal_mentions'),
                JsonResponse::HTTP_BAD_REQUEST
            );
        }

        return new JsonResponse(
            $legalMentions->getContent(),
            JsonResponse::HTTP_OK
        );
    }

    /**
     * @Route("/admin/legal/create-form", name="fetch_create_legal_form", methods={"GET"})
     * @param RouterInterface $router
     * @param LegalManager $legalManager
     * @return JsonResponse
     */
    public function fetchLegalForm(
        RouterInterface $router,
        LegalManager $legalManager
    ): JsonResponse
    {
        /** @var Legal|null $legal */
        $legal = $legalManager->getLegalMentions();

        if (null === $legal) {
            $legal = new Legal();
        }

        $form = $this->createForm(LegalType::class, $legal, [
            'action' => $router->generate('handle_legal_form_submission'),
        ]);

        return new JsonResponse(
            $this->renderView('form/legal_form.html.twig', [
                'form' => $form->createView()
            ]),
            JsonResponse::HTTP_OK
        );
    }

    /**
     * @Route("/admin/legal/handle-form", name="handle_legal_form_submission", methods={"POST"})
     * @param Request $request
     * @param TranslatorInterface $translator
     * @param LegalManager $legalManager
     * @param EntityManagerInterface $entityManager
     * @return JsonResponse
     */
    public function handleLegalFormSubmission(
        Request $request,
        TranslatorInterface $translator,
        LegalManager $legalManager,
        EntityManagerInterface $entityManager
    ): JsonResponse
    {
        /** @var Legal|null $legal */
        $legal = $legalManager->getLegalMentions();
        $create = false;

        if (null === $legal) {
            $create = true;
            $legal = new Legal();
        }

        $form = $this->createForm(LegalType::class, $legal);

        $form->handleRequest($request);

        $message = $translator->trans('legal.update.success');

        if ($form->isSubmitted() && $form->isValid()) {
            if ($create) {
                $message = $translator->trans('legal.creation.success');
                $entityManager->persist($legal);
            }

            $entityManager->flush();

            return new JsonResponse($message, JsonResponse::HTTP_OK);
        }

        return new JsonResponse(
            $translator->trans('generic.form.invalid'),
            JsonResponse::HTTP_OK
        );
    }
}