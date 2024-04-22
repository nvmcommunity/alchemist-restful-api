<?php

namespace Feature;

use Nvmcommunity\Alchemist\RestfulApi\AlchemistRestfulApi;
use Nvmcommunity\Alchemist\RestfulApi\Common\Exceptions\AlchemistRestfulApiException;
use Nvmcommunity\Alchemist\RestfulApi\ResourceFilter\Objects\FilteringRules;
use PHPUnit\Framework\TestCase;

class ResourceFilterTest extends TestCase
{
    /**
     * @throws AlchemistRestfulApiException
     */
    public function test_ResourceFilter_in_NormalCase_with_StringCondition_must_pass(): void
    {
        $restfulApi = new AlchemistRestfulApi([
            'filtering' => [
                'condition:eq' => 'qwerty',
                'condition:ne' => 'qwerty',
                'condition:gte' => '1',
                'condition:lte' => '1',
                'condition:gt' => '1',
                'condition:lt' => '1',
                'condition:contains' => 'qwerty',
                'condition:in' => ['1','2','3'],
                'condition:not_in' => ['1','2','3'],
                'condition:between' => ['1','2'],
                'condition:not_between' => ['1','2'],
            ]
        ]);

        $restfulApi->resourceFilter()->defineFilteringRules([FilteringRules::String('condition', ['eq', 'is', 'ne', 'gte', 'lte', 'gt', 'lt', 'in', 'not_in', 'contains', 'between', 'not_between'])]);

        $this->assertTrue($restfulApi->validate()->passes());

        $this->assertSame([
            "filtering" => "condition",
            "operator" => "=",
            "filteringValue" => "qwerty",
        ], $restfulApi->resourceFilter()->filtering()[0]->toArray(), 'eq');

        $this->assertSame([
            "filtering" => "condition",
            "operator" => "!=",
            "filteringValue" => "qwerty",
        ], $restfulApi->resourceFilter()->filtering()[1]->toArray(), 'ne');

        $this->assertSame([
            "filtering" => "condition",
            "operator" => ">=",
            "filteringValue" => '1',
        ], $restfulApi->resourceFilter()->filtering()[2]->toArray(), 'gte');

        $this->assertSame([
            "filtering" => "condition",
            "operator" => "<=",
            "filteringValue" => '1',
        ], $restfulApi->resourceFilter()->filtering()[3]->toArray(), 'lte');

        $this->assertSame([
            "filtering" => "condition",
            "operator" => ">",
            "filteringValue" => '1',
        ], $restfulApi->resourceFilter()->filtering()[4]->toArray(), 'gt');

        $this->assertSame([
            "filtering" => "condition",
            "operator" => "<",
            "filteringValue" => '1',
        ], $restfulApi->resourceFilter()->filtering()[5]->toArray(), 'lt');

        $this->assertSame([
            "filtering" => "condition",
            "operator" => "contains",
            "filteringValue" => "qwerty",
        ], $restfulApi->resourceFilter()->filtering()[6]->toArray(), 'contains');

        $this->assertSame([
            "filtering" => "condition",
            "operator" => "in",
            "filteringValue" => ['1','2','3'],
        ], $restfulApi->resourceFilter()->filtering()[7]->toArray(), 'in');

        $this->assertSame([
            "filtering" => "condition",
            "operator" => "not_in",
            "filteringValue" => ['1','2','3'],
        ], $restfulApi->resourceFilter()->filtering()[8]->toArray(), 'not_in');

        $this->assertSame([
            "filtering" => "condition",
            "operator" => "between",
            "filteringValue" => ['1','2'],
        ], $restfulApi->resourceFilter()->filtering()[9]->toArray(), 'between');

        $this->assertSame([
            "filtering" => "condition",
            "operator" => "not_between",
            "filteringValue" => ['1','2'],
        ], $restfulApi->resourceFilter()->filtering()[10]->toArray(), 'not_between');
    }

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
    public function test_ResourceFilter_in_NormalCase_with_DateCondition_must_pass(): void
    {
        $restfulApi = new AlchemistRestfulApi([
            'filtering' => [
                'condition:eq' => '2024-04-22',
                'condition:ne' => '2024-04-22',
                'condition:gte' => '2024-04-22',
                'condition:lte' => '2024-04-22',
                'condition:gt' => '2024-04-22',
                'condition:lt' => '2024-04-22',
                'condition:contains' => '2024',
                'condition:in' => ['2024-04-22', '2024-04-23', '2024-04-24'],
                'condition:not_in' => ['2024-04-22', '2024-04-23', '2024-04-24'],
                'condition:between' => ['2024-04-22', '2024-04-24'],
                'condition:not_between' => ['2024-04-22', '2024-04-24'],
            ]
        ]);

        $restfulApi->resourceFilter()->defineFilteringRules([FilteringRules::Date(
            'condition', ['eq', 'is', 'ne', 'gte', 'lte', 'gt', 'lt', 'in', 'not_in', 'contains', 'between', 'not_between'],
        )]);

        $this->assertTrue($restfulApi->validate()->passes());

        $this->assertSame([
            "filtering" => "condition",
            "operator" => "=",
            "filteringValue" => "2024-04-22",
        ], $restfulApi->resourceFilter()->filtering()[0]->toArray(), 'eq');

        $this->assertSame([
            "filtering" => "condition",
            "operator" => "!=",
            "filteringValue" => "2024-04-22",
        ], $restfulApi->resourceFilter()->filtering()[1]->toArray(), 'ne');

        $this->assertSame([
            "filtering" => "condition",
            "operator" => ">=",
            "filteringValue" => "2024-04-22",
        ], $restfulApi->resourceFilter()->filtering()[2]->toArray(), 'gte');

        $this->assertSame([
            "filtering" => "condition",
            "operator" => "<=",
            "filteringValue" => "2024-04-22",
        ], $restfulApi->resourceFilter()->filtering()[3]->toArray(), 'lte');

        $this->assertSame([
            "filtering" => "condition",
            "operator" => ">",
            "filteringValue" => "2024-04-22",
        ], $restfulApi->resourceFilter()->filtering()[4]->toArray(), 'gt');

        $this->assertSame([
            "filtering" => "condition",
            "operator" => "<",
            "filteringValue" => "2024-04-22",
        ], $restfulApi->resourceFilter()->filtering()[5]->toArray(), 'lt');

        $this->assertSame([
            "filtering" => "condition",
            "operator" => "contains",
            "filteringValue" => "2024",
        ], $restfulApi->resourceFilter()->filtering()[6]->toArray(), 'contains');

        $this->assertSame([
            "filtering" => "condition",
            "operator" => "in",
            "filteringValue" => ['2024-04-22', '2024-04-23', '2024-04-24'],
        ], $restfulApi->resourceFilter()->filtering()[7]->toArray(), 'in');

        $this->assertSame([
            "filtering" => "condition",
            "operator" => "not_in",
            "filteringValue" => ['2024-04-22', '2024-04-23', '2024-04-24'],
        ], $restfulApi->resourceFilter()->filtering()[8]->toArray(), 'not_in');

        $this->assertSame([
            "filtering" => "condition",
            "operator" => "between",
            "filteringValue" => ['2024-04-22', '2024-04-24'],
        ], $restfulApi->resourceFilter()->filtering()[9]->toArray(), 'between');

        $this->assertSame([
            "filtering" => "condition",
            "operator" => "not_between",
            "filteringValue" => ['2024-04-22', '2024-04-24'],
        ], $restfulApi->resourceFilter()->filtering()[10]->toArray(), 'not_between');
    }

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
    public function test_ResourceFilter_in_NormalCase_with_IntegerCondition_must_pass(): void
    {
        $restfulApi = new AlchemistRestfulApi([
            'filtering' => [
                'condition:eq' => '1',
                'condition:ne' => '1',
                'condition:gte' => '1',
                'condition:lte' => '1',
                'condition:gt' => '1',
                'condition:lt' => '1',
                'condition:contains' => '2024',
                'condition:in' => ['1', '2', '3'],
                'condition:not_in' => ['1', '2', '3'],
                'condition:between' => ['1', '3'],
                'condition:not_between' => ['1', '3'],
            ]
        ]);

        $restfulApi->resourceFilter()->defineFilteringRules([FilteringRules::Integer(
            'condition', ['eq', 'is', 'ne', 'gte', 'lte', 'gt', 'lt', 'in', 'not_in', 'contains', 'between', 'not_between'],
        )]);

        $this->assertTrue($restfulApi->validate()->passes());

        $this->assertSame([
            "filtering" => "condition",
            "operator" => "=",
            "filteringValue" => 1,
        ], $restfulApi->resourceFilter()->filtering()[0]->toArray(), 'eq');

        $this->assertSame([
            "filtering" => "condition",
            "operator" => "!=",
            "filteringValue" => 1,
        ], $restfulApi->resourceFilter()->filtering()[1]->toArray(), 'ne');

        $this->assertSame([
            "filtering" => "condition",
            "operator" => ">=",
            "filteringValue" => 1,
        ], $restfulApi->resourceFilter()->filtering()[2]->toArray(), 'gte');

        $this->assertSame([
            "filtering" => "condition",
            "operator" => "<=",
            "filteringValue" => 1,
        ], $restfulApi->resourceFilter()->filtering()[3]->toArray(), 'lte');

        $this->assertSame([
            "filtering" => "condition",
            "operator" => ">",
            "filteringValue" => 1,
        ], $restfulApi->resourceFilter()->filtering()[4]->toArray(), 'gt');

        $this->assertSame([
            "filtering" => "condition",
            "operator" => "<",
            "filteringValue" => 1,
        ], $restfulApi->resourceFilter()->filtering()[5]->toArray(), 'lt');

        $this->assertSame([
            "filtering" => "condition",
            "operator" => "contains",
            "filteringValue" => 2024,
        ], $restfulApi->resourceFilter()->filtering()[6]->toArray(), 'contains');

        $this->assertSame([
            "filtering" => "condition",
            "operator" => "in",
            "filteringValue" => [1,2,3],
        ], $restfulApi->resourceFilter()->filtering()[7]->toArray(), 'in');

        $this->assertSame([
            "filtering" => "condition",
            "operator" => "not_in",
            "filteringValue" => [1,2,3],
        ], $restfulApi->resourceFilter()->filtering()[8]->toArray(), 'not_in');

        $this->assertSame([
            "filtering" => "condition",
            "operator" => "between",
            "filteringValue" => [1,3],
        ], $restfulApi->resourceFilter()->filtering()[9]->toArray(), 'between');

        $this->assertSame([
            "filtering" => "condition",
            "operator" => "not_between",
            "filteringValue" => [1,3],
        ], $restfulApi->resourceFilter()->filtering()[10]->toArray(), 'not_between');
    }

    /**
     * @throws AlchemistRestfulApiException
     */
    public function test_ResourceFilter_in_NormalCase_with_NegativeIntegerCondition_must_pass(): void
    {
        $restfulApi = new AlchemistRestfulApi([
            'filtering' => [
                'condition:eq' => '-1',
                'condition:ne' => '-1',
                'condition:gte' => '-1',
                'condition:lte' => '-1',
                'condition:gt' => '-1',
                'condition:lt' => '-1',
                'condition:contains' => '-2024',
                'condition:in' => ['-1', '-2', '-3'],
                'condition:not_in' => ['-1', '-2', '-3'],
                'condition:between' => ['-1', '-3'],
                'condition:not_between' => ['-1', '-3'],
            ]
        ]);

        $restfulApi->resourceFilter()->defineFilteringRules([FilteringRules::Integer(
            'condition', ['eq', 'is', 'ne', 'gte', 'lte', 'gt', 'lt', 'in', 'not_in', 'contains', 'between', 'not_between'],
        )]);

        $this->assertTrue($restfulApi->validate()->passes());

        $this->assertSame([
            "filtering" => "condition",
            "operator" => "=",
            "filteringValue" => -1,
        ], $restfulApi->resourceFilter()->filtering()[0]->toArray(), 'eq');

        $this->assertSame([
            "filtering" => "condition",
            "operator" => "!=",
            "filteringValue" => -1,
        ], $restfulApi->resourceFilter()->filtering()[1]->toArray(), 'ne');

        $this->assertSame([
            "filtering" => "condition",
            "operator" => ">=",
            "filteringValue" => -1,
        ], $restfulApi->resourceFilter()->filtering()[2]->toArray(), 'gte');

        $this->assertSame([
            "filtering" => "condition",
            "operator" => "<=",
            "filteringValue" => -1,
        ], $restfulApi->resourceFilter()->filtering()[3]->toArray(), 'lte');

        $this->assertSame([
            "filtering" => "condition",
            "operator" => ">",
            "filteringValue" => -1,
        ], $restfulApi->resourceFilter()->filtering()[4]->toArray(), 'gt');

        $this->assertSame([
            "filtering" => "condition",
            "operator" => "<",
            "filteringValue" => -1,
        ], $restfulApi->resourceFilter()->filtering()[5]->toArray(), 'lt');

        $this->assertSame([
            "filtering" => "condition",
            "operator" => "contains",
            "filteringValue" => -2024,
        ], $restfulApi->resourceFilter()->filtering()[6]->toArray(), 'contains');

        $this->assertSame([
            "filtering" => "condition",
            "operator" => "in",
            "filteringValue" => [-1,-2,-3],
        ], $restfulApi->resourceFilter()->filtering()[7]->toArray(), 'in');

        $this->assertSame([
            "filtering" => "condition",
            "operator" => "not_in",
            "filteringValue" => [-1,-2,-3],
        ], $restfulApi->resourceFilter()->filtering()[8]->toArray(), 'not_in');

        $this->assertSame([
            "filtering" => "condition",
            "operator" => "between",
            "filteringValue" => [-1,-3],
        ], $restfulApi->resourceFilter()->filtering()[9]->toArray(), 'between');

        $this->assertSame([
            "filtering" => "condition",
            "operator" => "not_between",
            "filteringValue" => [-1,-3],
        ], $restfulApi->resourceFilter()->filtering()[10]->toArray(), 'not_between');
    }

    /**
     * @throws AlchemistRestfulApiException
     */
    public function test_ResourceFilter_in_NormalCase_with_NumberCondition_must_pass(): void
    {
        $restfulApi = new AlchemistRestfulApi([
            'filtering' => [
                'condition:eq' => '1.0',
                'condition:ne' => '1.0',
                'condition:gte' => '1.0',
                'condition:lte' => '1.0',
                'condition:gt' => '1.0',
                'condition:lt' => '1.0',
                'condition:contains' => '2024.0',
                'condition:in' => ['1.0', '2.0', '3.0'],
                'condition:not_in' => ['1.0', '2.0', '3.0'],
                'condition:between' => ['1.0', '3.0'],
                'condition:not_between' => ['1.0', '3.0'],
            ]
        ]);

        $restfulApi->resourceFilter()->defineFilteringRules([FilteringRules::Number(
            'condition', ['eq', 'is', 'ne', 'gte', 'lte', 'gt', 'lt', 'in', 'not_in', 'contains', 'between', 'not_between'],
        )]);

        $this->assertTrue($restfulApi->validate()->passes());

        $this->assertSame([
            "filtering" => "condition",
            "operator" => "=",
            "filteringValue" => 1.0,
        ], $restfulApi->resourceFilter()->filtering()[0]->toArray(), 'eq');

        $this->assertSame([
            "filtering" => "condition",
            "operator" => "!=",
            "filteringValue" => 1.0,
        ], $restfulApi->resourceFilter()->filtering()[1]->toArray(), 'ne');

        $this->assertSame([
            "filtering" => "condition",
            "operator" => ">=",
            "filteringValue" => 1.0,
        ], $restfulApi->resourceFilter()->filtering()[2]->toArray(), 'gte');

        $this->assertSame([
            "filtering" => "condition",
            "operator" => "<=",
            "filteringValue" => 1.0,
        ], $restfulApi->resourceFilter()->filtering()[3]->toArray(), 'lte');

        $this->assertSame([
            "filtering" => "condition",
            "operator" => ">",
            "filteringValue" => 1.0,
        ], $restfulApi->resourceFilter()->filtering()[4]->toArray(), 'gt');

        $this->assertSame([
            "filtering" => "condition",
            "operator" => "<",
            "filteringValue" => 1.0,
        ], $restfulApi->resourceFilter()->filtering()[5]->toArray(), 'lt');

        $this->assertSame([
            "filtering" => "condition",
            "operator" => "contains",
            "filteringValue" => 2024.0,
        ], $restfulApi->resourceFilter()->filtering()[6]->toArray(), 'contains');

        $this->assertSame([
            "filtering" => "condition",
            "operator" => "in",
            "filteringValue" => [1.0,2.0,3.0],
        ], $restfulApi->resourceFilter()->filtering()[7]->toArray(), 'in');

        $this->assertSame([
            "filtering" => "condition",
            "operator" => "not_in",
            "filteringValue" => [1.0,2.0,3.0],
        ], $restfulApi->resourceFilter()->filtering()[8]->toArray(), 'not_in');

        $this->assertSame([
            "filtering" => "condition",
            "operator" => "between",
            "filteringValue" => [1.0,3.0],
        ], $restfulApi->resourceFilter()->filtering()[9]->toArray(), 'between');

        $this->assertSame([
            "filtering" => "condition",
            "operator" => "not_between",
            "filteringValue" => [1.0,3.0],
        ], $restfulApi->resourceFilter()->filtering()[10]->toArray(), 'not_between');
    }

    /**
     * @throws AlchemistRestfulApiException
     */
    public function test_ResourceFilter_in_NormalCase_with_NegativeNumberCondition_must_pass(): void
    {
        $restfulApi = new AlchemistRestfulApi([
            'filtering' => [
                'condition:eq' => -1.0,
                'condition:ne' => -1.0,
                'condition:gte' => -1.0,
                'condition:lte' => -1.0,
                'condition:gt' => -1.0,
                'condition:lt' => -1.0,
                'condition:contains' => -2024.0,
                'condition:in' => [-1.0,-2.0,-3.0],
                'condition:not_in' => [-1.0,-2.0,-3.0],
                'condition:between' => [-1.0,-3.0],
                'condition:not_between' => [-1.0,-3.0],
            ]
        ]);

        $restfulApi->resourceFilter()->defineFilteringRules([FilteringRules::Number(
            'condition', ['eq', 'is', 'ne', 'gte', 'lte', 'gt', 'lt', 'in', 'not_in', 'contains', 'between', 'not_between'],
        )]);

        $this->assertTrue($restfulApi->validate()->passes());

        $this->assertSame([
            "filtering" => "condition",
            "operator" => "=",
            "filteringValue" => -1.0,
        ], $restfulApi->resourceFilter()->filtering()[0]->toArray(), 'eq');

        $this->assertSame([
            "filtering" => "condition",
            "operator" => "!=",
            "filteringValue" => -1.0,
        ], $restfulApi->resourceFilter()->filtering()[1]->toArray(), 'ne');

        $this->assertSame([
            "filtering" => "condition",
            "operator" => ">=",
            "filteringValue" => -1.0,
        ], $restfulApi->resourceFilter()->filtering()[2]->toArray(), 'gte');

        $this->assertSame([
            "filtering" => "condition",
            "operator" => "<=",
            "filteringValue" => -1.0,
        ], $restfulApi->resourceFilter()->filtering()[3]->toArray(), 'lte');

        $this->assertSame([
            "filtering" => "condition",
            "operator" => ">",
            "filteringValue" => -1.0,
        ], $restfulApi->resourceFilter()->filtering()[4]->toArray(), 'gt');

        $this->assertSame([
            "filtering" => "condition",
            "operator" => "<",
            "filteringValue" => -1.0,
        ], $restfulApi->resourceFilter()->filtering()[5]->toArray(), 'lt');

        $this->assertSame([
            "filtering" => "condition",
            "operator" => "contains",
            "filteringValue" => -2024.0,
        ], $restfulApi->resourceFilter()->filtering()[6]->toArray(), 'contains');

        $this->assertSame([
            "filtering" => "condition",
            "operator" => "in",
            "filteringValue" => [-1.0,-2.0,-3.0],
        ], $restfulApi->resourceFilter()->filtering()[7]->toArray(), 'in');

        $this->assertSame([
            "filtering" => "condition",
            "operator" => "not_in",
            "filteringValue" => [-1.0,-2.0,-3.0],
        ], $restfulApi->resourceFilter()->filtering()[8]->toArray(), 'not_in');

        $this->assertSame([
            "filtering" => "condition",
            "operator" => "between",
            "filteringValue" => [-1.0,-3.0],
        ], $restfulApi->resourceFilter()->filtering()[9]->toArray(), 'between');

        $this->assertSame([
            "filtering" => "condition",
            "operator" => "not_between",
            "filteringValue" => [-1.0,-3.0],
        ], $restfulApi->resourceFilter()->filtering()[10]->toArray(), 'not_between');
    }

    /**
     * @throws AlchemistRestfulApiException
     */
    public function test_ResourceFilter_in_NormalCase_with_BooleanCondition_must_pass(): void
    {
        $restfulApi = new AlchemistRestfulApi([
            'filtering' => [
                'condition:eq' => true,
                'condition:ne' => true,
                'condition:gte' => true,
                'condition:lte' => true,
                'condition:gt' => true,
                'condition:lt' => true,
                'condition:contains' => true,
                'condition:in' => [false, true],
                'condition:not_in' => [false, true],
                'condition:between' => [false, true],
                'condition:not_between' => [false, true],
            ]
        ]);

        $restfulApi->resourceFilter()->defineFilteringRules([FilteringRules::Boolean(
            'condition', ['eq', 'is', 'ne', 'gte', 'lte', 'gt', 'lt', 'in', 'not_in', 'contains', 'between', 'not_between'],
        )]);

        $this->assertTrue($restfulApi->validate()->passes());

        $this->assertSame([
            "filtering" => "condition",
            "operator" => "=",
            "filteringValue" => true,
        ], $restfulApi->resourceFilter()->filtering()[0]->toArray(), 'eq');

        $this->assertSame([
            "filtering" => "condition",
            "operator" => "!=",
            "filteringValue" => true,
        ], $restfulApi->resourceFilter()->filtering()[1]->toArray(), 'ne');

        $this->assertSame([
            "filtering" => "condition",
            "operator" => ">=",
            "filteringValue" => true,
        ], $restfulApi->resourceFilter()->filtering()[2]->toArray(), 'gte');

        $this->assertSame([
            "filtering" => "condition",
            "operator" => "<=",
            "filteringValue" => true,
        ], $restfulApi->resourceFilter()->filtering()[3]->toArray(), 'lte');

        $this->assertSame([
            "filtering" => "condition",
            "operator" => ">",
            "filteringValue" => true,
        ], $restfulApi->resourceFilter()->filtering()[4]->toArray(), 'gt');

        $this->assertSame([
            "filtering" => "condition",
            "operator" => "<",
            "filteringValue" => true,
        ], $restfulApi->resourceFilter()->filtering()[5]->toArray(), 'lt');

        $this->assertSame([
            "filtering" => "condition",
            "operator" => "contains",
            "filteringValue" => true,
        ], $restfulApi->resourceFilter()->filtering()[6]->toArray(), 'contains');

        $this->assertSame([
            "filtering" => "condition",
            "operator" => "in",
            "filteringValue" => [false, true],
        ], $restfulApi->resourceFilter()->filtering()[7]->toArray(), 'in');

        $this->assertSame([
            "filtering" => "condition",
            "operator" => "not_in",
            "filteringValue" => [false, true],
        ], $restfulApi->resourceFilter()->filtering()[8]->toArray(), 'not_in');

        $this->assertSame([
            "filtering" => "condition",
            "operator" => "between",
            "filteringValue" => [false, true],
        ], $restfulApi->resourceFilter()->filtering()[9]->toArray(), 'between');

        $this->assertSame([
            "filtering" => "condition",
            "operator" => "not_between",
            "filteringValue" => [false, true],
        ], $restfulApi->resourceFilter()->filtering()[10]->toArray(), 'not_between');
    }

    /**
     * @throws AlchemistRestfulApiException
     */
    public function test_ResourceFilter_in_NormalCase_with_StringBooleanCondition_must_pass(): void
    {
        $restfulApi = new AlchemistRestfulApi([
            'filtering' => [
                'condition:eq' => 'true',
                'condition:ne' => 'true',
                'condition:gte' => 'true',
                'condition:lte' => 'true',
                'condition:gt' => 'true',
                'condition:lt' => 'true',
                'condition:contains' => 'true',
                'condition:in' => ['false', 'true'],
                'condition:not_in' => ['false', 'true'],
                'condition:between' => ['false', 'true'],
                'condition:not_between' => ['false', 'true'],
            ]
        ]);

        $restfulApi->resourceFilter()->defineFilteringRules([FilteringRules::Boolean(
            'condition', ['eq', 'is', 'ne', 'gte', 'lte', 'gt', 'lt', 'in', 'not_in', 'contains', 'between', 'not_between'],
        )]);

        $this->assertTrue($restfulApi->validate()->passes());

        $this->assertSame([
            "filtering" => "condition",
            "operator" => "=",
            "filteringValue" => true,
        ], $restfulApi->resourceFilter()->filtering()[0]->toArray(), 'eq');

        $this->assertSame([
            "filtering" => "condition",
            "operator" => "!=",
            "filteringValue" => true,
        ], $restfulApi->resourceFilter()->filtering()[1]->toArray(), 'ne');

        $this->assertSame([
            "filtering" => "condition",
            "operator" => ">=",
            "filteringValue" => true,
        ], $restfulApi->resourceFilter()->filtering()[2]->toArray(), 'gte');

        $this->assertSame([
            "filtering" => "condition",
            "operator" => "<=",
            "filteringValue" => true,
        ], $restfulApi->resourceFilter()->filtering()[3]->toArray(), 'lte');

        $this->assertSame([
            "filtering" => "condition",
            "operator" => ">",
            "filteringValue" => true,
        ], $restfulApi->resourceFilter()->filtering()[4]->toArray(), 'gt');

        $this->assertSame([
            "filtering" => "condition",
            "operator" => "<",
            "filteringValue" => true,
        ], $restfulApi->resourceFilter()->filtering()[5]->toArray(), 'lt');

        $this->assertSame([
            "filtering" => "condition",
            "operator" => "contains",
            "filteringValue" => true,
        ], $restfulApi->resourceFilter()->filtering()[6]->toArray(), 'contains');

        $this->assertSame([
            "filtering" => "condition",
            "operator" => "in",
            "filteringValue" => [false, true],
        ], $restfulApi->resourceFilter()->filtering()[7]->toArray(), 'in');

        $this->assertSame([
            "filtering" => "condition",
            "operator" => "not_in",
            "filteringValue" => [false, true],
        ], $restfulApi->resourceFilter()->filtering()[8]->toArray(), 'not_in');

        $this->assertSame([
            "filtering" => "condition",
            "operator" => "between",
            "filteringValue" => [false, true],
        ], $restfulApi->resourceFilter()->filtering()[9]->toArray(), 'between');

        $this->assertSame([
            "filtering" => "condition",
            "operator" => "not_between",
            "filteringValue" => [false, true],
        ], $restfulApi->resourceFilter()->filtering()[10]->toArray(), 'not_between');
    }
}
