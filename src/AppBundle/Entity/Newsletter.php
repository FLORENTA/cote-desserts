<?php

namespace AppBundle\Entity;

use Symfony\Component\Serializer\Annotation\Groups;
use DateTime;

/**
 * Newsletter
 */
class Newsletter
{
    /**
     * @Groups("newsletter")
     * @var int
     */
    private $id;

    /**
     * @Groups("newsletter")
     * @var string
     */
    private $email;

    /**
     * @Groups("newsletter")
     * @var DateTime
     */
    private $date;

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
     * Set email
     *
     * @param string $email
     *
     * @return Newsletter
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
     * Set date
     *
     * @param DateTime $date
     *
     * @return Newsletter
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
     * @var string
     */
    private $token;


    /**
     * Set token
     *
     * @param string $token
     *
     * @return Newsletter
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
