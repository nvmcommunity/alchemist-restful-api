<?php

namespace Nvmcommunity\Alchemist\RestfulApi\ResourceFilter\Objects;

class FilteringOptions
{
    /**
     * Example: ['filter_01:eq', 'filter_02:any', 'v_string:any', 'v_enum:is', 'v_collection.valid:in']
     * + eq, lte, gte,...: specific operator
     * + any: any operator
     *
     * @var array
     */
    private array $required = [];
    /**
     * Example: ['s_string:any' => ['v_collection.valid:in' => [1]]]
     *
     * @var array
     */
    private array $required_if = [];
    /**
     * Example: ['required_with' => ['s_string:any' => ['v_enum:eq', 'v_collection.valid:not_in']]]
     *
     * @var array
     */
    private array $required_with = [];

    /**
     * @param array $params
     */
    public function __construct(array $params)
    {
        foreach ($params as $field => $value) {
            $this->{$field} = $value;
        }
    }

    /**
     * @return array
     */
    public function getRequired(): array
    {
        return $this->required;
    }

    /**
     * @return array
     */
    public function getRequiredIf(): array
    {
        return $this->required_if;
    }

    /**
     * @return array
     */
    public function getRequiredWith(): array
    {
        return $this->required_with;
    }
}