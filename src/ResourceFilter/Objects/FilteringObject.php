<?php

namespace Nvmcommunity\Alchemist\RestfulApi\ResourceFilter\Objects;

class FilteringObject
{
    /**
     * @return string
     */
    private string $filtering;
    /**
     * @return string
     */
    private string $operator;
    /**
     * @return mixed
     */
    private $filteringValue;

    public function __construct(string $filtering, string $operator, $filteringValue)
    {
        $this->filtering = $filtering;
        $this->operator = $operator;
        $this->filteringValue = $filteringValue;
    }

    /**
     * @return string
     */
    public function getFiltering(): string
    {
        return $this->filtering;
    }

    /**
     * @return string
     */
    public function getOperator(): string
    {
        return $this->operator;
    }

    /**
     * @return mixed
     */
    public function getFilteringValue()
    {
        return $this->filteringValue;
    }

    /**
     * @return array
     */
    public function toArray(): array
    {
        return [
            'filtering' => $this->filtering,
            'operator' => $this->operator,
            'filteringValue' => $this->filteringValue,
        ];
    }

    /**
     * @return array
     */
    public function flatArray(): array
    {
        return [$this->filtering, $this->operator, $this->filteringValue];
    }
}