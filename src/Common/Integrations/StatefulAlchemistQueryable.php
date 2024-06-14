<?php

namespace Nvmcommunity\Alchemist\RestfulApi\Common\Integrations;

use Nvmcommunity\Alchemist\RestfulApi\Common\Integrations\Adapters\AlchemistAdapter;
use Nvmcommunity\Alchemist\RestfulApi\FieldSelector\Handlers\FieldSelector;
use Nvmcommunity\Alchemist\RestfulApi\ResourceFilter\Handlers\ResourceFilter;
use Nvmcommunity\Alchemist\RestfulApi\ResourcePaginations\OffsetPaginator\Handlers\ResourceOffsetPaginator;
use Nvmcommunity\Alchemist\RestfulApi\ResourceSearch\Handlers\ResourceSearch;
use Nvmcommunity\Alchemist\RestfulApi\ResourceSort\Handlers\ResourceSort;

abstract class StatefulAlchemistQueryable
{
    /**
     * @return AlchemistAdapter|null
     */
    public function getAdapter(): ?AlchemistAdapter
    {
        return null;
    }

    /**
     * @param FieldSelector $fieldSelector
     * @return void
     */
    public function fieldSelector(FieldSelector $fieldSelector): void
    {
    }

    /**
     * @param ResourceFilter $resourceFilter
     * @return void
     */
    public function resourceFilter(ResourceFilter $resourceFilter): void
    {
    }

    /**
     * @param ResourceOffsetPaginator $resourceOffsetPaginator
     * @return void
     */
    public function resourceOffsetPaginator(ResourceOffsetPaginator $resourceOffsetPaginator): void
    {
    }

    /**
     * @param ResourceSearch $resourceSearch
     * @return void
     */
    public function resourceSearch(ResourceSearch $resourceSearch): void
    {
    }

    /**
     * @param ResourceSort $resourceSort
     * @return void
     */
    public function resourceSort(ResourceSort $resourceSort): void
    {
    }
}