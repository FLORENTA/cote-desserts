<?php

namespace AppBundle\Entity;

use Symfony\Component\Serializer\Annotation\Groups;
use DateTime;

/**
 * Contact
 */
class Contact
{
    /**
     * @Groups("contact")
     * @var int
     */
    private $id;

    /**
     * @Groups("contact")
     * @var string
     */
    private $email;

    /**
     * @Groups("contact")
     * @var string
     */
    private $message;

    /**
     * @Groups("contact")
     * @var DateTime
     */
    private $date;

    /**
     * @var string
     */
    private $token;

    /**
     * Get id
     *
     * @return int
     */
    public function getId(): int
    {
        return $this->id;
    }

    /**
     * Set email
     *
     * @param string $email
     *
     * @return Contact
     */
    public function setEmail($email): Contact
    {
        $this->email = $email;

        return $this;
    }

    /**
     * Get email
     *
     * @return string|null
     */
    public function getEmail(): ?string
    {
        return $this->email;
    }

    /**
     * Set message
     *
     * @param string $message
     *
     * @return Contact
     */
    public function setMessage($message): Contact
    {
        $this->message = $message;

        return $this;
    }

    /**
     * Get message
     *
     * @return string|null
     */
    public function getMessage(): ?string
    {
        return $this->message;
    }

    /**
     * Set date
     *
     * @param DateTime $date
     *
     * @return Contact
     */
    public function setDate($date): Contact
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
     * Set token
     *
     * @param string $token
     *
     * @return Contact
     */
    public function setToken($token): Contact
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
}
