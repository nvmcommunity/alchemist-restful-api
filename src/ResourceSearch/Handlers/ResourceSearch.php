<?php

namespace Nvmcommunity\Alchemist\RestfulApi\ResourceSearch\Handlers;

use Nvmcommunity\Alchemist\RestfulApi\ResourceSearch\Notifications\ResourceSearchErrorBag;
use Nvmcommunity\Alchemist\RestfulApi\ResourceSearch\Objects\ResourceSearchObject;

class ResourceSearch
{
    /**
     * @var string
     */
    private string $searchValue;
    /**
     * @var string
     */
    private string $searchCondition = '';

    /**
     * @param string $searchValue
     */
    public function __construct(string $searchValue)
    {
        $this->searchValue = $searchValue;
    }

    /**
     * @param string $condition
     * @return ResourceSearch
     */
    public function defineSearchCondition(string $condition): self
    {
        $this->searchCondition = $condition;

        return $this;
    }

    /**
     * @return ResourceSearchObject
     */
    public function search(): ResourceSearchObject
    {
        return new ResourceSearchObject($this->searchValue, $this->searchCondition);
    }
}