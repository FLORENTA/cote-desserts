<?php

namespace AppBundle\Controller\Rest;

use AppBundle\Entity\Legal;
use AppBundle\Form\LegalType;
use AppBundle\Manager\LegalManager;
use Doctrine\ORM\EntityManagerInterface;
use FOS\RestBundle\Controller\AbstractFOSRestController;
use FOS\RestBundle\Controller\Annotations as Rest;
use FOS\RestBundle\View\View;
use Psr\Log\LoggerInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Translation\TranslatorInterface;

/**
 * Class LegalController
 * @package AppBundle\Controller\Rest
 */
class LegalController extends AbstractFOSRestController
{
    /** @var LoggerInterface $logger */
    private $logger;

    /** @var LegalManager $legalManager */
    private $legalManager;

    /** @var TranslatorInterface $translator */
    private $translator;

    /** @var EntityManagerInterface $em */
    private $em;

    /**
     * LegalController constructor.
     * @param LoggerInterface $logger
     * @param LegalManager $legalManager
     * @param TranslatorInterface $translator
     * @param EntityManagerInterface $entityManager
     */
    public function __construct(
        LoggerInterface $logger,
        LegalManager $legalManager,
        TranslatorInterface $translator,
        EntityManagerInterface $entityManager
    )
    {
        $this->logger = $logger;
        $this->legalManager = $legalManager;
        $this->translator = $translator;
        $this->em = $entityManager;
    }

    /**
     * @Rest\Get()
     * @return View
     */
    public function getLegalAction(): View
    {
        /** @var Legal|null $legalMentions */
        $legalMentions = $this->legalManager->getLegalMentions();

        if (null === $legalMentions || empty($legalMentions->getContent())) {
            $this->logger->warning('No legal mentions', ['_method' => __METHOD__]);

            return $this->view(
                $this->translator->trans('query.no_legal_mentions'),
                JsonResponse::HTTP_BAD_REQUEST
            );
        }

        return $this->view($legalMentions->getContent(), JsonResponse::HTTP_OK);
    }

    /**
     * @Rest\Post()
     * @param Request $request
     * @return View
     */
    public function createLegalAction(Request $request): View
    {
        $legal = new Legal();
        $form = $this->createForm(LegalType::class, $legal);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->legalManager->createLegalMentions($legal);

            return $this->view($this->translator->trans('legal.creation.success'), JsonResponse::HTTP_OK);
        }

        return $this->view($this->translator->trans('generic.form.invalid'), JsonResponse::HTTP_OK);
    }

    /**
     * @Rest\Post()
     * @param Request $request
     * @return View
     */
    public function updateLegalAction(Request $request): View
    {
        /** @var Legal|null $legal */
        $legal = $this->legalManager->getLegalMentions();

        if (null !== $legal) {
            $form = $this->createForm(LegalType::class, $legal);
            $form->handleRequest($request);

            if ($form->isSubmitted() && $form->isValid()) {
                $this->em->flush();

                return $this->view($this->translator->trans('legal.update.success'), JsonResponse::HTTP_OK);
            }

            return $this->view($this->translator->trans('generic.form.invalid'), JsonResponse::HTTP_OK);
        }

        return $this->view(
            $this->translator->trans('query.no_legal_mentions'),
            JsonResponse::HTTP_BAD_REQUEST
        );
    }
}