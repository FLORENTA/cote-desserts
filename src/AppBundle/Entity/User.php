<?php

namespace AppBundle\Entity;

use Symfony\Component\Security\Core\User\AdvancedUserInterface;

/**
 * User
 */
class User implements AdvancedUserInterface
{
    /**
     * @var int
     */
    private $id;

    /**
     * @var string
     */
    private $username;

    /**
     * @var string
     */
    private $password;

    /**
     * @var bool $isEnabled
     */
    private $isEnabled;

    /**
     * @var bool $isAccountNonExpired
     */
    private $isAccountNonExpired;

    /**
     * @var bool $isAccountNonLocked
     */
    private $isAccountNonLocked;

    /**
     * @var bool $isCredentialsNonExpired
     */
    private $isCredentialsNonExpired;

    /**
     * @var array $roles
     */
    private $roles = [];

    /**
     * @var string $salt
     */
    private $salt;

    /**
     * User constructor.
     */
    public function __construct()
    {
        $this->setIsAccountNonExpired(true);
        $this->setIsCredentialsNonExpired(true);
        $this->setIsAccountNonLocked(true);
        $this->setIsEnabled(true);
    }

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
     * @return User
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
     * Set password
     *
     * @param string $password
     *
     * @return User
     */
    public function setPassword($password)
    {
        $this->password = $password;

        return $this;
    }

    /**
     * Get password
     *
     * @return string
     */
    public function getPassword()
    {
        return $this->password;
    }

    public function eraseCredentials()
    {

    }

    /**
     * @return bool
     */
    public function isAccountNonExpired()
    {
        return $this->isCredentialsNonExpired;
    }

    /**
     * @param bool $isAccountNonExpired
     * @return $this
     */
    public function setIsAccountNonExpired($isAccountNonExpired)
    {
        $this->isAccountNonExpired = $isAccountNonExpired;

        return $this;
    }

    /**
     * @return bool
     */
    public function isAccountNonLocked()
    {
        return $this->isAccountNonLocked;
    }

    /**
     * @param bool $isAccountNonLocked
     * @return $this
     */
    public function setIsAccountNonLocked($isAccountNonLocked)
    {
        $this->isAccountNonLocked = $isAccountNonLocked;

        return $this;
    }

    /**
     * @return bool
     */
    public function isCredentialsNonExpired()
    {
        return $this->isCredentialsNonExpired;
    }

    /**
     * @param bool $isCredentialsNonExpired
     * @return $this
     */
    public function setIsCredentialsNonExpired($isCredentialsNonExpired)
    {
        $this->isCredentialsNonExpired = $isCredentialsNonExpired;

        return $this;
    }

    /**
     * @return bool
     */
    public function isEnabled()
    {
        return $this->isEnabled;
    }

    /**
     * @param bool $isEnabled
     * @return $this
     */
    public function setIsEnabled($isEnabled)
    {
        $this->isEnabled = $isEnabled;

        return $this;
    }

    /**
     * @return array
     */
    public function getRoles()
    {
        return $this->roles;
    }

    /**
     * @param array $roles
     * @return $this
     */
    public function setRoles($roles)
    {
        $this->roles = $roles;

        return $this;
    }

    public function getSalt()
    {
        return $this->salt;
    }

    /**
     * Get isEnabled
     *
     * @return boolean
     */
    public function getIsEnabled()
    {
        return $this->isEnabled;
    }

    /**
     * Get isAccountNonExpired
     *
     * @return boolean
     */
    public function getIsAccountNonExpired()
    {
        return $this->isAccountNonExpired;
    }

    /**
     * Get isAccountNonLocked
     *
     * @return boolean
     */
    public function getIsAccountNonLocked()
    {
        return $this->isAccountNonLocked;
    }

    /**
     * Get isCredentialsNonExpired
     *
     * @return boolean
     */
    public function getIsCredentialsNonExpired()
    {
        return $this->isCredentialsNonExpired;
    }
}
