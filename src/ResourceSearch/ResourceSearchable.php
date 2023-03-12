<?php
namespace Nvmcommunity\Alchemist\RestfulApi\ResourceSearch;

use Nvmcommunity\Alchemist\RestfulApi\ResourceSearch\Handlers\ResourceSeach;

trait ResourceSearchable
{
    /**
     * @var ResourceSeach
     */
    private ResourceSeach $resourceSearch;

    /**
     * @param string $search
     * @return void
     */
    private function initResourceSearch(string $search): void
    {
        $this->resourceSearch = new ResourceSeach($search);
    }

    /**
     * @return ResourceSeach
     */
    public function resourceSearch(): ResourceSeach
    {
        return $this->resourceSearch;
    }
}