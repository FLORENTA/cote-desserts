<?php

namespace AppBundle\Service;

use AppBundle\Entity\Pdf;
use Psr\Log\LoggerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\RequestStack;
use Exception;

/**
 * Class PdfManager
 * @package AppBundle\Service
 */
class PdfService
{
    const NO_ERROR = 0;
    const ERROR = -1;

    /** @var Request $request */
    private $request;

    /** @var FileService $fileService */
    private $fileService;

    /** @var LoggerInterface $logger */
    private $logger;

    /**
     * PdfService constructor.
     * @param RequestStack $request
     * @param FileService $fileService
     * @param LoggerInterface $logger
     */
    public function __construct(
        RequestStack $request,
        FileService $fileService,
        LoggerInterface $logger
    )
    {
        $this->request = $request->getCurrentRequest();
        $this->fileService = $fileService;
        $this->logger = $logger;
    }

    /**
     * @param Pdf $pdf
     * @return int
     */
    public function remove(Pdf $pdf): int
    {
        try {
            /** @var string $src */
            $src = $pdf->getSrc();
            $this->fileService->removeFile($src);
            $this->logger->info(
                sprintf('The pdf %s has been successfully removed.', $src),
                ['_method' => __METHOD__]
            );

            return self::NO_ERROR;
        } catch (Exception $exception) {
            $this->logger->error($exception->getMessage(), [
                '_method' => __METHOD__
            ]);

            return self::ERROR;
        }
    }
}