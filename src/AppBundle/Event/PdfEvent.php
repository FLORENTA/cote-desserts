<?php

namespace AppBundle\Event;

use AppBundle\Entity\Pdf;
use Symfony\Component\EventDispatcher\Event;

/**
 * Class PdfEvent
 * @package AppBundle\Event
 */
final class PdfEvent extends Event
{
    const APP_BUNDLE_PDF_REMOVE = 'app_bundle.pdf.remove';

    /** @var Pdf $pdf */
    private $pdf;

    /** @var int $status */
    private $status;

    /**
     * PdfEvent constructor.
     * @param Pdf|null $pdf
     */
    public function __construct(?Pdf $pdf)
    {
        $this->pdf = $pdf;
    }

    /**
     * @return Pdf
     */
    public function getPdf(): Pdf
    {
        return $this->pdf;
    }

    /**
     * @return int
     */
    public function getStatus(): int
    {
        return $this->status;
    }

    /**
     * @param int $status
     * @return PdfEvent
     */
    public function setStatus(int $status): PdfEvent
    {
        $this->status = $status;

        return $this;
    }
}