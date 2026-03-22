<?php

namespace Feature\ResourceFilter;

use Nvmcommunity\Alchemist\RestfulApi\AlchemistRestfulApi;
use Nvmcommunity\Alchemist\RestfulApi\Common\Exceptions\AlchemistRestfulApiException;
use Nvmcommunity\Alchemist\RestfulApi\ResourceFilter\Objects\FilteringRules;
use PHPUnit\Framework\TestCase;

class ResourceFilter_StartsWithOperatorTest extends TestCase
{
    /**
     * @throws AlchemistRestfulApiException
     */
    public function test_ResourceFilter_with_StartsWithOperator_on_String_must_pass(): void
    {
        $restfulApi = new AlchemistRestfulApi([
            'filtering' => [
                'condition:starts_with' => 'qwe',
            ]
        ]);

        $restfulApi->resourceFilter()->defineFilteringRules([
            FilteringRules::String('condition', ['starts_with'])
        ]);

        $this->assertTrue($restfulApi->validate()->passes());
        $this->assertSame([
            'filtering' => 'condition',
            'operator' => 'starts_with',
            'filteringValue' => 'qwe',
        ], $restfulApi->resourceFilter()->filtering()[0]->toArray());
    }

    /**
     * @throws AlchemistRestfulApiException
     */
    public function test_ResourceFilter_with_StartsWithOperator_on_Enum_must_accept_partial_string(): void
    {
        $restfulApi = new AlchemistRestfulApi([
            'filtering' => [
                'condition:starts_with' => 'ab',
            ]
        ]);

        $restfulApi->resourceFilter()->defineFilteringRules([
            FilteringRules::Enum('condition', ['starts_with'], ['abc', 'xyz'])
        ]);

        $this->assertTrue($restfulApi->validate()->passes());
    }

    /**
     * @throws AlchemistRestfulApiException
     */
    public function test_ResourceFilter_with_StartsWithOperator_on_Date_must_accept_string_prefix(): void
    {
        $restfulApi = new AlchemistRestfulApi([
            'filtering' => [
                'condition:starts_with' => '2024',
            ]
        ]);

        $restfulApi->resourceFilter()->defineFilteringRules([
            FilteringRules::Date('condition', ['starts_with'])
        ]);

        $this->assertTrue($restfulApi->validate()->passes());
    }

    /**
     * @throws AlchemistRestfulApiException
     */
    public function test_ResourceFilter_with_StartsWithOperator_on_Datetime_must_accept_string_prefix(): void
    {
        $restfulApi = new AlchemistRestfulApi([
            'filtering' => [
                'condition:starts_with' => '2024-01',
            ]
        ]);

        $restfulApi->resourceFilter()->defineFilteringRules([
            FilteringRules::Datetime('condition', ['starts_with'])
        ]);

        $this->assertTrue($restfulApi->validate()->passes());
    }
}
