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

        $this->assertSame('title', $restfulApi->resourceSearch()->search()->getSearchCondition());
        $this->assertSame('qwerty', $restfulApi->resourceSearch()->search()->getSearchValue());
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
        $this->assertNotNull($restfulApi->resourceSearch()->validate()->isInvalidInputType());
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

        $this->assertSame('title', $restfulApi->resourceSearch()->search()->getSearchCondition());
        $this->assertSame('qwerty', $restfulApi->resourceSearch()->search()->getSearchValue());
    }
}
