<?php

namespace Nvmcommunity\Alchemist\RestfulApi\ResourcePaginations\OffsetPaginator\Handlers;

use Nvmcommunity\Alchemist\RestfulApi\ResourcePaginations\OffsetPaginator\Notifications\ResourceOffsetPaginationValidationNotification;
use Nvmcommunity\Alchemist\RestfulApi\ResourcePaginations\OffsetPaginator\Objects\OffsetPaginateObject;

class ResourceOffsetPaginator
{
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
        return new OffsetPaginateObject($this->limit ?: $this->maxLimit, $this->offset, $this->maxLimit);
    }

    /**
     * @param $notification
     * @return ResourceOffsetPaginationValidationNotification
     */
    public function validate(&$notification = null): ResourceOffsetPaginationValidationNotification
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

        return $notification = new ResourceOffsetPaginationValidationNotification($passes, $maxLimitReached, $isNegativeOffset);
    }
}