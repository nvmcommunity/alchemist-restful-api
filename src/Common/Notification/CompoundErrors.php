<?php

namespace Nvmcommunity\Alchemist\RestfulApi\Common\Notification;

use Nvmcommunity\Alchemist\RestfulApi\FieldSelector\Notifications\FieldSelectorErrorBag;
use Nvmcommunity\Alchemist\RestfulApi\ResourceFilter\Notifications\ResourceFilterErrorBag;
use Nvmcommunity\Alchemist\RestfulApi\ResourcePaginations\OffsetPaginator\Notifications\ResourceOffsetPaginationErrorBag;
use Nvmcommunity\Alchemist\RestfulApi\ResourceSort\Notifications\ResourceSortErrorBag;

class CompoundErrors
{
    /**
     * @var FieldSelectorErrorBag|null
     */
    public ?FieldSelectorErrorBag $fieldSelector = null;

    /**
     * @var ResourceFilterErrorBag|null
     */
    public ?ResourceFilterErrorBag $resourceFilter = null;

    /**
     * @var ResourceOffsetPaginationErrorBag|null
     */
    public ?ResourceOffsetPaginationErrorBag $resourceOffsetPaginator = null;

    /**
     * @var ResourceSortErrorBag|null
     */
    public ?ResourceSortErrorBag $resourceSort = null;
}