<?php
namespace Nvmcommunity\Alchemist\RestfulApi\ResourceSearch;

use Nvmcommunity\Alchemist\RestfulApi\ResourceSearch\Handlers\ResourceSearch;

trait ResourceSearchable
{
    /**
     * @var ResourceSearch
     */
    private ResourceSearch $resourceSearch;

    /**
     * @param string $search
     * @return void
     */
    private function initResourceSearch(string $search): void
    {
        $this->resourceSearch = new ResourceSearch($search);
    }

    /**
     * @return ResourceSearch
     */
    public function resourceSearch(): ResourceSearch
    {
        return $this->resourceSearch;
    }
}