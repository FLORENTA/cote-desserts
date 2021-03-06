<?php

namespace AppBundle\EventListener;

use AppBundle\Event\PdfEvent;
use AppBundle\Service\PdfService;

/**
 * Class PdfListener
 * @package AppBundle\EventListener
 */
class PdfListener
{
    /** @var PdfService $pdfService */
    private $pdfService;

    /**
     * pdfListener constructor.
     * @param PdfService $pdfService
     */
    public function __construct(PdfService $pdfService)
    {
        $this->pdfService = $pdfService;
    }

    /**
     * @param PdfEvent $pdfEvent
     */
    public function remove(PdfEvent $pdfEvent): void
    {
        /** @var string $pdf */
        $pdf = $pdfEvent->getFilename();

        /** @var int $status */
        $status = $this->pdfService->remove($pdf);

        $pdfEvent->setStatus($status);
    }
}