<?php

namespace Nvmcommunity\Alchemist\RestfulApi\ResourcePaginations\OffsetPaginator\Notifications;

use Nvmcommunity\Alchemist\RestfulApi\Common\Objects\BaseAlchemistErrorBag;

class ResourceOffsetPaginationErrorBag extends BaseAlchemistErrorBag
{
    public const MAX_LIMIT_REACHED = 'MAX_LIMIT_REACHED';
    public const NEGATIVE_OFFSET = 'NEGATIVE_OFFSET';
    public const NEGATIVE_LIMIT = 'NEGATIVE_LIMIT';
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
     * Limit passed in a negative value.
     *
     * @var bool
     */
    private bool $negativeLimit;

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
     * @param bool $negativeLimit
     * @param array $invalidInputTypes
     */
    public function __construct(
        bool $passes,
        bool $maxLimitReached = false,
        bool $negativeOffset = false,
        bool $negativeLimit = false,
        array $invalidInputTypes = []
    ) {
        $this->passes = $passes;
        $this->maxLimitReached = $maxLimitReached;
        $this->negativeOffset = $negativeOffset;
        $this->negativeLimit = $negativeLimit;
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
            'negative_limit' => $this->negativeLimit,
            'invalid_input_parameters' => $this->invalidInputTypes,
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
     * @return string
     */
    public function errorKey(): string
    {
        return 'paginator';
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

        if ($this->isNegativeLimit()) {
            $messages[] = [
                'error_code' => static::NEGATIVE_LIMIT,
                'error_message' => "You have passed in a negative limit. Limit must be a positive integer.",
            ];
        }

        return $messages;
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
    public function isNegativeLimit(): bool
    {
        return $this->negativeLimit;
    }

    /**
     * @return bool
     */
    public function hasInvalidInputTypes(): bool
    {
        return count($this->invalidInputTypes) > 0;
    }
}