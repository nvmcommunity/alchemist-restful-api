<?php

namespace Feature;

use Nvmcommunity\Alchemist\RestfulApi\AlchemistRestfulApi;
use Nvmcommunity\Alchemist\RestfulApi\Common\Exceptions\AlchemistRestfulApiException;
use PHPUnit\Framework\TestCase;

class ResourceSortTest extends TestCase
{
    /**
     * @throws AlchemistRestfulApiException
     */
    public function test_ResourceSort_in_NormalCase_must_pass(): void
    {
        $restfulApi = new AlchemistRestfulApi([
            'sort' => 'id',
            'direction' => 'asc',
        ]);

        $this->assertSame('id', $restfulApi->resourceSort()->sort()->getSortField());
        $this->assertSame('asc', $restfulApi->resourceSort()->sort()->getDirection());
    }

    /**
     * @throws AlchemistRestfulApiException
     */
    public function test_ResourceSort_with_ArraySortParamValue_must_FALSE_validation(): void
    {
        $restfulApi = new AlchemistRestfulApi([
            'sort' => ['invalid_type'],
        ]);

        $this->assertFalse($restfulApi->validate()->passes());
        $this->assertNotNull($restfulApi->validate()->getErrors()['sort']);
        $this->assertTrue($restfulApi->resourceSort()->validate()->hasInvalidInputTypes());
    }

    /**
     * @throws AlchemistRestfulApiException
     */
    public function test_ResourceSort_with_ArrayDirectionParamValue_must_FALSE_validation(): void
    {
        $restfulApi = new AlchemistRestfulApi([
            'direction' => ['invalid_type'],
        ]);

        $this->assertFalse($restfulApi->validate()->passes());
        $this->assertNotNull($restfulApi->validate()->getErrors()['sort']);
        $this->assertTrue($restfulApi->resourceSort()->validate()->hasInvalidInputTypes());
    }

    /**
     * @throws AlchemistRestfulApiException
     */
    public function test_ResourceSort_with_InvalidDirectionParamValue_must_FALSE_validation(): void
    {
        $restfulApi = new AlchemistRestfulApi([
            'direction' => 'not_asc_nor_desc',
        ]);

        $this->assertFalse($restfulApi->validate()->passes());
        $this->assertNotNull($restfulApi->validate()->getErrors()['sort']);
        $this->assertTrue($restfulApi->resourceSort()->validate()->isInvalidDirection());
    }

    /**
     * @throws AlchemistRestfulApiException
     */
    public function test_ResourceSort_with_UndefinedSortField_must_FALSE_validation(): void
    {
        $restfulApi = new AlchemistRestfulApi([
            'sort' => 'qwerty',
        ]);

        $this->assertFalse($restfulApi->validate()->passes());
        $this->assertNotNull($restfulApi->validate()->getErrors()['sort']);
        $this->assertTrue($restfulApi->resourceSort()->validate()->isInvalidSortField());
    }

    /**
     * @throws AlchemistRestfulApiException
     */
    public function test_ResourceSort_with_FieldThatIsNotInSortFields_must_FALSE_validation(): void
    {
        $restfulApi = new AlchemistRestfulApi([
            'sort' => 'not_in_sort_fields',
        ]);

        $restfulApi->resourceSort()->defineSortableFields([
            'id',
            'name',
        ]);

        $this->assertFalse($restfulApi->validate()->passes());
        $this->assertNotNull($restfulApi->validate()->getErrors()['sort']);
        $this->assertTrue($restfulApi->resourceSort()->validate()->isInvalidSortField());
    }

    /**
     * @throws AlchemistRestfulApiException
     */
    public function test_ResourceSort_with_DefaultSortField_must_pass(): void
    {
        $restfulApi = new AlchemistRestfulApi([]);

        $restfulApi->resourceSort()->defineDefaultSort('id');

        $this->assertSame('id', $restfulApi->resourceSort()->sort()->getSortField());
    }

    /**
     * @throws AlchemistRestfulApiException
     */
    public function test_ResourceSort_with_DefaultDirection_must_pass(): void
    {
        $restfulApi = new AlchemistRestfulApi([]);

        $restfulApi->resourceSort()->defineDefaultDirection('asc');

        $this->assertSame('asc', $restfulApi->resourceSort()->sort()->getDirection());
    }
}
