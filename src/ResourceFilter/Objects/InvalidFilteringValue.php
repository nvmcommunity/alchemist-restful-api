<?php

namespace Nvmcommunity\Alchemist\RestfulApi\ResourceFilter\Objects;

class InvalidFilteringValue
{
    /**
     * @var string
     */
    private string $filtering;

    /**
     * @var string[]
     */
    private array $supportedType;

    /**
     * @var array|null
     */
    private ?array $supportedValue;

    /**
     * @var array|null
     */
    private ?array $supportedFormats;

    /**
     * @param string $filtering
     * @param string[] $supportedType
     * @param array|null $supportedValue
     * @param array|null $supportedFormats
     */
    public function __construct(string $filtering, array $supportedType, ?array $supportedValue = null, ?array $supportedFormats = null)
    {
        $this->filtering = $filtering;
        $this->supportedType = $supportedType;
        $this->supportedValue = $supportedValue;
        $this->supportedFormats = $supportedFormats;
    }

    /**
     * @return string
     */
    public function getFiltering(): string
    {
        return $this->filtering;
    }

    /**
     * @return string[]
     */
    public function getSupportedType(): array
    {
        return $this->supportedType;
    }

    /**
     * @return array|null
     */
    public function getSupportedValue(): ?array
    {
        return $this->supportedValue;
    }

    /**
     * @return array|null
     */
    public function getSupportedFormats(): ?array
    {
        return $this->supportedFormats;
    }

    /**
     * @return array
     */
    public function toArray(): array
    {
        return [
            'filtering' => $this->filtering,
            'supported_type' => $this->supportedType,
            'supported_value' => $this->supportedValue,
            'supported_format' => $this->supportedFormats,
        ];
    }
}