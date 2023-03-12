<?php

namespace Nvmcommunity\Alchemist\RestfulApi\ResourceSort\Objects;

class ResourceSortObject
{
    /**
     * @var string
     */
    private string $sortField;
    /**
     * @var string
     */
    private string $direction;

    /**
     * @param string $sortField
     * @param string $direction
     */
    public function __construct(string $sortField, string $direction)
    {
        $this->sortField = $sortField;
        $this->direction = $direction;
    }

    /**
     * @return string
     */
    public function getSortField(): string
    {
        return $this->sortField;
    }

    /**
     * @return string
     */
    public function getDirection(): string
    {
        return $this->direction;
    }
}