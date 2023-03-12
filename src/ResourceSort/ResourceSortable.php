<?php
namespace Nvmcommunity\Alchemist\RestfulApi\ResourceSort;

use Nvmcommunity\Alchemist\RestfulApi\ResourceSort\Handlers\ResourceSort;

trait ResourceSortable
{
    /**
     * @var ResourceSort
     */
    private ResourceSort $resourceSort;

    /**
     * @param string $sort
     * @param string $direction
     * @return void
     */
    private function initResourceSort(string $sort, string $direction): void
    {
        $this->resourceSort = new ResourceSort($sort, $direction);
    }

    /**
     * @return ResourceSort
     */
    public function resourceSort(): ResourceSort
    {
        return $this->resourceSort;
    }
}