<?php

namespace AppBundle\Entity;

use Symfony\Component\Serializer\Annotation\Groups;
use DateTime;

/**
 * Comment
 */
class Comment
{
    /**
     * @Groups({"article", "comment"})
     * @var int
     */
    private $id;

    /**
     * @Groups({"article", "comment"})
     * @var string
     */
    private $username;

    /**
     * @Groups({"article", "comment"})
     * @var string
     */
    private $comment;

    /**
     * @Groups({"article", "comment"})
     * @var DateTime
     */
    private $date;

    /**
     * @Groups({"article", "comment"})
     * @var string
     */
    private $email;

    /**
     * @Groups({"article", "comment"})
     * @var string
     */
    private $token;

    /**
     * @Groups("comment")
     * @var Article
     */
    private $article;

    /**
     * @Groups({"article", "comment"})
     * @var bool
     */
    private $published;

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
     * Set username
     *
     * @param string $username
     *
     * @return Comment
     */
    public function setUsername($username)
    {
        $this->username = $username;

        return $this;
    }

    /**
     * Get username
     *
     * @return string
     */
    public function getUsername()
    {
        return $this->username;
    }

    /**
     * Set comment
     *
     * @param string $comment
     *
     * @return Comment
     */
    public function setComment($comment)
    {
        $this->comment = $comment;

        return $this;
    }

    /**
     * Get comment
     *
     * @return string
     */
    public function getComment()
    {
        return $this->comment;
    }

    /**
     * Set date
     *
     * @param DateTime $date
     *
     * @return Comment
     */
    public function setDate($date)
    {
        $this->date = $date;

        return $this;
    }

    /**
     * Get date
     *
     * @return DateTime
     */
    public function getDate()
    {
        return $this->date;
    }

    /**
     * Set published
     *
     * @param boolean $published
     *
     * @return Comment
     */
    public function setPublished($published)
    {
        $this->published = $published;

        return $this;
    }

    /**
     * Get published
     *
     * @return bool
     */
    public function getPublished()
    {
        return $this->published;
    }

    /**
     * Set email
     *
     * @param string $email
     *
     * @return Comment
     */
    public function setEmail($email)
    {
        $this->email = $email;

        return $this;
    }

    /**
     * Get email
     *
     * @return string
     */
    public function getEmail()
    {
        return $this->email;
    }

    /**
     * Set article
     *
     * @param Article $article
     *
     * @return Comment
     */
    public function setArticle(Article $article = null)
    {
        $this->article = $article;

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
     * Set token
     *
     * @param string $token
     *
     * @return Comment
     */
    public function setToken($token)
    {
        $this->token = $token;

        return $this;
    }

    /**
     * Get token
     *
     * @return string
     */
    public function getToken()
    {
        return $this->token;
    }
}
