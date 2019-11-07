<?php

namespace AppBundle\Entity;

use Exception;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\Serializer\Annotation\Groups;
use DateTime;

/**
 * Image
 */
class Image
{
    /**
     * @Groups({"article", "image"})
     * @var int
     */
    private $id;

    /**
     * @Groups({"article", "image"})
     * @var string|null
     */
    private $src;

    /**
     * @Groups({"article", "image"})
     * @var string
     */
    private $title;

    /**
     * @Groups({"article", "image"})
     * @var string
     */
    private $content;

    /**
     * @Groups({"article", "image"})
     * @var DateTime
     */
    private $updateAt;

    /**
     * @var UploadedFile|null $file
     */
    private $file;

    /**
     * @var Article
     */
    private $article;

    /**
     * Get id
     *
     * @return int
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Set src
     *
     * @param string|null $src
     *
     * @return Image
     */
    public function setSrc(?string $src)
    {
        $this->src = $src;

        return $this;
    }

    /**
     * Get src
     *
     * @return string|null
     */
    public function getSrc(): ?string
    {
        return $this->src;
    }

    /**
     * Set title
     *
     * @param string $title
     *
     * @return Image
     */
    public function setTitle($title)
    {
        $this->title = $title;

        return $this;
    }

    /**
     * Get title
     *
     * @return string
     */
    public function getTitle()
    {
        return $this->title;
    }

    /**
     * Set content
     *
     * @param string $content
     *
     * @return Image
     */
    public function setContent($content)
    {
        $this->content = $content;

        return $this;
    }

    /**
     * Get content
     *
     * @return string
     */
    public function getContent()
    {
        return $this->content;
    }

    /**
     * Set updateAt
     *
     * @param DateTime $updateAt
     *
     * @return Image
     */
    public function setUpdateAt($updateAt)
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

    /**
     * Set article
     *
     * @param Article $article
     *
     * @return Image
     */
    public function setArticle(Article $article = null)
    {
        $this->article = $article;
        $article->addImage($this);

        return $this;
    }

    /**
     * Get article
     *
     * @return Article
     */
    public function getArticle()
    {
        return $this->article;
    }

    /**
     * @return UploadedFile|null
     */
    public function getFile(): ?UploadedFile
    {
        return $this->file;
    }

    /**
     * @param UploadedFile|null $file
     * @return Image
     * @throws Exception
     */
    public function setFile(?UploadedFile $file): Image
    {
        if (null !== $this->getSrc() && null !== $file) {
            $this->setUpdateAt(new DateTime());
        }

        $this->file = $file;

        return $this;
    }
}
