<?php

namespace Nvmcommunity\Alchemist\RestfulApi\ResourceFilter\Objects;

class FilteringObject
{
    private string $filtering;
    private string $operator;
    private mixed $filteringValue;

    public function __construct(string $filtering, string $operator, $filteringValue)
    {
        $this->filtering = $filtering;
        $this->operator = $operator;
        $this->filteringValue = $filteringValue;
    }

    /**
     * @return string
     */
    public function filtering(): string
    {
        return $this->filtering;
    }

    /**
     * @return string
     */
    public function operator(): string
    {
        return $this->operator;
    }

    /**
     * @return mixed
     */
    public function filteringValue(): mixed
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