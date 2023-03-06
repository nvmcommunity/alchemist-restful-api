<?php

namespace Nvmcommunity\Alchemist\RestfulApi\FieldSelector\Objects;

class FieldObject
{
    /**
     * Field name
     *
     * @var string
     */
    private string $name;

    /**
     * sub resource limit (optional)
     *
     * @var int
     */
    private int $limit;

    /**
     * Sub resource fields (optional)
     *
     * @var string[]
     */
    private array $subFields;

    /**
     * @param string $fieldName
     * @param int $limit
     * @param string[] $subFields
     */
    public function __construct(string $fieldName, int $limit = 0, array $subFields = [])
    {
        $this->name = $fieldName;
        $this->limit = $limit;
        $this->subFields = $subFields;
    }

    /**
     * @return string
     */
    public function getName(): string
    {
        return $this->name;
    }

    /**
     * @return int
     */
    public function getLimit(): int
    {
        return $this->limit;
    }

    /**
     * @return string[]
     */
    public function getSubFields(): array
    {
        return $this->subFields;
    }

    /**
     * @return string
     */
    public function __toString(): string
    {
        return $this->name;
    }
}