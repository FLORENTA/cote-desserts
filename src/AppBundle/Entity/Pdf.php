<?php

namespace AppBundle\Entity;

use DateTime;
use Exception;
use Symfony\Component\HttpFoundation\File\UploadedFile;

/**
 * Pdf
 */
class Pdf
{
    /**
     * @var int
     */
    private $id;

    /**
     * @var Article
     */
    private $article;

    /**
     * @var UploadedFile $file
     */
    private $file;

    /**
     * @var string $src
     */
    private $src;

    /**
     * @var DateTime $updateAt
     */
    private $updateAt;

    /**
     * Get id.
     *
     * @return int
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Set src.
     *
     * @param string|null $src
     *
     * @return Pdf
     */
    public function setSrc(?string $src): Pdf
    {
        $this->src = $src;

        return $this;
    }

    /**
     * Get src.
     *
     * @return string|null
     */
    public function getSrc(): ?string
    {
        return $this->src;
    }

    /**
     * Set article.
     *
     * @param Article|null $article
     *
     * @return Pdf
     */
    public function setArticle(?Article $article): Pdf
    {
        $this->article = $article;

        return $this;
    }

    /**
     * Get article.
     *
     * @return Article|null
     */
    public function getArticle(): ?Article
    {
        return $this->article;
    }

    /**
     * @param UploadedFile|null $file
     * @return $this
     * @throws Exception
     */
    public function setFile(?UploadedFile $file): Pdf
    {
        if (null !== $this->getId() && null !== $file) {
            $this->setUpdateAt(new DateTime());
        }

        $this->file = $file;

        return $this;
    }

    /**
     * @return UploadedFile|null
     */
    public function getFile(): ?UploadedFile
    {
        return $this->file;
    }

    /**
     * Set updateAt
     *
     * @param DateTime $updateAt
     *
     * @return Pdf
     */
    public function setUpdateAt($updateAt): Pdf
    {
        $this->updateAt = $updateAt;

        return $this;
    }

    /**
     * Get updateAt
     *
     * @return DateTime
     */
    public function getUpdateAt()
    {
        return $this->updateAt;
    }
}
