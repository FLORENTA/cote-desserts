<?php

namespace AppBundle\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use DateTime;
use Exception;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\Serializer\Annotation\Groups;

/**
 * Article
 */
class Article
{
    /**
     * @Groups("article")
     * @var integer $id
     */
    private $id;

    /**
     * @Groups("article")
     * @var string|null $title
     */
    private $title;

    /**
     * @Groups("article")
     * @var string|null $slug
     */
    private $slug;

    /**
     * @Groups("article")
     * @var DateTime|null $date
     */
    private $date;

    /**
     * @Groups({"article", "image"})
     * @var DateTime
     */
    private $updateAt;

    /**
     * @Groups("article")
     * @var Collection $images
     */
    private $images;

    /**
     * @Groups("article")
     * @var Collection $comments
     */
    private $comments;

    /**
     * @Groups("article")
     * @var Collection $categories
     */
    private $categories;

    /**
     * @Groups("article")
     * @var Pdf $pdf
     */
    private $pdf;

    /**
     * @Groups("article")
     * @var string|null $token
     */
    private $token;

    /**
     * @var boolean|null $newsletter
     */
    private $newsletter;

    /**
     * Article constructor.
     */
    public function __construct()
    {
        $this->images = new ArrayCollection();
        $this->comments = new ArrayCollection();
        $this->categories = new ArrayCollection();
    }

    /**
     * Get id
     *
     * @return integer
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Set title
     *
     * @param string|null $title
     *
     * @return Article
     */
    public function setTitle(?string $title)
    {
        $this->title = $title;

        return $this;
    }

    /**
     * Get title
     *
     * @return string|null
     */
    public function getTitle(): ?string
    {
        return $this->title;
    }

    /**
     * Set slug
     *
     * @param string|null $slug
     *
     * @return Article
     */
    public function setSlug(?string $slug): Article
    {
        $this->slug = $slug;

        return $this;
    }

    /**
     * Get slug
     *
     * @return string|null
     */
    public function getSlug(): ?string
    {
        return $this->slug;
    }

    /**
     * Set date
     *
     * @param DateTime|null $date
     *
     * @return Article
     */
    public function setDate(?DateTime $date): Article
    {
        $this->date = $date;

        return $this;
    }

    /**
     * Get date
     *
     * @return DateTime|null
     */
    public function getDate(): ?DateTime
    {
        return $this->date;
    }

    /**
     * Add image
     *
     * @param Image $image
     *
     * @return Article
     */
    public function addImage(Image $image): Article
    {
        if (!$this->images->contains($image)) {
            $this->images[] = $image;
            $image->setArticle($this);
        }

        return $this;
    }

    /**
     * Remove image
     *
     * @param Image $image
     */
    public function removeImage(Image $image): void
    {
        if ($this->images->contains($image)) {
            $this->images->removeElement($image);
        }
    }

    /**
     * Get images
     *
     * @return Collection
     */
    public function getImages()
    {
        return $this->images;
    }

    /**
     * Add comment
     *
     * @param Comment $comment
     *
     * @return Article
     */
    public function addComment(Comment $comment): Article
    {
        $this->comments[] = $comment;

        return $this;
    }

    /**
     * Remove comment
     *
     * @param Comment $comment
     */
    public function removeComment(Comment $comment)
    {
        if ($this->comments->contains($comment)) {
            $this->comments->removeElement($comment);
        }
    }

    /**
     * Get comments
     *
     * @return Collection
     */
    public function getComments()
    {
        return $this->comments;
    }

    /**
     * Add category
     *
     * @param Category $category
     *
     * @return Article
     */
    public function addCategory(Category $category): Article
    {
        if (!$this->categories->contains($category)) {
            $this->categories[] = $category;
            $category->addArticle($this);
        }

        return $this;
    }

    /**
     * Remove category
     *
     * @param Category $category
     */
    public function removeCategory(Category $category)
    {
        if ($this->categories->contains($category)) {
            $this->categories->removeElement($category);
        }
    }

    /**
     * Get categories
     *
     * @return Collection
     */
    public function getCategories()
    {
        return $this->categories;
    }

    /**
     * Set newsletter
     *
     * @param boolean|null $newsletter
     *
     * @return Article
     */
    public function setNewsletter(?bool $newsletter): Article
    {
        $this->newsletter = $newsletter;

        return $this;
    }

    /**
     * Get newsletter
     *
     * @return boolean|null
     */
    public function getNewsletter(): ?bool
    {
        return $this->newsletter;
    }
    
    /**
     * Set token
     *
     * @param string|null $token
     *
     * @return Article
     */
    public function setToken(?string $token): Article
    {
        $this->token = $token;

        return $this;
    }

    /**
     * Get token
     *
     * @return string|null
     */
    public function getToken(): ?string
    {
        return $this->token;
    }

    /**
     * Set updateAt
     *
     * @param DateTime $updateAt
     *
     * @return Article
     */
    public function setUpdateAt($updateAt): Article
    {
        $this->updateAt = $updateAt;

        return $this;
    }

    /**
     * Get updateAt
     *
     * @return DateTime
     */
    public function getUpdateAt(): DateTime
    {
        return $this->updateAt;
    }

    /**
     * Set pdf.
     *
     * @param Pdf|null $pdf
     *
     * @return Article
     */
    public function setPdf(?Pdf $pdf): Article
    {
        $this->pdf = $pdf;
        $pdf->setArticle($this);

        return $this;
    }

    /**
     * Get pdf.
     *
     * @return Pdf|null
     */
    public function getPdf(): ?Pdf
    {
        return $this->pdf;
    }
}
