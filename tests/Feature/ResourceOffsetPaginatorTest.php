<?php

namespace Feature;

use Nvmcommunity\Alchemist\RestfulApi\AlchemistRestfulApi;
use Nvmcommunity\Alchemist\RestfulApi\Common\Exceptions\AlchemistRestfulApiException;
use PHPUnit\Framework\TestCase;

class ResourceOffsetPaginatorTest extends TestCase
{
    /**
     * @throws AlchemistRestfulApiException
     */
    public function test_ResourceOffsetPaginator_in_NormalCase_must_pass(): void
    {
        $restfulApi = new AlchemistRestfulApi([
            'limit' => 10,
            'offset' => 0,
        ]);

        $this->assertSame(10, $restfulApi->resourceOffsetPaginator()->offsetPaginate()->getLimit());
        $this->assertSame(0, $restfulApi->resourceOffsetPaginator()->offsetPaginate()->getOffset());
    }

    /**
     * @throws AlchemistRestfulApiException
     */
    public function test_ResourceOffsetPaginator_in_NormalCase_WithStringParamValue_must_pass(): void
    {
        $restfulApi = new AlchemistRestfulApi([
            'limit' => '10',
            'offset' => '1',
        ]);

        $this->assertSame(10, $restfulApi->resourceOffsetPaginator()->offsetPaginate()->getLimit());
        $this->assertSame(1, $restfulApi->resourceOffsetPaginator()->offsetPaginate()->getOffset());
    }

    /**
     * @throws AlchemistRestfulApiException
     */
    public function test_ResourceOffsetPaginator_with_InvalidLimitParamValueType_must_FALSE_validation(): void
    {
        $restfulApi = new AlchemistRestfulApi([
            'limit' => '123_string_value'
        ]);

        $this->assertFalse($restfulApi->validate()->passes());
        $this->assertNotNull($restfulApi->validate()->getErrors()['paginator']);
        $this->assertTrue($restfulApi->resourceOffsetPaginator()->validate()->hasInvalidInputTypes());
    }

    /**
     * @throws AlchemistRestfulApiException
     */
    public function test_ResourceOffsetPaginator_with_LimitParamValueGreaterThanMaxLimit_must_FALSE_validation(): void
    {
        $restfulApi = new AlchemistRestfulApi([
            'limit' => 11
        ]);

        $restfulApi->resourceOffsetPaginator()->defineMaxLimit(10);

        $this->assertFalse($restfulApi->validate()->passes());
        $this->assertNotNull($restfulApi->validate()->getErrors()['paginator']);
        $this->assertTrue($restfulApi->resourceOffsetPaginator()->validate()->isMaxLimitReached());
    }

    /**
     * @throws AlchemistRestfulApiException
     */
    public function test_ResourceOffsetPaginator_in_case_of_MissingLimitParam_then_DefaultLimitValue_is_used(): void
    {
        $restfulApi = new AlchemistRestfulApi([]);

        $restfulApi->resourceOffsetPaginator()->defineDefaultLimit(10);

        $this->assertSame(10, $restfulApi->resourceOffsetPaginator()->offsetPaginate()->getLimit());
    }

    /**
     * @throws AlchemistRestfulApiException
     */
    public function test_ResourceOffsetPaginator_in_case_of_MissingLimitParam_and_DefaultLimitValueNotDefined_then_MaxLimitValue_is_used(): void
    {
        $restfulApi = new AlchemistRestfulApi([]);

        $restfulApi->resourceOffsetPaginator()->defineMaxLimit(10);

        $this->assertSame(10, $restfulApi->resourceOffsetPaginator()->offsetPaginate()->getLimit());
    }

    /**
     * @throws AlchemistRestfulApiException
     */
    public function test_ResourceOffsetPaginator_in_case_of_DefaultLimitValueNotDefined_and_MaxLimitValueNotDefined_then_LimitValue_is_not_set(): void
    {
        $restfulApi = new AlchemistRestfulApi([]);

        $this->assertSame(0, $restfulApi->resourceOffsetPaginator()->offsetPaginate()->getLimit());
    }

    /**
     * @throws AlchemistRestfulApiException
     */
    public function test_ResourceOffsetPaginator_in_case_of_NegativeLimitParam_must_FALSE_validation(): void
    {
        $restfulApi = new AlchemistRestfulApi([
            'limit' => -1
        ]);

        $this->assertFalse($restfulApi->validate()->passes());
        $this->assertNotNull($restfulApi->validate()->getErrors()['paginator']);
        $this->assertTrue($restfulApi->resourceOffsetPaginator()->validate()->isNegativeLimit());
    }

    /**
     * @throws AlchemistRestfulApiException
     */
    public function test_ResourceOffsetPaginator_in_case_of_NegativeOffsetParam_must_FALSE_validation(): void
    {
        $restfulApi = new AlchemistRestfulApi([
            'offset' => -1
        ]);

        $this->assertFalse($restfulApi->validate()->passes());
        $this->assertNotNull($restfulApi->validate()->getErrors()['paginator']);
        $this->assertTrue($restfulApi->resourceOffsetPaginator()->validate()->isNegativeOffset());
    }
}
