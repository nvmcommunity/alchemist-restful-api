<?php

namespace Nvmcommunity\Alchemist\RestfulApi\FieldSelector\Notifications;

class FieldSelectorValidationNotification
{
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
     * Error selecting subfields not defined in the selectable subfields.
     *
     * @var string[][]
     */
    private array $unselectableSubFields;

    /**
     * Error selecting subsidiary fields on field that do not define any subsidiary field.
     *
     * @var string[]
     */
    private array $subsidiarySelectErrors;

    /**
     * @param bool $passes
     * @param string[] $unselectableFields
     * @param string[][] $unselectableSubFields
     * @param string[] $subsidiarySelectErrors
     */
    public function __construct(
        bool $passes,
        array $unselectableFields = [],
        array $unselectableSubFields = [],
        array $subsidiarySelectErrors = []
    ) {
        $this->passes = $passes;
        $this->unselectableFields = $unselectableFields;
        $this->unselectableSubFields = $unselectableSubFields;
        $this->subsidiarySelectErrors = $subsidiarySelectErrors;
    }

    /**
     * @return array<string,array<array<string>|string>|bool>
     */
    public function toArray(): array
    {
        return [
            'passes' => $this->passes,
            'unselectable_fields' => $this->unselectableFields,
            'unselectable_sub_fields' => $this->unselectableSubFields,
            'subsidiary_select_errors' => $this->subsidiarySelectErrors,
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
     * @return string[]
     */
    public function getUnselectableFields(): array
    {
        return $this->unselectableFields;
    }

    /**
     * @return string[][]
     */
    public function getUnselectableSubFields(): array
    {
        return $this->unselectableSubFields;
    }

    /**
     * @return string[]
     */
    public function getSubsidiarySelectErrors(): array
    {
        return $this->subsidiarySelectErrors;
    }

    /**
     * @return bool
     */
    public function hasUnselectableFields(): bool
    {
        return ! empty($this->unselectableFields);
    }

    /**
     * @param string $fieldName
     * @return bool
     */
    public function hasUnselectableSubFields(string $fieldName): bool
    {
        return ! empty($this->unselectableSubFields[$fieldName]);
    }

    /**
     * @return bool
     */
    public function hasSubsidiarySelectErrors(): bool
    {
        return ! empty($this->subsidiarySelectErrors);
    }
}