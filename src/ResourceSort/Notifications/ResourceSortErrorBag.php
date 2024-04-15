<?php

namespace Nvmcommunity\Alchemist\RestfulApi\ResourceSort\Notifications;

class ResourceSortErrorBag
{
    public const INVALID_DIRECTION = 'INVALID_DIRECTION';
    public const INVALID_SORT_FIELD = 'INVALID_SORT_FIELD';
    /**
     * Has passed all the tests.
     *
     * @var bool
     */
    private bool $passes;
    /**
     * @var bool
     */
    private bool $invalidDirection;
    /**
     * @var bool
     */
    private bool $invalidSortField;

    /**
     * @param bool $passes
     * @param bool $invalidDirection
     * @param bool $invalidSortField
     */
    public function __construct(bool $passes, bool $invalidDirection, bool $invalidSortField) {
        $this->passes = $passes;
        $this->invalidDirection = $invalidDirection;
        $this->invalidSortField = $invalidSortField;
    }

    /**
     * @return bool[]
     */
    public function toArray(): array
    {
        return [
            'passes' => $this->passes,
            'invalid_direction' => $this->invalidDirection,
            'invalid_sort_field' => $this->invalidSortField,
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

        if ($this->isInvalidDirection()) {
            $messages[] = [
                'error_code' => static::INVALID_DIRECTION,
                'error_message' => "You are trying to sort by an invalid direction.",
            ];
        }

        if ($this->isInvalidSortField()) {
            $messages[] = [
                'error_code' => static::INVALID_SORT_FIELD,
                'error_message' => "You are trying to sort by a field that is not supported.",
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
    public function isInvalidDirection(): bool
    {
        return $this->invalidDirection;
    }

    /**
     * @return bool
     */
    public function isInvalidSortField(): bool
    {
        return $this->invalidSortField;
    }
}