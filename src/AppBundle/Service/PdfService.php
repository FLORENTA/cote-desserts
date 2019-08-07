<?php

namespace AppBundle\Service;

use Psr\Log\LoggerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\Translation\TranslatorInterface;
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

    /** @var TranslatorInterface $translator */
    private $translator;

    /**
     * PdfService constructor.
     * @param RequestStack $request
     * @param FileService $fileService
     * @param LoggerInterface $logger
     * @param TranslatorInterface $translator
     */
    public function __construct(
        RequestStack $request,
        FileService $fileService,
        LoggerInterface $logger,
        TranslatorInterface $translator
    )
    {
        $this->request = $request->getCurrentRequest();
        $this->fileService = $fileService;
        $this->logger = $logger;
        $this->translator = $translator;
    }

    /**
     * @param string $pdf
     * @return int
     */
    public function remove(string $pdf): int
    {
        try {
            $this->fileService->removeFile($pdf);
            $this->logger->info(
                $this->translator->trans('pdf.deletion.success', ['%pdf%' => $pdf]),
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