<?php

namespace AppBundle\Manager;

use AppBundle\Entity\Pdf;
use AppBundle\Repository\PdfRepository;
use Doctrine\ORM\EntityManagerInterface;

/**
 * Class PdfManager
 * @package AppBundle\Manager
 */
class PdfManager
{
    /** @var EntityManagerInterface $em */
    private $em;

    /** @var PdfRepository $pdfRepository */
    private $pdfRepository;

    /**
     * PdfManager constructor.
     * @param EntityManagerInterface $entityManager
     */
    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->em = $entityManager;
        $this->pdfRepository = $entityManager->getRepository(Pdf::class);
    }

    /**
     * @param string $src
     * @return Pdf|null
     */
    public function getPdfBySrc(string $src): ?Pdf
    {
        return $this->pdfRepository->findOneBy([
            'src' => $src
        ]);
    }

    /**
     * @param Pdf $pdf
     */
    public function clearPdf(Pdf $pdf): void
    {
        $pdf->setSrc(null);
        $this->em->flush();
    }
}