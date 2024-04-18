<?php

namespace Nvmcommunity\Alchemist\RestfulApi\ResourcePaginations\OffsetPaginator\Notifications;

class ResourceOffsetPaginationErrorBag
{
    public const MAX_LIMIT_REACHED = 'MAX_LIMIT_REACHED';
    public const NEGATIVE_OFFSET = 'NEGATIVE_OFFSET';
    public const INVALID_INPUT_TYPE = 'INVALID_INPUT_TYPE';
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
     * The input type is invalid.
     *
     * @var array
     */
    private array $invalidInputTypes;

    /**
     * @param bool $passes
     * @param bool $maxLimitReached
     * @param bool $negativeOffset
     * @param array $invalidInputTypes
     */
    public function __construct(
        bool $passes,
        bool $maxLimitReached = false,
        bool $negativeOffset = false,
        array $invalidInputTypes = []
    ) {
        $this->passes = $passes;
        $this->maxLimitReached = $maxLimitReached;
        $this->negativeOffset = $negativeOffset;
        $this->invalidInputTypes = $invalidInputTypes;
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
            'invalid_input_parameters' => $this->invalidInputTypes,
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

        if ($this->hasInvalidInputTypes()) {
            $messages[] = [
                'error_code' => static::INVALID_INPUT_TYPE,
                'error_message' => "The input type is invalid.",
                'invalid_input_parameters' => $this->invalidInputTypes,
            ];
        }

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

    /**
     * @return bool
     */
    public function hasInvalidInputTypes(): bool
    {
        return count($this->invalidInputTypes) > 0;
    }
}