<?php

namespace Nvmcommunity\Alchemist\RestfulApi\ResourceFilter\Objects;

class FilteringRules
{
    public const TYPE_STRING    = 'TYPE_STRING';
    public const TYPE_ENUM      = 'TYPE_ENUM';
    public const TYPE_BOOLEAN   = 'TYPE_BOOLEAN';
    public const TYPE_DATE      = 'TYPE_DATE';
    public const TYPE_DATETIME  = 'TYPE_DATETIME';
    public const TYPE_INTEGER   = 'TYPE_INTEGER';
    public const TYPE_NUMBER    = 'TYPE_NUMBER';
    public const TYPE_GROUP     = 'TYPE_GROUP';

    private ?string $name = null;
    private ?array $operators = [];
    private ?string $type = null;
    private array $enums = [];
    private array $formats = [];

    /** @var FilteringRules[] */
    private array $items = [];

    private array $flippedOperators = [];
    private array $flippedEnums = [];
    private array $nameMappedItems = [];

    private function __construct(array $params)
    {
        foreach ($params as $field => $value) {
            $this->{$field} = $value;
        }

        $this->nameMappedItems = [];

        foreach ($this->items as $item) {
            $this->nameMappedItems[$item->getName()] = $item;
        }

        $this->flippedOperators = array_flip($this->operators);
        $this->flippedEnums = array_flip($this->enums);
    }

    #---------------------------------------------------------------------------#
    # Define Instances                                                          #
    #---------------------------------------------------------------------------#

    /**
     * @param string $name
     * @param array $operators
     *
     * @return FilteringRules
     */
    public static function String(string $name, array $operators): FilteringRules
    {
        return new FilteringRules(['type' => self::TYPE_STRING, 'name' => $name, 'operators' => $operators]);
    }

    /**
     * @param string $name
     * @param array $operators
     *
     * @return FilteringRules
     */
    public static function Integer(string $name, array $operators): FilteringRules
    {
        return new FilteringRules(['type' => self::TYPE_INTEGER, 'name' => $name, 'operators' => $operators]);
    }

    /**
     * @param string $name
     * @param array $operators
     *
     * @return FilteringRules
     */
    public static function Number(string $name, array $operators): FilteringRules
    {
        return new FilteringRules(['type' => self::TYPE_NUMBER, 'name' => $name, 'operators' => $operators]);
    }

    /**
     * @param string $name
     * @param array $operators
     * @param array $formats
     *
     * @return FilteringRules
     */
    public static function Date(string $name, array $operators, array $formats = ['Y-m-d']): FilteringRules
    {
        return new FilteringRules(['type' => self::TYPE_DATE, 'name' => $name, 'operators' => $operators, 'formats' => $formats]);
    }

    /**
     * @param string $name
     * @param array $operators
     * @param array $formats
     *
     * @return FilteringRules
     */
    public static function Datetime(string $name, array $operators, array $formats = ['Y-m-d H:i:s']): FilteringRules
    {
        return new FilteringRules(['type' => self::TYPE_DATETIME, 'name' => $name, 'operators' => $operators, 'formats' => $formats]);
    }

    /**
     * @param string $name
     * @param array $operators
     * @param array $enums
     *
     * @return FilteringRules
     */
    public static function Enum(string $name, array $operators, array $enums): FilteringRules
    {
        return new FilteringRules(['type' => self::TYPE_ENUM, 'name' => $name, 'operators' => $operators, 'enums' => $enums]);
    }

    /**
     * @param string $name
     * @param array $extraOperators [empty, duplicate]
     *
     * @return FilteringRules
     */
    public static function Boolean(string $name, array $extraOperators = []): FilteringRules
    {
        return new FilteringRules(['type' => self::TYPE_BOOLEAN, 'name' => $name, 'operators' => array_unique(array_merge(['is', 'eq'], $extraOperators))]);
    }

    /**
     * @param string $name
     * @param array $items
     *
     * @return FilteringRules
     */
    public static function Group(string $name, array $items): FilteringRules
    {
        return new FilteringRules(['type' => self::TYPE_GROUP, 'name' => $name, 'operators' => ['eq'], 'items' => $items]);
    }

    #---------------------------------------------------------------------------#
    # Getter Methods                                                            #
    #---------------------------------------------------------------------------#

    /**
     * @return string|null
     */
    public function getName(): ?string
    {
        return $this->name;
    }

    /**
     * @return array
     */
    public function getOperators(): array
    {
        return $this->operators;
    }

    /**
     * @return string|null
     */
    public function getType(): ?string
    {
        return $this->type;
    }

    /**
     * @return array
     */
    public function getEnums(): array
    {
        return $this->enums;
    }

    /**
     * @return FilteringRules[]
     */
    public function getItems(): array
    {
        return $this->items;
    }

    /**
     * @return array
     */
    public function getFormats(): array
    {
        return $this->formats;
    }

    /**
     * @return array
     */
    public function getNameMappedItems(): array
    {
        return $this->nameMappedItems;
    }

    #---------------------------------------------------------------------------#
    # Helpers                                                                   #
    #---------------------------------------------------------------------------#

    /**
     * @param string $itemName
     *
     * @return FilteringRules|null
     */
    public function collectItemByName(string $itemName): ?FilteringRules
    {
        return $this->nameMappedItems[$itemName] ?? null;
    }

    /**
     * @param string $operator
     *
     * @return bool
     */
    public function hasOperator(string $operator): bool
    {
        return isset($this->flippedOperators[$operator]);
    }

    /**
     * @param $enum
     *
     * @return bool
     */
    public function hasEnum($enum): bool
    {
        return isset($this->flippedEnums[$enum]);
    }

    /**
     * @param string $itemName
     *
     * @return bool
     */
    public function hasItem(string $itemName): bool
    {
        return isset($this->nameMappedItems[$itemName]);
    }
}