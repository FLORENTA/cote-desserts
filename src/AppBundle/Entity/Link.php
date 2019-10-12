<?php

namespace AppBundle\Entity;

/**
 * Link
 */
class Link
{
    /**
     * @var int $id
     */
    private $id;

    /**
     * @var string $path
     */
    private $path;

    /**
     * @var string
     */
    private $token;

    /**
     * Get id.
     *
     * @return int
     */
    public function getId(): int
    {
        return $this->id;
    }

    /**
     * Set path.
     *
     * @param string $path
     *
     * @return Link
     */
    public function setPath($path): Link
    {
        $this->path = $path;

        return $this;
    }

    /**
     * Get path.
     *
     * @return string|null
     */
    public function getPath(): ?string
    {
        return $this->path;
    }

    /**
     * Set token.
     *
     * @param string $token
     *
     * @return Link
     */
    public function setToken($token): Link
    {
        $this->token = $token;

        return $this;
    }

    /**
     * Get token.
     *
     * @return string|null
     */
    public function getToken(): ?string
    {
        return $this->token;
    }
}
