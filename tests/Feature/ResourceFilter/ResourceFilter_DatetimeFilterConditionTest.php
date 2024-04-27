<?php

namespace Feature\ResourceFilter;

use Nvmcommunity\Alchemist\RestfulApi\AlchemistRestfulApi;
use Nvmcommunity\Alchemist\RestfulApi\Common\Exceptions\AlchemistRestfulApiException;
use Nvmcommunity\Alchemist\RestfulApi\ResourceFilter\Objects\FilteringRules;
use Nvmcommunity\Alchemist\RestfulApi\ResourceFilter\Objects\InvalidFilteringValue;
use PHPUnit\Framework\TestCase;

class ResourceFilter_DatetimeFilterConditionTest extends TestCase
{
    /**
     * @throws AlchemistRestfulApiException
     */
    public function test_ResourceFilter_in_NormalCase_with_DatetimeCondition_must_pass(): void
    {
        $restfulApi = new AlchemistRestfulApi([
            'filtering' => [
                'condition:eq' => '2024-04-22 00:00:00',
                'condition:ne' => '2024-04-22 00:00:00',
                'condition:gte' => '2024-04-22 00:00:00',
                'condition:lte' => '2024-04-22 00:00:00',
                'condition:gt' => '2024-04-22 00:00:00',
                'condition:lt' => '2024-04-22 00:00:00',
                'condition:contains' => '2024',
                'condition:in' => ['2024-04-22 00:00:00', '2024-04-23 00:00:00', '2024-04-24 00:00:00'],
                'condition:not_in' => ['2024-04-22 00:00:00', '2024-04-23 00:00:00', '2024-04-24 00:00:00'],
                'condition:between' => ['2024-04-22 00:00:00', '2024-04-24 00:00:00'],
                'condition:not_between' => ['2024-04-22 00:00:00', '2024-04-24 00:00:00'],
            ]
        ]);

        $restfulApi->resourceFilter()->defineFilteringRules([FilteringRules::Datetime(
            'condition', ['eq', 'is', 'ne', 'gte', 'lte', 'gt', 'lt', 'in', 'not_in', 'contains', 'between', 'not_between'],
        )]);

        $this->assertTrue($restfulApi->validate()->passes());

        $this->assertSame([
            "filtering" => "condition",
            "operator" => "=",
            "filteringValue" => "2024-04-22 00:00:00",
        ], $restfulApi->resourceFilter()->filtering()[0]->toArray(), 'eq');

        $this->assertSame([
            "filtering" => "condition",
            "operator" => "!=",
            "filteringValue" => "2024-04-22 00:00:00",
        ], $restfulApi->resourceFilter()->filtering()[1]->toArray(), 'ne');

        $this->assertSame([
            "filtering" => "condition",
            "operator" => ">=",
            "filteringValue" => "2024-04-22 00:00:00",
        ], $restfulApi->resourceFilter()->filtering()[2]->toArray(), 'gte');

        $this->assertSame([
            "filtering" => "condition",
            "operator" => "<=",
            "filteringValue" => "2024-04-22 00:00:00",
        ], $restfulApi->resourceFilter()->filtering()[3]->toArray(), 'lte');

        $this->assertSame([
            "filtering" => "condition",
            "operator" => ">",
            "filteringValue" => "2024-04-22 00:00:00",
        ], $restfulApi->resourceFilter()->filtering()[4]->toArray(), 'gt');

        $this->assertSame([
            "filtering" => "condition",
            "operator" => "<",
            "filteringValue" => "2024-04-22 00:00:00",
        ], $restfulApi->resourceFilter()->filtering()[5]->toArray(), 'lt');

        $this->assertSame([
            "filtering" => "condition",
            "operator" => "contains",
            "filteringValue" => "2024",
        ], $restfulApi->resourceFilter()->filtering()[6]->toArray(), 'contains');

        $this->assertSame([
            "filtering" => "condition",
            "operator" => "in",
            "filteringValue" => ['2024-04-22 00:00:00', '2024-04-23 00:00:00', '2024-04-24 00:00:00'],
        ], $restfulApi->resourceFilter()->filtering()[7]->toArray(), 'in');

        $this->assertSame([
            "filtering" => "condition",
            "operator" => "not_in",
            "filteringValue" => ['2024-04-22 00:00:00', '2024-04-23 00:00:00', '2024-04-24 00:00:00'],
        ], $restfulApi->resourceFilter()->filtering()[8]->toArray(), 'not_in');

        $this->assertSame([
            "filtering" => "condition",
            "operator" => "between",
            "filteringValue" => ['2024-04-22 00:00:00', '2024-04-24 00:00:00'],
        ], $restfulApi->resourceFilter()->filtering()[9]->toArray(), 'between');

        $this->assertSame([
            "filtering" => "condition",
            "operator" => "not_between",
            "filteringValue" => ['2024-04-22 00:00:00', '2024-04-24 00:00:00'],
        ], $restfulApi->resourceFilter()->filtering()[10]->toArray(), 'not_between');
    }

    /**
     * @throws AlchemistRestfulApiException
     */
    public function test_ResourceFilter_DatetimeCondition_with_EqOperator_but_ValueIsNotAValidDatetime_must_False_validation(): void
    {
        $restfulApi = new AlchemistRestfulApi([
            'filtering' => [
                'condition1:eq' => 123,
                'condition2:eq' => 123.0001,
                'condition3:eq' => true,
                'condition4:eq' => false,
                'condition5:eq' => [],
                'condition6:eq' => 'string1',
            ]
        ]);

        $restfulApi->resourceFilter()->defineFilteringRules([
            FilteringRules::Datetime('condition1', ['eq']),
            FilteringRules::Datetime('condition2', ['eq']),
            FilteringRules::Datetime('condition3', ['eq']),
            FilteringRules::Datetime('condition4', ['eq']),
            FilteringRules::Datetime('condition5', ['eq']),
            FilteringRules::Datetime('condition6', ['eq']),
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
    public function test_ResourceFilter_DatetimeCondition_with_NeOperator_but_ValueIsNotAValidDatetime_must_False_validation(): void
    {
        $restfulApi = new AlchemistRestfulApi([
            'filtering' => [
                'condition1:ne' => 123,
                'condition2:ne' => 123.0001,
                'condition3:ne' => true,
                'condition4:ne' => false,
                'condition5:ne' => [],
                'condition6:ne' => 'string1',
            ]
        ]);

        $restfulApi->resourceFilter()->defineFilteringRules([
            FilteringRules::Datetime('condition1', ['ne']),
            FilteringRules::Datetime('condition2', ['ne']),
            FilteringRules::Datetime('condition3', ['ne']),
            FilteringRules::Datetime('condition4', ['ne']),
            FilteringRules::Datetime('condition5', ['ne']),
            FilteringRules::Datetime('condition6', ['ne']),
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
    public function test_ResourceFilter_DatetimeCondition_with_GteOperator_but_ValueIsNotAValidDatetime_must_False_validation(): void
    {
        $restfulApi = new AlchemistRestfulApi([
            'filtering' => [
                'condition1:gte' => 123,
                'condition2:gte' => 123.0001,
                'condition3:gte' => true,
                'condition4:gte' => false,
                'condition5:gte' => [],
                'condition6:gte' => 'string1',
            ]
        ]);

        $restfulApi->resourceFilter()->defineFilteringRules([
            FilteringRules::Datetime('condition1', ['gte']),
            FilteringRules::Datetime('condition2', ['gte']),
            FilteringRules::Datetime('condition3', ['gte']),
            FilteringRules::Datetime('condition4', ['gte']),
            FilteringRules::Datetime('condition5', ['gte']),
            FilteringRules::Datetime('condition6', ['gte']),
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
    public function test_ResourceFilter_DatetimeCondition_with_LteOperator_but_ValueIsNotAValidDatetime_must_False_validation(): void
    {
        $restfulApi = new AlchemistRestfulApi([
            'filtering' => [
                'condition1:lte' => 123,
                'condition2:lte' => 123.0001,
                'condition3:lte' => true,
                'condition4:lte' => false,
                'condition5:lte' => [],
                'condition6:lte' => 'string1',
            ]
        ]);

        $restfulApi->resourceFilter()->defineFilteringRules([
            FilteringRules::Datetime('condition1', ['lte']),
            FilteringRules::Datetime('condition2', ['lte']),
            FilteringRules::Datetime('condition3', ['lte']),
            FilteringRules::Datetime('condition4', ['lte']),
            FilteringRules::Datetime('condition5', ['lte']),
            FilteringRules::Datetime('condition6', ['lte']),
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
    public function test_ResourceFilter_DatetimeCondition_with_GtOperator_but_ValueIsNotAValidDatetime_must_False_validation(): void
    {
        $restfulApi = new AlchemistRestfulApi([
            'filtering' => [
                'condition1:gt' => 123,
                'condition2:gt' => 123.0001,
                'condition3:gt' => true,
                'condition4:gt' => false,
                'condition5:gt' => [],
                'condition6:gt' => 'string1',
            ]
        ]);

        $restfulApi->resourceFilter()->defineFilteringRules([
            FilteringRules::Datetime('condition1', ['gt']),
            FilteringRules::Datetime('condition2', ['gt']),
            FilteringRules::Datetime('condition3', ['gt']),
            FilteringRules::Datetime('condition4', ['gt']),
            FilteringRules::Datetime('condition5', ['gt']),
            FilteringRules::Datetime('condition6', ['gt']),
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
    public function test_ResourceFilter_DatetimeCondition_with_LtOperator_but_ValueIsNotAValidDatetime_must_False_validation(): void
    {
        $restfulApi = new AlchemistRestfulApi([
            'filtering' => [
                'condition1:lt' => 123,
                'condition2:lt' => 123.0001,
                'condition3:lt' => true,
                'condition4:lt' => false,
                'condition5:lt' => [],
                'condition6:lt' => 'string1',
            ]
        ]);

        $restfulApi->resourceFilter()->defineFilteringRules([
            FilteringRules::Datetime('condition1', ['lt']),
            FilteringRules::Datetime('condition2', ['lt']),
            FilteringRules::Datetime('condition3', ['lt']),
            FilteringRules::Datetime('condition4', ['lt']),
            FilteringRules::Datetime('condition5', ['lt']),
            FilteringRules::Datetime('condition6', ['lt']),
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
    public function test_ResourceFilter_DatetimeCondition_with_ContainsOperator_but_ValueIsNotAValidDatetime_must_False_validation(): void
    {
        $restfulApi = new AlchemistRestfulApi([
            'filtering' => [
                'condition1:contains' => 123,
                'condition2:contains' => 123.0001,
                'condition3:contains' => true,
                'condition4:contains' => false,
                'condition5:contains' => [],
            ]
        ]);

        $restfulApi->resourceFilter()->defineFilteringRules([
            FilteringRules::Datetime('condition1', ['contains']),
            FilteringRules::Datetime('condition2', ['contains']),
            FilteringRules::Datetime('condition3', ['contains']),
            FilteringRules::Datetime('condition4', ['contains']),
            FilteringRules::Datetime('condition5', ['contains']),
        ]);

        $invalidFilteringValues = array_reduce($restfulApi->resourceFilter()->validate()->getInvalidFilteringValue(), function ($carry, InvalidFilteringValue $item) {
            $carry[$item->getFiltering()] = $item;

            return $carry;
        }, []);

        $this->assertFalse($restfulApi->validate()->passes());

        $this->assertArrayHasKey('condition1:contains', $invalidFilteringValues);
        $this->assertArrayHasKey('condition2:contains', $invalidFilteringValues);
        $this->assertArrayHasKey('condition3:contains', $invalidFilteringValues);
        $this->assertArrayHasKey('condition4:contains', $invalidFilteringValues);
        $this->assertArrayHasKey('condition5:contains', $invalidFilteringValues);
    }

    /**
     * @throws AlchemistRestfulApiException
     */
    public function test_ResourceFilter_DatetimeCondition_with_InOperator_but_ValueIsNotAnArray_must_False_validation(): void
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
                'condition11:in' => ['string1', 'string1'],
                'condition12:in' => [[123.0001], [123.0001]],
            ]
        ]);

        $restfulApi->resourceFilter()->defineFilteringRules([
            FilteringRules::Datetime('condition1', ['in']),
            FilteringRules::Datetime('condition2', ['in']),
            FilteringRules::Datetime('condition3', ['in']),
            FilteringRules::Datetime('condition4', ['in']),
            FilteringRules::Datetime('condition5', ['in']),
            FilteringRules::Datetime('condition7', ['in']),
            FilteringRules::Datetime('condition8', ['in']),
            FilteringRules::Datetime('condition9', ['in']),
            FilteringRules::Datetime('condition10', ['in']),
            FilteringRules::Datetime('condition11', ['in']),
            FilteringRules::Datetime('condition12', ['in']),
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
    }

    /**
     * @throws AlchemistRestfulApiException
     */
    public function test_ResourceFilter_DatetimeCondition_with_NotInOperator_but_ValueIsNotAnArray_must_False_validation(): void
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
                'condition11:not_in' => ['string1', 'string1'],
                'condition12:not_in' => [[123.0001], [123.0001]],
            ]
        ]);

        $restfulApi->resourceFilter()->defineFilteringRules([
            FilteringRules::Datetime('condition1', ['not_in']),
            FilteringRules::Datetime('condition2', ['not_in']),
            FilteringRules::Datetime('condition3', ['not_in']),
            FilteringRules::Datetime('condition4', ['not_in']),
            FilteringRules::Datetime('condition5', ['not_in']),
            FilteringRules::Datetime('condition7', ['not_in']),
            FilteringRules::Datetime('condition8', ['not_in']),
            FilteringRules::Datetime('condition9', ['not_in']),
            FilteringRules::Datetime('condition10', ['not_in']),
            FilteringRules::Datetime('condition11', ['not_in']),
            FilteringRules::Datetime('condition12', ['not_in']),
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
    }

    /**
     * @throws AlchemistRestfulApiException
     */
    public function test_ResourceFilter_DatetimeCondition_with_BetweenOperator_but_ValueIsNotAnArray_must_False_validation(): void
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
                'condition10:between' => ['string1', 'string1'],
                'condition11:between' => [123.0001, 123.0001],
                'condition12:between' => [[123.0001], [123.0001]],
            ]
        ]);

        $restfulApi->resourceFilter()->defineFilteringRules([
            FilteringRules::Datetime('condition1', ['between']),
            FilteringRules::Datetime('condition2', ['between']),
            FilteringRules::Datetime('condition3', ['between']),
            FilteringRules::Datetime('condition4', ['between']),
            FilteringRules::Datetime('condition5', ['between']),
            FilteringRules::Datetime('condition6', ['between']),
            FilteringRules::Datetime('condition7', ['between']),
            FilteringRules::Datetime('condition8', ['between']),
            FilteringRules::Datetime('condition9', ['between']),
            FilteringRules::Datetime('condition10', ['between']),
            FilteringRules::Datetime('condition11', ['between']),
            FilteringRules::Datetime('condition12', ['between']),
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
    }

    /**
     * @throws AlchemistRestfulApiException
     */
    public function test_ResourceFilter_DatetimeCondition_with_NotBetweenOperator_but_ValueIsNotAnArray_must_False_validation(): void
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
                'condition11:not_between' => ['string1', 'string1'],
                'condition12:not_between' => [[123.0001], [123.0001]],
            ]
        ]);

        $restfulApi->resourceFilter()->defineFilteringRules([
            FilteringRules::Datetime('condition1', ['not_between']),
            FilteringRules::Datetime('condition2', ['not_between']),
            FilteringRules::Datetime('condition3', ['not_between']),
            FilteringRules::Datetime('condition4', ['not_between']),
            FilteringRules::Datetime('condition5', ['not_between']),
            FilteringRules::Datetime('condition6', ['not_between']),
            FilteringRules::Datetime('condition7', ['not_between']),
            FilteringRules::Datetime('condition8', ['not_between']),
            FilteringRules::Datetime('condition9', ['not_between']),
            FilteringRules::Datetime('condition10', ['not_between']),
            FilteringRules::Datetime('condition11', ['not_between']),
            FilteringRules::Datetime('condition12', ['not_between']),
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
    }
}
