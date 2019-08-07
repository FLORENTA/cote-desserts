<?php

namespace AppBundle\Event;

use Symfony\Component\EventDispatcher\Event;

/**
 * Class PdfEvent
 * @package AppBundle\Event
 */
final class PdfEvent extends Event
{
    const APP_BUNDLE_PDF_REMOVE = 'app_bundle.pdf.remove';

    /** @var string $filename */
    private $filename;

    /** @var int $status */
    private $status;

    /**
     * PdfEvent constructor.
     * @param string|null $filename
     */
    public function __construct(string $filename = null)
    {
        $this->filename = $filename;
    }

    /**
     * @return string
     */
    public function getFilename(): string
    {
        return $this->filename;
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