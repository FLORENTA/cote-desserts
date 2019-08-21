<?php

namespace AppBundle\Model;

/**
 * Class PasswordModel
 * @package AppBundle\Model
 */
class PasswordModel
{
    /** @var string $username */
    private $username;

    /** @var string $lastPassword */
    private $lastPassword;

    /** @var string $newPassword */
    private $newPassword;

    /** @var string $confirmNewPassword */
    private $confirmNewPassword;

    /**
     * @return string|null
     */
    public function getUsername(): ?string
    {
        return $this->username;
    }

    /**
     * @param string|null $username
     * @return PasswordModel
     */
    public function setUsername(?string $username): PasswordModel
    {
        $this->username = $username;

        return $this;
    }

    /**
     * @return string|null
     */
    public function getLastPassword(): ?string
    {
        return $this->lastPassword;
    }

    /**
     * @param string|null $lastPassword
     * @return PasswordModel
     */
    public function setLastPassword(?string $lastPassword): PasswordModel
    {
        $this->lastPassword = $lastPassword;
        
        return $this;
    }

    /**
     * @return string|null
     */
    public function getNewPassword(): ?string
    {
        return $this->newPassword;
    }

    /**
     * @param string|null $newPassword
     * @return PasswordModel
     */
    public function setNewPassword(?string $newPassword): PasswordModel
    {
        $this->newPassword = $newPassword;
        
        return $this;
    }

    /**
     * @return string|null
     */
    public function getConfirmNewPassword(): ?string
    {
        return $this->confirmNewPassword;
    }

    /**
     * @param string|null $confirmNewPassword
     * @return PasswordModel
     */
    public function setConfirmNewPassword(?string $confirmNewPassword): PasswordModel
    {
        $this->confirmNewPassword = $confirmNewPassword;
        
        return $this;
    }
}