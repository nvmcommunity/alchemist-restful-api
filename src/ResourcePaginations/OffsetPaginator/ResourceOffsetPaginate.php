<?php
namespace Nvmcommunity\Alchemist\RestfulApi\ResourcePaginations\OffsetPaginator;

use Nvmcommunity\Alchemist\RestfulApi\ResourcePaginations\OffsetPaginator\Handlers\ResourceOffsetPaginator;

trait ResourceOffsetPaginate
{
    /**
     * @var ResourceOffsetPaginator
     */
    private ResourceOffsetPaginator $resourceOffsetPaginator;

    /**
     * @param int $offset
     * @param int $limit
     * @return void
     */
    private function initResourceOffsetPaginator(int $limit, int $offset): void
    {
        $this->resourceOffsetPaginator = new ResourceOffsetPaginator($limit, $offset);
    }

    /**
     * @return ResourceOffsetPaginator
     */
    public function resourceOffsetPaginator(): ResourceOffsetPaginator
    {
        return $this->resourceOffsetPaginator;
    }
}