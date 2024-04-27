<?php

namespace Feature;

use Nvmcommunity\Alchemist\RestfulApi\AlchemistRestfulApi;
use Nvmcommunity\Alchemist\RestfulApi\Common\Exceptions\AlchemistRestfulApiException;
use PHPUnit\Framework\TestCase;

class ResourceSearchTest extends TestCase
{
    /**
     * @throws AlchemistRestfulApiException
     */
    public function test_ResourceSearch_in_NormalCase_must_pass(): void
    {
        $restfulApi = new AlchemistRestfulApi([
            'search' => 'qwerty',
        ]);

        $restfulApi->resourceSearch()->defineSearchCondition('title');

        $this->assertTrue($restfulApi->validate()->passes());

        $this->assertSame('title', $restfulApi->resourceSearch()->search()->getSearchCondition());
        $this->assertSame('qwerty', $restfulApi->resourceSearch()->search()->getSearchValue());
    }

    /**
     * @throws AlchemistRestfulApiException
     */
    public function test_ResourceSearch_with_SearchConditionDefined_must_pass(): void
    {
        $restfulApi = new AlchemistRestfulApi([
            'search' => 'qwerty',
        ]);

        $restfulApi->resourceSearch()->defineSearchCondition('title');

        $this->assertTrue($restfulApi->validate()->passes());

        $this->assertSame('title', $restfulApi->resourceSearch()->search()->getSearchCondition());
        $this->assertSame('qwerty', $restfulApi->resourceSearch()->search()->getSearchValue());
    }

    /**
     * @throws AlchemistRestfulApiException
     */
    public function test_ResourceSearch_without_SearchCondition_must_pass(): void
    {
        $restfulApi = new AlchemistRestfulApi([
            'search' => 'qwerty',
        ]);

        $this->assertTrue($restfulApi->validate()->passes());

        $this->assertSame('', $restfulApi->resourceSearch()->search()->getSearchCondition());
        $this->assertSame('qwerty', $restfulApi->resourceSearch()->search()->getSearchValue());
    }

    /**
     * @throws AlchemistRestfulApiException
     */
    public function test_ResourceSearch_with_NullSearchValue_must_pass(): void
    {
        $restfulApi = new AlchemistRestfulApi([
            'search' => null,
        ]);

        $this->assertTrue($restfulApi->validate()->passes());

        $this->assertSame('', $restfulApi->resourceSearch()->search()->getSearchCondition());
        $this->assertSame('', $restfulApi->resourceSearch()->search()->getSearchValue());
    }

    /**
     * @throws AlchemistRestfulApiException
     */
    public function test_ResourceSearch_with_ArraySortParamValue_must_FALSE_validation(): void
    {
        $restfulApi = new AlchemistRestfulApi([
            'search' => ['invalid_type'],
        ]);

        $this->assertFalse($restfulApi->validate()->passes());

        $this->assertNotNull($restfulApi->validate()->getErrors()['search']);
        $this->assertTrue($restfulApi->resourceSearch()->validate()->isInvalidInputType());

        $this->assertSame('', $restfulApi->resourceSearch()->search()->getSearchCondition());
        $this->assertSame('', $restfulApi->resourceSearch()->search()->getSearchValue());
    }

    /**
     * @throws AlchemistRestfulApiException
     */
    public function test_ResourceSearch_with_ObjectSortParamValue_must_FALSE_validation(): void
    {
        $restfulApi = new AlchemistRestfulApi([
            'search' => new \stdClass(),
        ]);

        $this->assertFalse($restfulApi->validate()->passes());

        $this->assertNotNull($restfulApi->validate()->getErrors()['search']);
        $this->assertTrue($restfulApi->resourceSearch()->validate()->isInvalidInputType());

        $this->assertSame('', $restfulApi->resourceSearch()->search()->getSearchCondition());
        $this->assertSame('', $restfulApi->resourceSearch()->search()->getSearchValue());
    }

    /**
     * @throws AlchemistRestfulApiException
     */
    public function test_ResourceSearch_with_TrueBooleanSortParamValue_must_FALSE_validation(): void
    {
        $restfulApi = new AlchemistRestfulApi([
            'search' => true,
        ]);

        $this->assertFalse($restfulApi->validate()->passes());

        $this->assertNotNull($restfulApi->validate()->getErrors()['search']);
        $this->assertTrue($restfulApi->resourceSearch()->validate()->isInvalidInputType());

        $this->assertSame('', $restfulApi->resourceSearch()->search()->getSearchCondition());
        $this->assertSame('', $restfulApi->resourceSearch()->search()->getSearchValue());
    }

    /**
     * @throws AlchemistRestfulApiException
     */
    public function test_ResourceSearch_with_FalseBooleanSortParamValue_must_FALSE_validation(): void
    {
        $restfulApi = new AlchemistRestfulApi([
            'search' => false,
        ]);

        $this->assertFalse($restfulApi->validate()->passes());

        $this->assertNotNull($restfulApi->validate()->getErrors()['search']);
        $this->assertTrue($restfulApi->resourceSearch()->validate()->isInvalidInputType());

        $this->assertSame('', $restfulApi->resourceSearch()->search()->getSearchCondition());
        $this->assertSame('', $restfulApi->resourceSearch()->search()->getSearchValue());
    }

    /**
     * @throws AlchemistRestfulApiException
     */
    public function test_ResourceSearch_with_IntegerBooleanSortParamValue_must_FALSE_validation(): void
    {
        $restfulApi = new AlchemistRestfulApi([
            'search' => 123,
        ]);

        $this->assertFalse($restfulApi->validate()->passes());

        $this->assertNotNull($restfulApi->validate()->getErrors()['search']);
        $this->assertTrue($restfulApi->resourceSearch()->validate()->isInvalidInputType());

        $this->assertSame('', $restfulApi->resourceSearch()->search()->getSearchCondition());
        $this->assertSame('', $restfulApi->resourceSearch()->search()->getSearchValue());
    }

    /**
     * @throws AlchemistRestfulApiException
     */
    public function test_ResourceSearch_withNumericBooleanSortParamValue_must_FALSE_validation(): void
    {
        $restfulApi = new AlchemistRestfulApi([
            'search' => 123.1,
        ]);

        $this->assertFalse($restfulApi->validate()->passes());

        $this->assertNotNull($restfulApi->validate()->getErrors()['search']);
        $this->assertTrue($restfulApi->resourceSearch()->validate()->isInvalidInputType());

        $this->assertSame('', $restfulApi->resourceSearch()->search()->getSearchCondition());
        $this->assertSame('', $restfulApi->resourceSearch()->search()->getSearchValue());
    }
}
