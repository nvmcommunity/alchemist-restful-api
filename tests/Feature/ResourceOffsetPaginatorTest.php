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

        $this->assertTrue($restfulApi->validate()->passes());

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

        $this->assertTrue($restfulApi->validate()->passes());

        $this->assertSame(10, $restfulApi->resourceOffsetPaginator()->offsetPaginate()->getLimit());
        $this->assertSame(1, $restfulApi->resourceOffsetPaginator()->offsetPaginate()->getOffset());
    }

    /**
     * @throws AlchemistRestfulApiException
     */
    public function test_ResourceOffsetPaginator_in_NormalCase_WithNullParamValue_must_pass(): void
    {
        $restfulApi = new AlchemistRestfulApi([
            'limit' => null,
            'offset' => null,
        ]);

        $this->assertTrue($restfulApi->validate()->passes());

        $this->assertSame(0, $restfulApi->resourceOffsetPaginator()->offsetPaginate()->getLimit());
        $this->assertSame(0, $restfulApi->resourceOffsetPaginator()->offsetPaginate()->getOffset());
    }

    /**
     * @throws AlchemistRestfulApiException
     */
    public function test_ResourceOffsetPaginator_in_case_of_MissingLimitParam_then_DefaultLimitValue_is_used(): void
    {
        $restfulApi = new AlchemistRestfulApi([]);

        $restfulApi->resourceOffsetPaginator()->defineDefaultLimit(10);

        $this->assertTrue($restfulApi->validate()->passes());

        $this->assertSame(10, $restfulApi->resourceOffsetPaginator()->offsetPaginate()->getLimit());
    }

    /**
     * @throws AlchemistRestfulApiException
     */
    public function test_ResourceOffsetPaginator_in_case_of_MissingLimitParam_and_DefaultLimitValueNotDefined_then_MaxLimitValue_is_used(): void
    {
        $restfulApi = new AlchemistRestfulApi([]);

        $restfulApi->resourceOffsetPaginator()->defineMaxLimit(10);

        $this->assertTrue($restfulApi->validate()->passes());

        $this->assertSame(10, $restfulApi->resourceOffsetPaginator()->offsetPaginate()->getLimit());
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

        $this->assertSame(0, $restfulApi->resourceOffsetPaginator()->offsetPaginate()->getLimit());
        $this->assertSame(0, $restfulApi->resourceOffsetPaginator()->offsetPaginate()->getOffset());
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

        $this->assertSame(11, $restfulApi->resourceOffsetPaginator()->offsetPaginate()->getLimit());
        $this->assertSame(0, $restfulApi->resourceOffsetPaginator()->offsetPaginate()->getOffset());
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

        $this->assertSame(-1, $restfulApi->resourceOffsetPaginator()->offsetPaginate()->getLimit());
    }

    /**
     * @throws AlchemistRestfulApiException
     */
    public function test_ResourceOffsetPaginator_in_NormalCase_with_StringLimitParamValue_must_pass(): void
    {
        $restfulApi = new AlchemistRestfulApi([
            'limit' => 'abc',
        ]);

        $this->assertFalse($restfulApi->validate()->passes());

        $this->assertNotNull($restfulApi->validate()->getErrors()['paginator']);
        $this->assertTrue($restfulApi->resourceOffsetPaginator()->validate()->hasInvalidInputTypes());

        $this->assertSame(0, $restfulApi->resourceOffsetPaginator()->offsetPaginate()->getLimit());
    }

    /**
     * @throws AlchemistRestfulApiException
     */
    public function test_ResourceOffsetPaginator_in_NormalCase_with_ArrayLimitParamValue_must_pass(): void
    {
        $restfulApi = new AlchemistRestfulApi([
            'limit' => ['abc'],
        ]);

        $this->assertFalse($restfulApi->validate()->passes());

        $this->assertNotNull($restfulApi->validate()->getErrors()['paginator']);
        $this->assertTrue($restfulApi->resourceOffsetPaginator()->validate()->hasInvalidInputTypes());

        $this->assertSame(0, $restfulApi->resourceOffsetPaginator()->offsetPaginate()->getLimit());
    }

    /**
     * @throws AlchemistRestfulApiException
     */
    public function test_ResourceOffsetPaginator_in_NormalCase_with_ObjectLimitParamValue_must_pass(): void
    {
        $restfulApi = new AlchemistRestfulApi([
            'limit' => new \stdClass(),
        ]);

        $this->assertFalse($restfulApi->validate()->passes());

        $this->assertNotNull($restfulApi->validate()->getErrors()['paginator']);
        $this->assertTrue($restfulApi->resourceOffsetPaginator()->validate()->hasInvalidInputTypes());

        $this->assertSame(0, $restfulApi->resourceOffsetPaginator()->offsetPaginate()->getLimit());
    }

    /**
     * @throws AlchemistRestfulApiException
     */
    public function test_ResourceOffsetPaginator_in_NormalCase_with_TrueBooleanLimitParamValue_must_pass(): void
    {
        $restfulApi = new AlchemistRestfulApi([
            'limit' => true,
        ]);

        $this->assertFalse($restfulApi->validate()->passes());

        $this->assertNotNull($restfulApi->validate()->getErrors()['paginator']);
        $this->assertTrue($restfulApi->resourceOffsetPaginator()->validate()->hasInvalidInputTypes());

        $this->assertSame(0, $restfulApi->resourceOffsetPaginator()->offsetPaginate()->getLimit());
    }

    /**
     * @throws AlchemistRestfulApiException
     */
    public function test_ResourceOffsetPaginator_in_NormalCase_with_FalseBooleanLimitParamValue_must_pass(): void
    {
        $restfulApi = new AlchemistRestfulApi([
            'limit' => false,
        ]);

        $this->assertFalse($restfulApi->validate()->passes());

        $this->assertNotNull($restfulApi->validate()->getErrors()['paginator']);
        $this->assertTrue($restfulApi->resourceOffsetPaginator()->validate()->hasInvalidInputTypes());

        $this->assertSame(0, $restfulApi->resourceOffsetPaginator()->offsetPaginate()->getLimit());
    }

    /**
     * @throws AlchemistRestfulApiException
     */
    public function test_ResourceOffsetPaginator_in_NormalCase_with_NumericLimitParamValue_must_pass(): void
    {
        $restfulApi = new AlchemistRestfulApi([
            'limit' => 123.1,
        ]);

        $this->assertFalse($restfulApi->validate()->passes());

        $this->assertNotNull($restfulApi->validate()->getErrors()['paginator']);
        $this->assertTrue($restfulApi->resourceOffsetPaginator()->validate()->hasInvalidInputTypes());

        $this->assertSame(0, $restfulApi->resourceOffsetPaginator()->offsetPaginate()->getLimit());
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

        $this->assertSame(-1, $restfulApi->resourceOffsetPaginator()->offsetPaginate()->getOffset());
    }

    /**
     * @throws AlchemistRestfulApiException
     */
    public function test_ResourceOffsetPaginator_in_NormalCase_with_StringOffsetParamValue_must_pass(): void
    {
        $restfulApi = new AlchemistRestfulApi([
            'offset' => 'abc',
        ]);

        $this->assertFalse($restfulApi->validate()->passes());

        $this->assertNotNull($restfulApi->validate()->getErrors()['paginator']);
        $this->assertTrue($restfulApi->resourceOffsetPaginator()->validate()->hasInvalidInputTypes());

        $this->assertSame(0, $restfulApi->resourceOffsetPaginator()->offsetPaginate()->getOffset());
    }

    /**
     * @throws AlchemistRestfulApiException
     */
    public function test_ResourceOffsetPaginator_in_NormalCase_with_ArrayOffsetParamValue_must_pass(): void
    {
        $restfulApi = new AlchemistRestfulApi([
            'offset' => ['abc'],
        ]);

        $this->assertFalse($restfulApi->validate()->passes());

        $this->assertNotNull($restfulApi->validate()->getErrors()['paginator']);
        $this->assertTrue($restfulApi->resourceOffsetPaginator()->validate()->hasInvalidInputTypes());

        $this->assertSame(0, $restfulApi->resourceOffsetPaginator()->offsetPaginate()->getOffset());
    }

    /**
     * @throws AlchemistRestfulApiException
     */
    public function test_ResourceOffsetPaginator_in_NormalCase_with_ObjectOffsetParamValue_must_pass(): void
    {
        $restfulApi = new AlchemistRestfulApi([
            'offset' => new \stdClass(),
        ]);

        $this->assertFalse($restfulApi->validate()->passes());

        $this->assertNotNull($restfulApi->validate()->getErrors()['paginator']);
        $this->assertTrue($restfulApi->resourceOffsetPaginator()->validate()->hasInvalidInputTypes());

        $this->assertSame(0, $restfulApi->resourceOffsetPaginator()->offsetPaginate()->getOffset());
    }

    /**
     * @throws AlchemistRestfulApiException
     */
    public function test_ResourceOffsetPaginator_in_NormalCase_with_TrueBooleanOffsetParamValue_must_pass(): void
    {
        $restfulApi = new AlchemistRestfulApi([
            'offset' => true,
        ]);

        $this->assertFalse($restfulApi->validate()->passes());

        $this->assertNotNull($restfulApi->validate()->getErrors()['paginator']);
        $this->assertTrue($restfulApi->resourceOffsetPaginator()->validate()->hasInvalidInputTypes());

        $this->assertSame(0, $restfulApi->resourceOffsetPaginator()->offsetPaginate()->getOffset());
    }

    /**
     * @throws AlchemistRestfulApiException
     */
    public function test_ResourceOffsetPaginator_in_NormalCase_with_FalseBooleanOffsetParamValue_must_pass(): void
    {
        $restfulApi = new AlchemistRestfulApi([
            'offset' => false,
        ]);

        $this->assertFalse($restfulApi->validate()->passes());

        $this->assertNotNull($restfulApi->validate()->getErrors()['paginator']);
        $this->assertTrue($restfulApi->resourceOffsetPaginator()->validate()->hasInvalidInputTypes());

        $this->assertSame(0, $restfulApi->resourceOffsetPaginator()->offsetPaginate()->getOffset());
    }

    /**
     * @throws AlchemistRestfulApiException
     */
    public function test_ResourceOffsetPaginator_in_NormalCase_with_NumericOffsetParamValue_must_pass(): void
    {
        $restfulApi = new AlchemistRestfulApi([
            'offset' => 123.1,
        ]);

        $this->assertFalse($restfulApi->validate()->passes());

        $this->assertNotNull($restfulApi->validate()->getErrors()['paginator']);
        $this->assertTrue($restfulApi->resourceOffsetPaginator()->validate()->hasInvalidInputTypes());

        $this->assertSame(0, $restfulApi->resourceOffsetPaginator()->offsetPaginate()->getOffset());
    }
}
