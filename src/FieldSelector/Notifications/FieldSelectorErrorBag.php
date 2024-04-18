<?php

namespace Nvmcommunity\Alchemist\RestfulApi\FieldSelector\Notifications;

class FieldSelectorErrorBag
{
    public const UNSELECTABLE_FIELD = 'UNSELECTABLE_FIELD';
    public const INVALID_INPUT_TYPE = 'INVALID_INPUT_TYPE';

    /**
     * Has passed all the tests.
     *
     * @var bool
     */
    private bool $passes;

    /**
     * Error selecting fields not defined in the selectable fields.
     *
     * @var string[]
     */
    private array $unselectableFields;

    /**
     * The namespace where error has occurred
     *
     * @var string
     */
    private string $namespace;

    /**
     * The input type is invalid.
     *
     * @var bool
     */
    private bool $invalidInputType;

    /**
     * @param bool $passes
     * @param string $namespace
     * @param string[] $unselectableFields
     * @param bool $invalidInputType
     */
    public function __construct(
        bool $passes,
        string $namespace = '',
        array $unselectableFields = [],
        bool $invalidInputType = false
    ) {
        $this->passes = $passes;
        $this->namespace = $namespace;
        $this->unselectableFields = $unselectableFields;
        $this->invalidInputType = $invalidInputType;
    }

    /**
     * @return array<bool,string,array<string>>
     */
    public function toArray(): array
    {
        return [
            'passes' => $this->passes,
            'namespace' => $this->namespace,
            'unselectable_fields' => $this->unselectableFields,
            'invalid_input_type' => $this->invalidInputType,
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

        if ($this->isInvalidInputType()) {
            $messages[] = [
                'error_code' => static::INVALID_INPUT_TYPE,
                'error_message' => "The input type is invalid. It must be a string.",
            ];
        }

        if ($this->hasUnselectableFields()) {
            $messages[] = [
                'error_code' => static::UNSELECTABLE_FIELD,
                'error_message' => "You are trying to select fields that are not selectable.",
                'error_namespace' => $this->getNamespace(),
                'error_fields' => $this->getUnselectableFields(),
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
     * @return string
     */
    public function getNamespace(): string
    {
        return $this->namespace;
    }

    /**
     * @return string[]
     */
    public function getUnselectableFields(): array
    {
        return $this->unselectableFields;
    }

    /**
     * @return bool
     */
    public function hasUnselectableFields(): bool
    {
        return ! empty($this->unselectableFields);
    }

    /**
     * @return bool
     */
    public function isInvalidInputType(): bool
    {
        return $this->invalidInputType;
    }
}