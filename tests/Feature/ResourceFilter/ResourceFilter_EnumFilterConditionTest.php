<?php

namespace Feature\ResourceFilter;

use Nvmcommunity\Alchemist\RestfulApi\AlchemistRestfulApi;
use Nvmcommunity\Alchemist\RestfulApi\Common\Exceptions\AlchemistRestfulApiException;
use Nvmcommunity\Alchemist\RestfulApi\ResourceFilter\Objects\FilteringRules;
use Nvmcommunity\Alchemist\RestfulApi\ResourceFilter\Objects\InvalidFilteringValue;
use PHPUnit\Framework\TestCase;

class ResourceFilter_EnumFilterConditionTest extends TestCase
{
    /**
     * @throws AlchemistRestfulApiException
     */
    public function test_ResourceFilter_in_NormalCase_with_EnumCondition_must_pass(): void
    {
        $restfulApi = new AlchemistRestfulApi([
            'filtering' => [
                'condition:eq' => 'a',
                'condition:ne' => 'a',
                'condition:gte' => 'a',
                'condition:lte' => 'a',
                'condition:gt' => 'a',
                'condition:lt' => 'a',
                'condition:contains' => 'abc',
                'condition:in' => ['a','b','c'],
                'condition:not_in' => ['a','b','c'],
                'condition:between' => ['a','b'],
                'condition:not_between' => ['a','b'],
            ]
        ]);

        $restfulApi->resourceFilter()->defineFilteringRules([FilteringRules::Enum(
            'condition',
            ['eq', 'is', 'ne', 'gte', 'lte', 'gt', 'lt', 'in', 'not_in', 'contains', 'between', 'not_between'],
            ['a', 'b', 'c']
        )]);

        $this->assertTrue($restfulApi->validate()->passes());

        $this->assertSame([
            "filtering" => "condition",
            "operator" => "=",
            "filteringValue" => "a",
        ], $restfulApi->resourceFilter()->filtering()[0]->toArray(), 'eq');

        $this->assertSame([
            "filtering" => "condition",
            "operator" => "!=",
            "filteringValue" => "a",
        ], $restfulApi->resourceFilter()->filtering()[1]->toArray(), 'ne');

        $this->assertSame([
            "filtering" => "condition",
            "operator" => ">=",
            "filteringValue" => "a",
        ], $restfulApi->resourceFilter()->filtering()[2]->toArray(), 'gte');

        $this->assertSame([
            "filtering" => "condition",
            "operator" => "<=",
            "filteringValue" => "a",
        ], $restfulApi->resourceFilter()->filtering()[3]->toArray(), 'lte');

        $this->assertSame([
            "filtering" => "condition",
            "operator" => ">",
            "filteringValue" => "a",
        ], $restfulApi->resourceFilter()->filtering()[4]->toArray(), 'gt');

        $this->assertSame([
            "filtering" => "condition",
            "operator" => "<",
            "filteringValue" => "a",
        ], $restfulApi->resourceFilter()->filtering()[5]->toArray(), 'lt');

        $this->assertSame([
            "filtering" => "condition",
            "operator" => "contains",
            "filteringValue" => "abc",
        ], $restfulApi->resourceFilter()->filtering()[6]->toArray(), 'contains');

        $this->assertSame([
            "filtering" => "condition",
            "operator" => "in",
            "filteringValue" => ['a','b','c'],
        ], $restfulApi->resourceFilter()->filtering()[7]->toArray(), 'in');

        $this->assertSame([
            "filtering" => "condition",
            "operator" => "not_in",
            "filteringValue" => ['a','b','c'],
        ], $restfulApi->resourceFilter()->filtering()[8]->toArray(), 'not_in');

        $this->assertSame([
            "filtering" => "condition",
            "operator" => "between",
            "filteringValue" => ['a','b'],
        ], $restfulApi->resourceFilter()->filtering()[9]->toArray(), 'between');

        $this->assertSame([
            "filtering" => "condition",
            "operator" => "not_between",
            "filteringValue" => ['a','b'],
        ], $restfulApi->resourceFilter()->filtering()[10]->toArray(), 'not_between');
    }

    /**
     * @throws AlchemistRestfulApiException
     */
    public function test_ResourceFilter_EnumCondition_with_EqOperator_but_ValueIsNotValidEnum_must_False_validation(): void
    {
        $restfulApi = new AlchemistRestfulApi([
            'filtering' => [
                'condition1:eq' => 123,
                'condition2:eq' => 123.0001,
                'condition3:eq' => true,
                'condition4:eq' => false,
                'condition5:eq' => [],
                'condition6:eq' => 'abc',
            ]
        ]);

        $restfulApi->resourceFilter()->defineFilteringRules([
            FilteringRules::Enum('condition1', ['eq'], ['a', 'b']),
            FilteringRules::Enum('condition2', ['eq'], ['a', 'b']),
            FilteringRules::Enum('condition3', ['eq'], ['a', 'b']),
            FilteringRules::Enum('condition4', ['eq'], ['a', 'b']),
            FilteringRules::Enum('condition5', ['eq'], ['a', 'b']),
            FilteringRules::Enum('condition6', ['eq'], ['a', 'b']),
        ]);

        $invalidFilteringValues = array_reduce($restfulApi->resourceFilter()->validate()->getInvalidFilteringValue(), function ($carry, InvalidFilteringValue $item) {
            $carry[$item->getFiltering()] = $item;

            return $carry;
        }, []);

        $this->assertFalse($restfulApi->validate()->passes());

        $this->assertArrayHasKey('condition1:eq', $invalidFilteringValues);
        $this->assertArrayHasKey('condition2:eq', $invalidFilteringValues);
        $this->assertArrayHasKey('condition3:eq', $invalidFilteringValues);
        $this->assertArrayHasKey('condition4:eq', $invalidFilteringValues);
        $this->assertArrayHasKey('condition5:eq', $invalidFilteringValues);
        $this->assertArrayHasKey('condition6:eq', $invalidFilteringValues);
    }

    /**
     * @throws AlchemistRestfulApiException
     */
    public function test_ResourceFilter_EnumCondition_with_NeOperator_but_ValueIsNotValidEnum_must_False_validation(): void
    {
        $restfulApi = new AlchemistRestfulApi([
            'filtering' => [
                'condition1:ne' => 123,
                'condition2:ne' => 123.0001,
                'condition3:ne' => true,
                'condition4:ne' => false,
                'condition5:ne' => [],
                'condition6:ne' => 'abc',
            ]
        ]);

        $restfulApi->resourceFilter()->defineFilteringRules([
            FilteringRules::Enum('condition1', ['ne'], ['a', 'b']),
            FilteringRules::Enum('condition2', ['ne'], ['a', 'b']),
            FilteringRules::Enum('condition3', ['ne'], ['a', 'b']),
            FilteringRules::Enum('condition4', ['ne'], ['a', 'b']),
            FilteringRules::Enum('condition5', ['ne'], ['a', 'b']),
            FilteringRules::Enum('condition6', ['ne'], ['a', 'b']),
        ]);

        $invalidFilteringValues = array_reduce($restfulApi->resourceFilter()->validate()->getInvalidFilteringValue(), function ($carry, InvalidFilteringValue $item) {
            $carry[$item->getFiltering()] = $item;

            return $carry;
        }, []);

        $this->assertFalse($restfulApi->validate()->passes());

        $this->assertArrayHasKey('condition1:ne', $invalidFilteringValues);
        $this->assertArrayHasKey('condition2:ne', $invalidFilteringValues);
        $this->assertArrayHasKey('condition3:ne', $invalidFilteringValues);
        $this->assertArrayHasKey('condition4:ne', $invalidFilteringValues);
        $this->assertArrayHasKey('condition5:ne', $invalidFilteringValues);
        $this->assertArrayHasKey('condition6:ne', $invalidFilteringValues);
    }

    /**
     * @throws AlchemistRestfulApiException
     */
    public function test_ResourceFilter_EnumCondition_with_GteOperator_but_ValueIsNotValidEnum_must_False_validation(): void
    {
        $restfulApi = new AlchemistRestfulApi([
            'filtering' => [
                'condition1:gte' => 123,
                'condition2:gte' => 123.0001,
                'condition3:gte' => true,
                'condition4:gte' => false,
                'condition5:gte' => [],
                'condition6:gte' => 'abc',
            ]
        ]);

        $restfulApi->resourceFilter()->defineFilteringRules([
            FilteringRules::Enum('condition1', ['gte'], ['a', 'b']),
            FilteringRules::Enum('condition2', ['gte'], ['a', 'b']),
            FilteringRules::Enum('condition3', ['gte'], ['a', 'b']),
            FilteringRules::Enum('condition4', ['gte'], ['a', 'b']),
            FilteringRules::Enum('condition5', ['gte'], ['a', 'b']),
            FilteringRules::Enum('condition6', ['gte'], ['a', 'b']),
        ]);

        $invalidFilteringValues = array_reduce($restfulApi->resourceFilter()->validate()->getInvalidFilteringValue(), function ($carry, InvalidFilteringValue $item) {
            $carry[$item->getFiltering()] = $item;

            return $carry;
        }, []);

        $this->assertFalse($restfulApi->validate()->passes());

        $this->assertArrayHasKey('condition1:gte', $invalidFilteringValues);
        $this->assertArrayHasKey('condition2:gte', $invalidFilteringValues);
        $this->assertArrayHasKey('condition3:gte', $invalidFilteringValues);
        $this->assertArrayHasKey('condition4:gte', $invalidFilteringValues);
        $this->assertArrayHasKey('condition5:gte', $invalidFilteringValues);
        $this->assertArrayHasKey('condition6:gte', $invalidFilteringValues);
    }

    /**
     * @throws AlchemistRestfulApiException
     */
    public function test_ResourceFilter_EnumCondition_with_LteOperator_but_ValueIsNotValidEnum_must_False_validation(): void
    {
        $restfulApi = new AlchemistRestfulApi([
            'filtering' => [
                'condition1:lte' => 123,
                'condition2:lte' => 123.0001,
                'condition3:lte' => true,
                'condition4:lte' => false,
                'condition5:lte' => [],
                'condition6:lte' => 'abc',
            ]
        ]);

        $restfulApi->resourceFilter()->defineFilteringRules([
            FilteringRules::Enum('condition1', ['lte'], ['a', 'b']),
            FilteringRules::Enum('condition2', ['lte'], ['a', 'b']),
            FilteringRules::Enum('condition3', ['lte'], ['a', 'b']),
            FilteringRules::Enum('condition4', ['lte'], ['a', 'b']),
            FilteringRules::Enum('condition5', ['lte'], ['a', 'b']),
            FilteringRules::Enum('condition6', ['lte'], ['a', 'b']),
        ]);

        $invalidFilteringValues = array_reduce($restfulApi->resourceFilter()->validate()->getInvalidFilteringValue(), function ($carry, InvalidFilteringValue $item) {
            $carry[$item->getFiltering()] = $item;

            return $carry;
        }, []);

        $this->assertFalse($restfulApi->validate()->passes());

        $this->assertArrayHasKey('condition1:lte', $invalidFilteringValues);
        $this->assertArrayHasKey('condition2:lte', $invalidFilteringValues);
        $this->assertArrayHasKey('condition3:lte', $invalidFilteringValues);
        $this->assertArrayHasKey('condition4:lte', $invalidFilteringValues);
        $this->assertArrayHasKey('condition5:lte', $invalidFilteringValues);
        $this->assertArrayHasKey('condition6:lte', $invalidFilteringValues);
    }

    /**
     * @throws AlchemistRestfulApiException
     */
    public function test_ResourceFilter_EnumCondition_with_GtOperator_but_ValueIsNotValidEnum_must_False_validation(): void
    {
        $restfulApi = new AlchemistRestfulApi([
            'filtering' => [
                'condition1:gt' => 123,
                'condition2:gt' => 123.0001,
                'condition3:gt' => true,
                'condition4:gt' => false,
                'condition5:gt' => [],
                'condition6:gt' => 'abc',
            ]
        ]);

        $restfulApi->resourceFilter()->defineFilteringRules([
            FilteringRules::Enum('condition1', ['gt'], ['a', 'b']),
            FilteringRules::Enum('condition2', ['gt'], ['a', 'b']),
            FilteringRules::Enum('condition3', ['gt'], ['a', 'b']),
            FilteringRules::Enum('condition4', ['gt'], ['a', 'b']),
            FilteringRules::Enum('condition5', ['gt'], ['a', 'b']),
            FilteringRules::Enum('condition6', ['gt'], ['a', 'b']),
        ]);

        $invalidFilteringValues = array_reduce($restfulApi->resourceFilter()->validate()->getInvalidFilteringValue(), function ($carry, InvalidFilteringValue $item) {
            $carry[$item->getFiltering()] = $item;

            return $carry;
        }, []);

        $this->assertFalse($restfulApi->validate()->passes());

        $this->assertArrayHasKey('condition1:gt', $invalidFilteringValues);
        $this->assertArrayHasKey('condition2:gt', $invalidFilteringValues);
        $this->assertArrayHasKey('condition3:gt', $invalidFilteringValues);
        $this->assertArrayHasKey('condition4:gt', $invalidFilteringValues);
        $this->assertArrayHasKey('condition5:gt', $invalidFilteringValues);
        $this->assertArrayHasKey('condition6:gt', $invalidFilteringValues);
    }

    /**
     * @throws AlchemistRestfulApiException
     */
    public function test_ResourceFilter_EnumCondition_with_LtOperator_but_ValueIsNotValidEnum_must_False_validation(): void
    {
        $restfulApi = new AlchemistRestfulApi([
            'filtering' => [
                'condition1:lt' => 123,
                'condition2:lt' => 123.0001,
                'condition3:lt' => true,
                'condition4:lt' => false,
                'condition5:lt' => [],
                'condition6:lt' => 'abc',
            ]
        ]);

        $restfulApi->resourceFilter()->defineFilteringRules([
            FilteringRules::Enum('condition1', ['lt'], ['a', 'b']),
            FilteringRules::Enum('condition2', ['lt'], ['a', 'b']),
            FilteringRules::Enum('condition3', ['lt'], ['a', 'b']),
            FilteringRules::Enum('condition4', ['lt'], ['a', 'b']),
            FilteringRules::Enum('condition5', ['lt'], ['a', 'b']),
            FilteringRules::Enum('condition6', ['lt'], ['a', 'b']),
        ]);

        $invalidFilteringValues = array_reduce($restfulApi->resourceFilter()->validate()->getInvalidFilteringValue(), function ($carry, InvalidFilteringValue $item) {
            $carry[$item->getFiltering()] = $item;

            return $carry;
        }, []);

        $this->assertFalse($restfulApi->validate()->passes());

        $this->assertArrayHasKey('condition1:lt', $invalidFilteringValues);
        $this->assertArrayHasKey('condition2:lt', $invalidFilteringValues);
        $this->assertArrayHasKey('condition3:lt', $invalidFilteringValues);
        $this->assertArrayHasKey('condition4:lt', $invalidFilteringValues);
        $this->assertArrayHasKey('condition5:lt', $invalidFilteringValues);
        $this->assertArrayHasKey('condition6:lt', $invalidFilteringValues);
    }

    /**
     * @throws AlchemistRestfulApiException
     */
    public function test_ResourceFilter_EnumCondition_with_ContainsOperator_but_ValueIsNotValidEnum_must_False_validation(): void
    {
        $restfulApi = new AlchemistRestfulApi([
            'filtering' => [
                'condition1:contains' => [],
            ]
        ]);

        $restfulApi->resourceFilter()->defineFilteringRules([
            FilteringRules::Enum('condition1', ['contains'], ['a', 'b']),
        ]);

        $invalidFilteringValues = array_reduce($restfulApi->resourceFilter()->validate()->getInvalidFilteringValue(), function ($carry, InvalidFilteringValue $item) {
            $carry[$item->getFiltering()] = $item;

            return $carry;
        }, []);

        $this->assertFalse($restfulApi->validate()->passes());

        $this->assertArrayHasKey('condition1:contains', $invalidFilteringValues);
    }

    /**
     * @throws AlchemistRestfulApiException
     */
    public function test_ResourceFilter_EnumCondition_with_InOperator_but_ValueIsNotAnArray_must_False_validation(): void
    {
        $restfulApi = new AlchemistRestfulApi([
            'filtering' => [
                'condition1:in' => 'string',
                'condition2:in' => 123,
                'condition3:in' => 123.0001,
                'condition4:in' => true,
                'condition5:in' => false,
                'condition7:in' => [true, true],
                'condition8:in' => [false, false],
                'condition9:in' => [123, 234],
                'condition10:in' => [123.0001, 123.0001],
                'condition11:in' => [123.0001, 123.0001],
                'condition12:in' => [[123.0001], [123.0001]],
                'condition13:in' => 'a',
                'condition14:in' => 'b',

            ]
        ]);

        $restfulApi->resourceFilter()->defineFilteringRules([
            FilteringRules::Enum('condition1', ['in'], ['a', 'b']),
            FilteringRules::Enum('condition2', ['in'], ['a', 'b']),
            FilteringRules::Enum('condition3', ['in'], ['a', 'b']),
            FilteringRules::Enum('condition4', ['in'], ['a', 'b']),
            FilteringRules::Enum('condition5', ['in'], ['a', 'b']),
            FilteringRules::Enum('condition7', ['in'], ['a', 'b']),
            FilteringRules::Enum('condition8', ['in'], ['a', 'b']),
            FilteringRules::Enum('condition9', ['in'], ['a', 'b']),
            FilteringRules::Enum('condition10', ['in'], ['a', 'b']),
            FilteringRules::Enum('condition11', ['in'], ['a', 'b']),
            FilteringRules::Enum('condition12', ['in'], ['a', 'b']),
            FilteringRules::Enum('condition13', ['in'], ['a', 'b']),
            FilteringRules::Enum('condition14', ['in'], ['a', 'b']),
        ]);

        $invalidFilteringValues = array_reduce($restfulApi->resourceFilter()->validate()->getInvalidFilteringValue(), function ($carry, InvalidFilteringValue $item) {
            $carry[$item->getFiltering()] = $item;

            return $carry;
        }, []);

        $this->assertFalse($restfulApi->validate()->passes());

        $this->assertArrayHasKey('condition1:in', $invalidFilteringValues);
        $this->assertArrayHasKey('condition2:in', $invalidFilteringValues);
        $this->assertArrayHasKey('condition3:in', $invalidFilteringValues);
        $this->assertArrayHasKey('condition4:in', $invalidFilteringValues);
        $this->assertArrayHasKey('condition5:in', $invalidFilteringValues);
        $this->assertArrayHasKey('condition7:in', $invalidFilteringValues);
        $this->assertArrayHasKey('condition8:in', $invalidFilteringValues);
        $this->assertArrayHasKey('condition9:in', $invalidFilteringValues);
        $this->assertArrayHasKey('condition10:in', $invalidFilteringValues);
        $this->assertArrayHasKey('condition11:in', $invalidFilteringValues);
        $this->assertArrayHasKey('condition12:in', $invalidFilteringValues);
        $this->assertArrayHasKey('condition13:in', $invalidFilteringValues);
        $this->assertArrayHasKey('condition14:in', $invalidFilteringValues);
    }

    /**
     * @throws AlchemistRestfulApiException
     */
    public function test_ResourceFilter_EnumCondition_with_NotInOperator_but_ValueIsNotAnArray_must_False_validation(): void
    {
        $restfulApi = new AlchemistRestfulApi([
            'filtering' => [
                'condition1:not_in' => 'string',
                'condition2:not_in' => 123,
                'condition3:not_in' => 123.0001,
                'condition4:not_in' => true,
                'condition5:not_in' => false,
                'condition7:not_in' => [true, true],
                'condition8:not_in' => [false, false],
                'condition9:not_in' => [123, 234],
                'condition10:not_in' => [123.0001, 123.0001],
                'condition11:not_in' => [123.0001, 123.0001],
                'condition12:not_in' => [[123.0001], [123.0001]],
                'condition13:not_in' => 'a',
                'condition14:not_in' => 'b',

            ]
        ]);

        $restfulApi->resourceFilter()->defineFilteringRules([
            FilteringRules::Enum('condition1', ['not_in'], ['a', 'b']),
            FilteringRules::Enum('condition2', ['not_in'], ['a', 'b']),
            FilteringRules::Enum('condition3', ['not_in'], ['a', 'b']),
            FilteringRules::Enum('condition4', ['not_in'], ['a', 'b']),
            FilteringRules::Enum('condition5', ['not_in'], ['a', 'b']),
            FilteringRules::Enum('condition7', ['not_in'], ['a', 'b']),
            FilteringRules::Enum('condition8', ['not_in'], ['a', 'b']),
            FilteringRules::Enum('condition9', ['not_in'], ['a', 'b']),
            FilteringRules::Enum('condition10', ['not_in'], ['a', 'b']),
            FilteringRules::Enum('condition11', ['not_in'], ['a', 'b']),
            FilteringRules::Enum('condition12', ['not_in'], ['a', 'b']),
            FilteringRules::Enum('condition13', ['not_in'], ['a', 'b']),
            FilteringRules::Enum('condition14', ['not_in'], ['a', 'b']),
        ]);

        $invalidFilteringValues = array_reduce($restfulApi->resourceFilter()->validate()->getInvalidFilteringValue(), function ($carry, InvalidFilteringValue $item) {
            $carry[$item->getFiltering()] = $item;

            return $carry;
        }, []);

        $this->assertFalse($restfulApi->validate()->passes());

        $this->assertArrayHasKey('condition1:not_in', $invalidFilteringValues);
        $this->assertArrayHasKey('condition2:not_in', $invalidFilteringValues);
        $this->assertArrayHasKey('condition3:not_in', $invalidFilteringValues);
        $this->assertArrayHasKey('condition4:not_in', $invalidFilteringValues);
        $this->assertArrayHasKey('condition5:not_in', $invalidFilteringValues);
        $this->assertArrayHasKey('condition7:not_in', $invalidFilteringValues);
        $this->assertArrayHasKey('condition8:not_in', $invalidFilteringValues);
        $this->assertArrayHasKey('condition9:not_in', $invalidFilteringValues);
        $this->assertArrayHasKey('condition10:not_in', $invalidFilteringValues);
        $this->assertArrayHasKey('condition11:not_in', $invalidFilteringValues);
        $this->assertArrayHasKey('condition12:not_in', $invalidFilteringValues);
        $this->assertArrayHasKey('condition13:not_in', $invalidFilteringValues);
        $this->assertArrayHasKey('condition14:not_in', $invalidFilteringValues);
    }

    /**
     * @throws AlchemistRestfulApiException
     */
    public function test_ResourceFilter_EnumCondition_with_BetweenOperator_but_ValueIsNotAnArray_must_False_validation(): void
    {
        $restfulApi = new AlchemistRestfulApi([
            'filtering' => [
                'condition1:between' => 'string',
                'condition2:between' => 123,
                'condition3:between' => 123.0001,
                'condition4:between' => true,
                'condition5:between' => false,
                'condition6:between' => ['string1', 'string2', 'string3'], // incorrect value format (array(<value[0]>, <value[1]>)
                'condition7:between' => [true, true],
                'condition8:between' => [false, false],
                'condition9:between' => [123, 234],
                'condition10:between' => [123.0001, 123.0001],
                'condition11:between' => [123.0001, 123.0001],
                'condition12:between' => [[123.0001], [123.0001]],
                'condition13:between' => 'a',
                'condition14:between' => 'b'
            ]
        ]);

        $restfulApi->resourceFilter()->defineFilteringRules([
            FilteringRules::Enum('condition1', ['between'], ['a', 'b']),
            FilteringRules::Enum('condition2', ['between'], ['a', 'b']),
            FilteringRules::Enum('condition3', ['between'], ['a', 'b']),
            FilteringRules::Enum('condition4', ['between'], ['a', 'b']),
            FilteringRules::Enum('condition5', ['between'], ['a', 'b']),
            FilteringRules::Enum('condition6', ['between'], ['a', 'b']),
            FilteringRules::Enum('condition7', ['between'], ['a', 'b']),
            FilteringRules::Enum('condition8', ['between'], ['a', 'b']),
            FilteringRules::Enum('condition9', ['between'], ['a', 'b']),
            FilteringRules::Enum('condition10', ['between'], ['a', 'b']),
            FilteringRules::Enum('condition11', ['between'], ['a', 'b']),
            FilteringRules::Enum('condition12', ['between'], ['a', 'b']),
            FilteringRules::Enum('condition13', ['between'], ['a', 'b']),
            FilteringRules::Enum('condition14', ['between'], ['a', 'b']),
        ]);

        $invalidFilteringValues = array_reduce($restfulApi->resourceFilter()->validate()->getInvalidFilteringValue(), function ($carry, InvalidFilteringValue $item) {
            $carry[$item->getFiltering()] = $item;

            return $carry;
        }, []);

        $this->assertFalse($restfulApi->validate()->passes());

        $this->assertArrayHasKey('condition1:between', $invalidFilteringValues);
        $this->assertArrayHasKey('condition2:between', $invalidFilteringValues);
        $this->assertArrayHasKey('condition3:between', $invalidFilteringValues);
        $this->assertArrayHasKey('condition4:between', $invalidFilteringValues);
        $this->assertArrayHasKey('condition5:between', $invalidFilteringValues);
        $this->assertArrayHasKey('condition6:between', $invalidFilteringValues);
        $this->assertArrayHasKey('condition7:between', $invalidFilteringValues);
        $this->assertArrayHasKey('condition8:between', $invalidFilteringValues);
        $this->assertArrayHasKey('condition9:between', $invalidFilteringValues);
        $this->assertArrayHasKey('condition10:between', $invalidFilteringValues);
        $this->assertArrayHasKey('condition11:between', $invalidFilteringValues);
        $this->assertArrayHasKey('condition12:between', $invalidFilteringValues);
        $this->assertArrayHasKey('condition13:between', $invalidFilteringValues);
        $this->assertArrayHasKey('condition14:between', $invalidFilteringValues);
    }

    /**
     * @throws AlchemistRestfulApiException
     */
    public function test_ResourceFilter_EnumCondition_with_NotBetweenOperator_but_ValueIsNotAnArray_must_False_validation(): void
    {
        $restfulApi = new AlchemistRestfulApi([
            'filtering' => [
                'condition1:not_between' => 'string',
                'condition2:not_between' => 123,
                'condition3:not_between' => 123.0001,
                'condition4:not_between' => true,
                'condition5:not_between' => false,
                'condition6:not_between' => ['string1', 'string2', 'string3'], // incorrect value format (array(<value[0]>, <value[1]>)
                'condition7:not_between' => [true, true],
                'condition8:not_between' => [false, false],
                'condition9:not_between' => [123, 234],
                'condition10:not_between' => [123.0001, 123.0001],
                'condition11:not_between' => [123.0001, 123.0001],
                'condition12:not_between' => [[123.0001], [123.0001]],
                'condition13:not_between' => 'a',
                'condition14:not_between' => 'b'
            ]
        ]);

        $restfulApi->resourceFilter()->defineFilteringRules([
            FilteringRules::Enum('condition1', ['not_between'], ['a', 'b']),
            FilteringRules::Enum('condition2', ['not_between'], ['a', 'b']),
            FilteringRules::Enum('condition3', ['not_between'], ['a', 'b']),
            FilteringRules::Enum('condition4', ['not_between'], ['a', 'b']),
            FilteringRules::Enum('condition5', ['not_between'], ['a', 'b']),
            FilteringRules::Enum('condition6', ['not_between'], ['a', 'b']),
            FilteringRules::Enum('condition7', ['not_between'], ['a', 'b']),
            FilteringRules::Enum('condition8', ['not_between'], ['a', 'b']),
            FilteringRules::Enum('condition9', ['not_between'], ['a', 'b']),
            FilteringRules::Enum('condition10', ['not_between'], ['a', 'b']),
            FilteringRules::Enum('condition11', ['not_between'], ['a', 'b']),
            FilteringRules::Enum('condition12', ['not_between'], ['a', 'b']),
            FilteringRules::Enum('condition13', ['not_between'], ['a', 'b']),
            FilteringRules::Enum('condition14', ['not_between'], ['a', 'b']),
        ]);

        $invalidFilteringValues = array_reduce($restfulApi->resourceFilter()->validate()->getInvalidFilteringValue(), function ($carry, InvalidFilteringValue $item) {
            $carry[$item->getFiltering()] = $item;

            return $carry;
        }, []);

        $this->assertFalse($restfulApi->validate()->passes());

        $this->assertArrayHasKey('condition1:not_between', $invalidFilteringValues);
        $this->assertArrayHasKey('condition2:not_between', $invalidFilteringValues);
        $this->assertArrayHasKey('condition3:not_between', $invalidFilteringValues);
        $this->assertArrayHasKey('condition4:not_between', $invalidFilteringValues);
        $this->assertArrayHasKey('condition5:not_between', $invalidFilteringValues);
        $this->assertArrayHasKey('condition6:not_between', $invalidFilteringValues);
        $this->assertArrayHasKey('condition7:not_between', $invalidFilteringValues);
        $this->assertArrayHasKey('condition8:not_between', $invalidFilteringValues);
        $this->assertArrayHasKey('condition9:not_between', $invalidFilteringValues);
        $this->assertArrayHasKey('condition10:not_between', $invalidFilteringValues);
        $this->assertArrayHasKey('condition11:not_between', $invalidFilteringValues);
        $this->assertArrayHasKey('condition12:not_between', $invalidFilteringValues);
        $this->assertArrayHasKey('condition13:not_between', $invalidFilteringValues);
        $this->assertArrayHasKey('condition14:not_between', $invalidFilteringValues);
    }
}
