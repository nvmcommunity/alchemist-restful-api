<?php

namespace Nvmcommunity\Alchemist\RestfulApi\FieldSelector\Notifications;

class FieldSelectorErrorBag
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
     * The namespace where error has occurred
     *
     * @var string
     */
    private string $namespace;

    /**
     * @param bool $passes
     * @param string $namespace
     * @param string[] $unselectableFields
     */
    public function __construct(
        bool $passes,
        string $namespace = '',
        array $unselectableFields = []
    ) {
        $this->passes = $passes;
        $this->namespace = $namespace;
        $this->unselectableFields = $unselectableFields;
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
}