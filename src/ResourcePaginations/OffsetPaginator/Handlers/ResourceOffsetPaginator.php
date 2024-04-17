<?php

namespace Nvmcommunity\Alchemist\RestfulApi\ResourcePaginations\OffsetPaginator\Handlers;

use Nvmcommunity\Alchemist\RestfulApi\ResourcePaginations\OffsetPaginator\Notifications\ResourceOffsetPaginationErrorBag;
use Nvmcommunity\Alchemist\RestfulApi\ResourcePaginations\OffsetPaginator\Objects\OffsetPaginateObject;

class ResourceOffsetPaginator
{
    /**
     * @var int
     */
    private int $defaultLimit = 0;
    /**
     * @var int
     */
    private int $maxLimit = 0;
    /**
     * @var int
     */
    private int $limit;
    /**
     * @var int
     */
    private int $offset;

    /**
     * @param int $limit
     * @param int $offset
     */
    public function __construct(int $limit, int $offset)
    {
        $this->limit = $limit;
        $this->offset = $offset;
    }

    /**
     * @param int $defaultLimit
     * @return ResourceOffsetPaginator
     */
    public function defineDefaultLimit(int $defaultLimit): self
    {
        $this->defaultLimit = $defaultLimit;

        return $this;
    }

    /**
     * @param int $maxLimit
     * @return ResourceOffsetPaginator
     */
    public function defineMaxLimit(int $maxLimit): self
    {
        $this->maxLimit = $maxLimit;

        return $this;
    }

    /**
     * @return OffsetPaginateObject
     */
    public function offsetPaginate(): OffsetPaginateObject
    {
        return new OffsetPaginateObject($this->limit ?: $this->defaultLimit, $this->offset, $this->maxLimit);
    }

    /**
     * @param $notification
     * @return ResourceOffsetPaginationErrorBag
     */
    public function validate(&$notification = null): ResourceOffsetPaginationErrorBag
    {
        $passes = true;
        $isNegativeOffset = false;
        $maxLimitReached = false;

        if ($this->maxLimit > 0 && $this->maxLimit < $this->limit) {
            $passes = false;
            $maxLimitReached = true;
        }

        if ($this->offset < 0) {
            $passes = false;
            $isNegativeOffset = true;
        }

        return $notification = new ResourceOffsetPaginationErrorBag($passes, $maxLimitReached, $isNegativeOffset);
    }
}