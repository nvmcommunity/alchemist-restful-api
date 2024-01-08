<?php

namespace Nvmcommunity\Alchemist\RestfulApi\FieldSelector\Objects\Structure\Abstracts;

abstract class FieldStructure
{
    private string $name;
    private ?string $substitute;
    private array $fields;

    public function __construct(string $name, ?string $substitute, array $fields)
    {
        $this->name = $name;
        $this->substitute = $substitute;
        $this->fields = $fields;
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function getSubstitute(): ?string
    {
        return $this->substitute;
    }

    public function getFields(): array
    {
        return $this->fields;
    }
}