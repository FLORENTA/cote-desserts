<?php

namespace AppBundle\Model;

/**
 * Class SearchModel
 * @package AppBundle\Model
 */
class SearchModel
{
    /** @var string|null $search */
    private $search;

    /**
     * @return string|null
     */
    public function getSearch(): ?string
    {
        return $this->search;
    }

    /**
     * @param string|null $search
     * @return SearchModel
     */
    public function setSearch(?string $search): SearchModel
    {
        $this->search = $search;

        return $this;
    }
}