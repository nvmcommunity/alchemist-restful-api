<?php

namespace Nvmcommunity\Alchemist\RestfulApi\ResourcePaginations\OffsetPaginator\Notifications;

class ResourceOffsetPaginationErrorBag
{
    /**
     * Has passed all the tests.
     *
     * @var bool
     */
    private bool $passes;
    /**
     * The limit passed in exceeds the maximum limit.
     *
     * @var bool
     */
    private bool $maxLimitReached;
    /**
     * Offset passed in a negative value.
     *
     * @var bool
     */
    private bool $negativeOffset;

    /**
     * @param bool $passes
     * @param bool $maxLimitReached
     * @param bool $negativeOffset
     */
    public function __construct(bool $passes, bool $maxLimitReached = false, bool $negativeOffset = false) {
        $this->passes = $passes;
        $this->maxLimitReached = $maxLimitReached;
        $this->negativeOffset = $negativeOffset;
    }

    /**
     * @return bool[]
     */
    public function toArray(): array
    {
        return [
            'passes' => $this->passes,
            'max_limit_reached' => $this->maxLimitReached,
            'negative_offset' => $this->negativeOffset,
        ];
    }

    /**
     * @return bool
     */
    public function passes(): bool
    {
        return $this->passes;
    }

    /**
     * @return bool
     */
    public function isMaxLimitReached(): bool
    {
        return $this->maxLimitReached;
    }

    /**
     * @return bool
     */
    public function isNegativeOffset(): bool
    {
        return $this->negativeOffset;
    }
}