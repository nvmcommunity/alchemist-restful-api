<?php

namespace Nvmcommunity\Alchemist\RestfulApi\Common\Objects;

use Nvmcommunity\Alchemist\RestfulApi\FieldSelector\Objects\FieldObject;
use Nvmcommunity\Alchemist\RestfulApi\ResourceFilter\Objects\FilteringObject;
use Nvmcommunity\Alchemist\RestfulApi\ResourcePaginations\OffsetPaginator\Objects\OffsetPaginateObject;
use Nvmcommunity\Alchemist\RestfulApi\ResourceSearch\Objects\ResourceSearchObject;
use Nvmcommunity\Alchemist\RestfulApi\ResourceSort\Objects\ResourceSortObject;

class ParamBag
{
    /**
     * @var FieldObject[]
     */
    private array $fields;
    /**
     * @var FilteringObject[]
     */
    private array $filtering;
    /**
     * @var OffsetPaginateObject
     */
    private OffsetPaginateObject $offsetPaginate;
    /**
     * @var ResourceSearchObject
     */
    private ResourceSearchObject $search;
    /**
     * @var ResourceSortObject
     */
    private ResourceSortObject $sort;

    public function __construct(
        array $fields,
        $filtering,
        OffsetPaginateObject $offsetPaginate,
        ResourceSearchObject $search,
        ResourceSortObject $sort
    )
    {
        $this->fields = $fields;
        $this->filtering = $filtering;
        $this->offsetPaginate = $offsetPaginate;
        $this->search = $search;
        $this->sort = $sort;
    }

    /**
     * @return array
     */
    public function getFields(): array
    {
        return $this->fields;
    }

    /**
     * @return array
     */
    public function getFiltering(): array
    {
        return $this->filtering;
    }

    /**
     * @return OffsetPaginateObject
     */
    public function getOffsetPaginate(): OffsetPaginateObject
    {
        return $this->offsetPaginate;
    }

    /**
     * @return ResourceSearchObject
     */
    public function getSearch(): ResourceSearchObject
    {
        return $this->search;
    }

    /**
     * @return ResourceSortObject
     */
    public function getSort(): ResourceSortObject
    {
        return $this->sort;
    }
}