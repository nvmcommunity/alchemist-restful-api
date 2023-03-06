<?php

namespace Nvmcommunity\Alchemist\RestfulApi\ResourceFilter;

use Nvmcommunity\Alchemist\RestfulApi\ResourceFilter\Handlers\ResourceFilter;

trait ResourceFilterable
{
    private ResourceFilter $resourceFilter;

    /**
     * @param array $filtering
     * @return void
     */
    public function initResourceFilter(array $filtering): void
    {
        $this->resourceFilter = new ResourceFilter($filtering);
    }

    /**
     * @return ResourceFilter
     */
    public function resourceFilter(): ResourceFilter
    {
        return $this->resourceFilter;
    }
}