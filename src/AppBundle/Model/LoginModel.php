<?php

namespace AppBundle\Model;

/**
 * Class LoginModel
 * @package AppBundle\Model
 */
class LoginModel
{
    /** @var string|null $username */
    private $username;

    /** @var string|null */
    private $password;

    /**
     * @return string|null
     */
    public function getUsername(): ?string
    {
        return $this->username;
    }

    /**
     * @param string|null $username
     * @return LoginModel
     */
    public function setUsername(?string $username): LoginModel
    {
        $this->username = $username;

        return $this;
    }

    /**
     * @return string|null
     */
    public function getPassword(): ?string
    {
        return $this->password;
    }

    /**
     * @param string|null $password
     * @return LoginModel
     */
    public function setPassword(?string $password): LoginModel
    {
        $this->password = $password;

        return $this;
    }
}