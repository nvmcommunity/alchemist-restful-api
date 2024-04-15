<?php

namespace Nvmcommunity\Alchemist\RestfulApi\ResourcePaginations\OffsetPaginator\Notifications;

class ResourceOffsetPaginationErrorBag
{
    public const MAX_LIMIT_REACHED = 'MAX_LIMIT_REACHED';
    public const NEGATIVE_OFFSET = 'NEGATIVE_OFFSET';
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
     * @return array
     */
    public function getMessages(): array
    {
        if ($this->passes()) {
            return [];
        }

        $messages = [];

        if ($this->isMaxLimitReached()) {
            $messages[] = [
                'error_code' => static::MAX_LIMIT_REACHED,
                'error_message' => "You have passed in a limit that exceeds the maximum limit.",
            ];
        }

        if ($this->isNegativeOffset()) {
            $messages[] = [
                'error_code' => static::NEGATIVE_OFFSET,
                'error_message' => "You have passed in a negative offset. Offset must be a positive integer.",
            ];
        }

        return $messages;
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