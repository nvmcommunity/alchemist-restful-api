<?php

namespace Nvmcommunity\Alchemist\RestfulApi\ResourceFilter;

use Nvmcommunity\Alchemist\RestfulApi\ResourceFilter\Handlers\ResourceFilter;

trait ResourceFilterable
{
    /**
     * @var ResourceFilter
     */
    private ResourceFilter $resourceFilter;

    /**
     * @param array $filtering
     * @return void
     */
    private function initResourceFilter(array $filtering): void
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