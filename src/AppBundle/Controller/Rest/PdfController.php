<?php

namespace AppBundle\Controller\Rest;

use AppBundle\Entity\Pdf;
use AppBundle\Event\PdfEvent;
use AppBundle\Manager\PdfManager;
use AppBundle\Service\PdfService;
use FOS\RestBundle\Controller\AbstractFOSRestController;
use FOS\RestBundle\View\View;
use Psr\Log\LoggerInterface;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Translation\TranslatorInterface;

/**
 * Class PdfController
 * @package AppBundle\Controller\Rest
 */
class PdfController extends AbstractFOSRestController
{
    /** @var PdfManager $pdfManager */
    private $pdfManager;

    /** @var EventDispatcherInterface $eventDispatcher */
    private $eventDispatcher;

    /** @var LoggerInterface $logger */
    private $logger;

    /** @var TranslatorInterface $translator */
    private $translator;

    /**
     * PdfController constructor.
     * @param PdfManager $pdfManager
     * @param EventDispatcherInterface $eventDispatcher
     * @param LoggerInterface $logger
     * @param TranslatorInterface $translator
     */
    public function __construct(
        PdfManager $pdfManager,
        EventDispatcherInterface $eventDispatcher,
        LoggerInterface $logger,
        TranslatorInterface$translator
    )
    {
        $this->pdfManager = $pdfManager;
        $this->eventDispatcher = $eventDispatcher;
        $this->logger = $logger;
        $this->translator = $translator;
    }

    /**
     * @param string $src
     * @return View
     */
    public function deletePdfAction(string $src): View
    {
        /** @var Pdf|null $pdf */
        $pdf = $this->pdfManager->getPdfBySrc($src);

        if (null === $pdf) {
            return $this->view(
                $this->translator->trans('pdf.token.not_found'),
                JsonResponse::HTTP_BAD_REQUEST
            );
        }

        /** @var string|null $src */
        $src = $pdf->getSrc();

        $pdfEvent = new PdfEvent($pdf);

        // Remove file associated to this Pdf entity
        $this->eventDispatcher->dispatch(PdfEvent::APP_BUNDLE_PDF_REMOVE, $pdfEvent);

        /** @var int $status */
        $status = $pdfEvent->getStatus();

        if ($status === PdfService::NO_ERROR) {
            $this->pdfManager->clearPdf($pdf);

            return $this->view(
                $this->translator->trans('pdf.deletion.success', ['%pdf%' => $src]),
                JsonResponse::HTTP_OK
            );
        }

        return $this->view(
            $this->translator->trans('pdf.deletion.failure', ['%pdf%' => $src]),
            JsonResponse::HTTP_BAD_REQUEST
        );
    }
}