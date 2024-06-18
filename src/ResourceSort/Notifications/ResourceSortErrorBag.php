<?php

namespace Nvmcommunity\Alchemist\RestfulApi\ResourceSort\Notifications;

use Nvmcommunity\Alchemist\RestfulApi\Common\Objects\BaseAlchemistErrorBag;

class ResourceSortErrorBag extends BaseAlchemistErrorBag
{
    public const INVALID_DIRECTION = 'INVALID_DIRECTION';
    public const INVALID_SORT_FIELD = 'INVALID_SORT_FIELD';
    public const INVALID_INPUT_TYPE = 'INVALID_INPUT_TYPE';
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
     * The input type is invalid.
     *
     * @var array
     */
    private array $invalidInputTypes;

    /**
     * @param bool $passes
     * @param bool $invalidDirection
     * @param bool $invalidSortField
     * @param array $invalidInputTypes
     */
    public function __construct(bool $passes, bool $invalidDirection, bool $invalidSortField, array $invalidInputTypes) {
        $this->passes = $passes;
        $this->invalidDirection = $invalidDirection;
        $this->invalidSortField = $invalidSortField;
        $this->invalidInputTypes = $invalidInputTypes;
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
        return 'sort';
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

    /**
     * @return bool
     */
    public function hasInvalidInputTypes(): bool
    {
        return ! empty($this->invalidInputTypes);
    }
}