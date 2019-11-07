<?php

namespace AppBundle\Service;

use Symfony\Component\HttpFoundation\File\File;
use Exception;

/**
 * Class FileService
 * @package AppBundle\Service
 */
class FileService
{
    /** @var string $imagesDirectory */
    private $imagesDirectory;

    /**
     * FileService constructor.
     * @param string $imagesDir
     */
    public function __construct($imagesDir)
    {
        $this->imagesDirectory = $imagesDir;
    }

    /**
     * @param File $file
     * @return string
     */
    public function uploadFile(File $file): string
    {
        /** @var string $filename */
        $filename = md5(uniqid()) . '.' . $file->guessExtension();
        $file->move($this->imagesDirectory, $filename);

        return $filename;
    }

    /**
     * @param string $file
     * @throws Exception
     */
    public function removeFile(string $file): void
    {
        if (is_file($file = $this->imagesDirectory . '/' . $file) && !unlink($file)) {
            throw new Exception(sprintf("File %s could not been deleted.", $file));
        }
    }
}