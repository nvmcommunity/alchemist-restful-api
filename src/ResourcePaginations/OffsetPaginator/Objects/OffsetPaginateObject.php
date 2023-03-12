<?php

namespace Nvmcommunity\Alchemist\RestfulApi\ResourcePaginations\OffsetPaginator\Objects;

class OffsetPaginateObject
{
    /**
     * resource limit (optional)
     *
     * @var int
     */
    private int $limit;
    /**
     * resource offset
     *
     * @var int
     */
    private int $offset;
    /**
     * resource max limit
     *
     * @var int
     */
    private int $maxLimit;

    /**
     * @param int $limit
     * @param int $offset
     * @param int $maxLimit
     */
    public function __construct(int $limit, int $offset, int $maxLimit)
    {
        $this->offset = $offset;
        $this->limit = $limit;
        $this->maxLimit = $maxLimit;
    }

    /**
     * @return int
     */
    public function getMaxLimit(): int
    {
        return $this->maxLimit;
    }

    /**
     * @return int
     */
    public function getOffset(): int
    {
        return $this->offset;
    }

    /**
     * @return int
     */
    public function getLimit(): int
    {
        return $this->limit;
    }
}