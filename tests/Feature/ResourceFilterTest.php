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
                'condition:contains' => 'a',
                'condition:in' => ['a','b','c'],
                'condition:not_in' => ['a','b','c'],
                'condition:between' => ['a','b'],
                'condition:not_between' => ['a','b'],
            ]
        ]);

        $restfulApi->resourceFilter()->defineFilteringRules([FilteringRules::Enum(
            'condition', ['eq', 'is', 'ne', 'gte', 'lte', 'gt', 'lt', 'in', 'not_in', 'contains', 'between', 'not_between'], ['a', 'b', 'c']
        )]);

        $this->assertTrue($restfulApi->validate()->passes());
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
                //'condition:contains' => '2024',
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
                //'condition:contains' => '2024',
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
    }

    /**
     * @throws AlchemistRestfulApiException
     */
    public function test_ResourceFilter_in_NormalCase_with_NegativeIntegerCondition_must_pass(): void
    {
        $restfulApi = new AlchemistRestfulApi([
            'filtering' => [
                //'condition:eq' => '-1',
                //'condition:ne' => '-1',
                //'condition:gte' => '-1',
                //'condition:lte' => '-1',
                //'condition:gt' => '-1',
                //'condition:lt' => '-1',
                //'condition:contains' => '-2024',
                //'condition:in' => ['-1', '-2', '-3'],
                //'condition:not_in' => ['-1', '-2', '-3'],
                //'condition:between' => ['-1', '-3'],
                //'condition:not_between' => ['-1', '-3'],
            ]
        ]);

        $restfulApi->resourceFilter()->defineFilteringRules([FilteringRules::Integer(
            'condition', ['eq', 'is', 'ne', 'gte', 'lte', 'gt', 'lt', 'in', 'not_in', 'contains', 'between', 'not_between'],
        )]);

        $this->assertTrue($restfulApi->validate()->passes());
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
    }

    /**
     * @throws AlchemistRestfulApiException
     */
    public function test_ResourceFilter_in_NormalCase_with_NegativeNumberCondition_must_pass(): void
    {
        $restfulApi = new AlchemistRestfulApi([
            'filtering' => [
                'condition:eq' => '-1.0',
                'condition:ne' => '-1.0',
                'condition:gte' => '-1.0',
                'condition:lte' => '-1.0',
                'condition:gt' => '-1.0',
                'condition:lt' => '-1.0',
                'condition:contains' => '-2024.0',
                'condition:in' => ['-1.0', '-2.0', '-3.0'],
                'condition:not_in' => ['-1.0', '-2.0', '-3.0'],
                'condition:between' => ['-1.0', '-3.0'],
                'condition:not_between' => ['-1.0', '-3.0'],
            ]
        ]);

        $restfulApi->resourceFilter()->defineFilteringRules([FilteringRules::Number(
            'condition', ['eq', 'is', 'ne', 'gte', 'lte', 'gt', 'lt', 'in', 'not_in', 'contains', 'between', 'not_between'],
        )]);

        $this->assertTrue($restfulApi->validate()->passes());
    }

    /**
     * @throws AlchemistRestfulApiException
     */
    public function test_ResourceFilter_in_NormalCase_with_BooleanCondition_must_pass(): void
    {
        $restfulApi = new AlchemistRestfulApi([
            'filtering' => [
                //'condition:eq' => true,
                //'condition:ne' => true,
                //'condition:gte' => true,
                //'condition:lte' => true,
                //'condition:gt' => true,
                //'condition:lt' => true,
                //'condition:contains' => true,
                //'condition:in' => [false, true],
                //'condition:not_in' => [false, true],
                //'condition:between' => [false, true],
                //'condition:not_between' => [false, true],
            ]
        ]);

        $restfulApi->resourceFilter()->defineFilteringRules([FilteringRules::Boolean(
            'condition', ['eq', 'is', 'ne', 'gte', 'lte', 'gt', 'lt', 'in', 'not_in', 'contains', 'between', 'not_between'],
        )]);

        $this->assertTrue($restfulApi->validate()->passes());
    }

    /**
     * @throws AlchemistRestfulApiException
     */
    public function test_ResourceFilter_in_NormalCase_with_StringBooleanCondition_must_pass(): void
    {
        $restfulApi = new AlchemistRestfulApi([
            'filtering' => [
                //'condition:eq' => 'true',
                //'condition:ne' => 'true',
                //'condition:gte' => 'true',
                //'condition:lte' => 'true',
                //'condition:gt' => 'true',
                //'condition:lt' => 'true',
                //'condition:contains' => 'true',
                //'condition:in' => ['true', 'true', 'true'],
                //'condition:not_in' => ['true', 'true', 'true'],
                //'condition:between' => ['true', 'true'],
                //'condition:not_between' => ['true', 'true'],
            ]
        ]);

        $restfulApi->resourceFilter()->defineFilteringRules([FilteringRules::Boolean(
            'condition', ['eq', 'is', 'ne', 'gte', 'lte', 'gt', 'lt', 'in', 'not_in', 'contains', 'between', 'not_between'],
        )]);

        $this->assertTrue($restfulApi->validate()->passes());
    }
}
