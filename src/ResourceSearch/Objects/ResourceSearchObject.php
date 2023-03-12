<?php

namespace Nvmcommunity\Alchemist\RestfulApi\ResourceSearch\Objects;

class ResourceSearchObject
{
    /**
     * @var string
     */
    private string $searchValue;
    /**
     * @var string
     */
    private string $searchCondition;

    /**
     * @param string $searchValue
     * @param string $searchCondition
     */
    public function __construct(string $searchValue, string $searchCondition)
    {
        $this->searchValue = $searchValue;
        $this->searchCondition = $searchCondition;
    }

    /**
     * @return string
     */
    public function getSearchValue(): string
    {
        return $this->searchValue;
    }

    /**
     * @return string
     */
    public function getSearchCondition(): string
    {
        return $this->searchCondition;
    }
}