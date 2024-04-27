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

        $restfulApi->resourceSort()->defineSortableFields(['id']);

        $this->assertTrue($restfulApi->validate()->passes());

        $this->assertSame('id', $restfulApi->resourceSort()->sort()->getSortField());
        $this->assertSame('asc', $restfulApi->resourceSort()->sort()->getDirection());
    }

    /**
     * @throws AlchemistRestfulApiException
     */
    public function test_ResourceSort_in_NormalCase_without_DirectionValueParam_then_DefaultDirection_used(): void
    {
        $restfulApi = new AlchemistRestfulApi([
            'sort' => 'id',
        ]);

        $restfulApi->resourceSort()->defineSortableFields(['id']);

        $this->assertTrue($restfulApi->validate()->passes());

        $this->assertSame('id', $restfulApi->resourceSort()->sort()->getSortField());
        $this->assertSame('asc', $restfulApi->resourceSort()->sort()->getDirection());
    }

    /**
     * @throws AlchemistRestfulApiException
     */
    public function test_ResourceSort_with_DefaultSortField_then_DefaultSort_used(): void
    {
        $restfulApi = new AlchemistRestfulApi([]);

        $restfulApi->resourceSort()->defineDefaultSort('id');

        $this->assertTrue($restfulApi->validate()->passes());

        $this->assertSame('id', $restfulApi->resourceSort()->sort()->getSortField());
    }

    /**
     * @throws AlchemistRestfulApiException
     */
    public function test_ResourceSort_with_DefaultDirection_must_pass(): void
    {
        $restfulApi = new AlchemistRestfulApi([]);

        $restfulApi->resourceSort()->defineDefaultDirection('asc');

        $this->assertTrue($restfulApi->validate()->passes());

        $this->assertSame('', $restfulApi->resourceSort()->sort()->getSortField());
        $this->assertSame('asc', $restfulApi->resourceSort()->sort()->getDirection());
    }

    /**
     * @throws AlchemistRestfulApiException
     */
    public function test_ResourceSort_with_NullSortParamValue_must_pass(): void
    {
        $restfulApi = new AlchemistRestfulApi([
            'sort' => null,
            'direction' => null,
        ]);

        $restfulApi->resourceSort()->defineSortableFields(['id']);

        $this->assertTrue($restfulApi->validate()->passes());

        $this->assertSame('', $restfulApi->resourceSort()->sort()->getSortField());
        $this->assertSame('asc', $restfulApi->resourceSort()->sort()->getDirection());
    }

    /**
     * @throws AlchemistRestfulApiException
     */
    public function test_ResourceSort_with_ArraySortParamValue_must_FALSE_validation(): void
    {
        $restfulApi = new AlchemistRestfulApi([
            'sort' => ['id'],
        ]);

        $restfulApi->resourceSort()->defineSortableFields(['id']);

        $this->assertFalse($restfulApi->validate()->passes());

        $this->assertNotNull($restfulApi->validate()->getErrors()['sort']);
        $this->assertTrue($restfulApi->resourceSort()->validate()->hasInvalidInputTypes());

        $this->assertSame('', $restfulApi->resourceSort()->sort()->getSortField());
        $this->assertSame('asc', $restfulApi->resourceSort()->sort()->getDirection());
    }

    /**
     * @throws AlchemistRestfulApiException
     */
    public function test_ResourceSort_with_ObjectSortParamValue_must_FALSE_validation(): void
    {
        $restfulApi = new AlchemistRestfulApi([
            'sort' => new \stdClass()
        ]);

        $this->assertFalse($restfulApi->validate()->passes());

        $this->assertNotNull($restfulApi->validate()->getErrors()['sort']);
        $this->assertTrue($restfulApi->resourceSort()->validate()->hasInvalidInputTypes());

        $this->assertSame('', $restfulApi->resourceSort()->sort()->getSortField());
        $this->assertSame('asc', $restfulApi->resourceSort()->sort()->getDirection());
    }

    /**
     * @throws AlchemistRestfulApiException
     */
    public function test_ResourceSort_with_TrueBooleanSortParamValue_must_FALSE_validation(): void
    {
        $restfulApi = new AlchemistRestfulApi([
            'sort' => true
        ]);

        $restfulApi->resourceSort()->defineSortableFields(['id']);

        $this->assertFalse($restfulApi->validate()->passes());

        $this->assertNotNull($restfulApi->validate()->getErrors()['sort']);
        $this->assertTrue($restfulApi->resourceSort()->validate()->hasInvalidInputTypes());

        $this->assertSame('', $restfulApi->resourceSort()->sort()->getSortField());
        $this->assertSame('asc', $restfulApi->resourceSort()->sort()->getDirection());
    }

    /**
     * @throws AlchemistRestfulApiException
     */
    public function test_ResourceSort_with_FalseBooleanSortParamValue_must_FALSE_validation(): void
    {
        $restfulApi = new AlchemistRestfulApi([
            'sort' => false
        ]);

        $restfulApi->resourceSort()->defineSortableFields(['id']);

        $this->assertFalse($restfulApi->validate()->passes());

        $this->assertNotNull($restfulApi->validate()->getErrors()['sort']);
        $this->assertTrue($restfulApi->resourceSort()->validate()->hasInvalidInputTypes());

        $this->assertSame('', $restfulApi->resourceSort()->sort()->getSortField());
        $this->assertSame('asc', $restfulApi->resourceSort()->sort()->getDirection());
    }

    /**
     * @throws AlchemistRestfulApiException
     */
    public function test_ResourceSort_with_IntegerSortParamValue_must_FALSE_validation(): void
    {
        $restfulApi = new AlchemistRestfulApi([
            'sort' => 123
        ]);

        $this->assertFalse($restfulApi->validate()->passes());

        $this->assertNotNull($restfulApi->validate()->getErrors()['sort']);
        $this->assertTrue($restfulApi->resourceSort()->validate()->hasInvalidInputTypes());

        $this->assertSame('', $restfulApi->resourceSort()->sort()->getSortField());
        $this->assertSame('asc', $restfulApi->resourceSort()->sort()->getDirection());
    }

    /**
     * @throws AlchemistRestfulApiException
     */
    public function test_ResourceSort_with_NumberSortParamValue_must_FALSE_validation(): void
    {
        $restfulApi = new AlchemistRestfulApi([
            'sort' => 123.1
        ]);

        $this->assertFalse($restfulApi->validate()->passes());

        $this->assertNotNull($restfulApi->validate()->getErrors()['sort']);
        $this->assertTrue($restfulApi->resourceSort()->validate()->hasInvalidInputTypes());

        $this->assertSame('', $restfulApi->resourceSort()->sort()->getSortField());
        $this->assertSame('asc', $restfulApi->resourceSort()->sort()->getDirection());
    }

    /**
     * @throws AlchemistRestfulApiException
     */
    public function test_ResourceSort_with_ArrayDirectionParamValue_must_FALSE_validation(): void
    {
        $restfulApi = new AlchemistRestfulApi([
            'direction' => ['id'],
        ]);

        $restfulApi->resourceSort()->defineSortableFields(['id']);

        $this->assertFalse($restfulApi->validate()->passes());
        $this->assertNotNull($restfulApi->validate()->getErrors()['sort']);
        $this->assertTrue($restfulApi->resourceSort()->validate()->hasInvalidInputTypes());

        $this->assertSame('', $restfulApi->resourceSort()->sort()->getSortField());
        $this->assertSame('asc', $restfulApi->resourceSort()->sort()->getDirection());
    }

    /**
     * @throws AlchemistRestfulApiException
     */
    public function test_ResourceSort_with_ObjectDirectionParamValue_must_FALSE_validation(): void
    {
        $restfulApi = new AlchemistRestfulApi([
            'direction' => new \stdClass()
        ]);

        $this->assertFalse($restfulApi->validate()->passes());

        $this->assertNotNull($restfulApi->validate()->getErrors()['sort']);
        $this->assertTrue($restfulApi->resourceSort()->validate()->hasInvalidInputTypes());

        $this->assertSame('', $restfulApi->resourceSort()->sort()->getSortField());
        $this->assertSame('asc', $restfulApi->resourceSort()->sort()->getDirection());
    }

    /**
     * @throws AlchemistRestfulApiException
     */
    public function test_ResourceSort_with_TrueBooleanDirectionParamValue_must_FALSE_validation(): void
    {
        $restfulApi = new AlchemistRestfulApi([
            'direction' => true
        ]);

        $restfulApi->resourceSort()->defineSortableFields(['id']);

        $this->assertFalse($restfulApi->validate()->passes());

        $this->assertNotNull($restfulApi->validate()->getErrors()['sort']);
        $this->assertTrue($restfulApi->resourceSort()->validate()->hasInvalidInputTypes());

        $this->assertSame('', $restfulApi->resourceSort()->sort()->getSortField());
        $this->assertSame('asc', $restfulApi->resourceSort()->sort()->getDirection());
    }

    /**
     * @throws AlchemistRestfulApiException
     */
    public function test_ResourceSort_with_FalseBooleanDirectionParamValue_must_FALSE_validation(): void
    {
        $restfulApi = new AlchemistRestfulApi([
            'direction' => false
        ]);

        $restfulApi->resourceSort()->defineSortableFields(['id']);

        $this->assertFalse($restfulApi->validate()->passes());

        $this->assertNotNull($restfulApi->validate()->getErrors()['sort']);
        $this->assertTrue($restfulApi->resourceSort()->validate()->hasInvalidInputTypes());

        $this->assertSame('', $restfulApi->resourceSort()->sort()->getSortField());
        $this->assertSame('asc', $restfulApi->resourceSort()->sort()->getDirection());
    }

    /**
     * @throws AlchemistRestfulApiException
     */
    public function test_ResourceSort_with_IntegerDirectionParamValue_must_FALSE_validation(): void
    {
        $restfulApi = new AlchemistRestfulApi([
            'direction' => 123
        ]);

        $this->assertFalse($restfulApi->validate()->passes());

        $this->assertNotNull($restfulApi->validate()->getErrors()['sort']);
        $this->assertTrue($restfulApi->resourceSort()->validate()->hasInvalidInputTypes());

        $this->assertSame('', $restfulApi->resourceSort()->sort()->getSortField());
        $this->assertSame('asc', $restfulApi->resourceSort()->sort()->getDirection());
    }

    /**
     * @throws AlchemistRestfulApiException
     */
    public function test_ResourceSort_with_NumericDirectionParamValue_must_FALSE_validation(): void
    {
        $restfulApi = new AlchemistRestfulApi([
            'direction' => 123.1
        ]);

        $this->assertFalse($restfulApi->validate()->passes());

        $this->assertNotNull($restfulApi->validate()->getErrors()['sort']);
        $this->assertTrue($restfulApi->resourceSort()->validate()->hasInvalidInputTypes());

        $this->assertSame('', $restfulApi->resourceSort()->sort()->getSortField());
        $this->assertSame('asc', $restfulApi->resourceSort()->sort()->getDirection());
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

        $this->assertSame('', $restfulApi->resourceSort()->sort()->getSortField());
        $this->assertSame('not_asc_nor_desc', $restfulApi->resourceSort()->sort()->getDirection());
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

        $this->assertSame('qwerty', $restfulApi->resourceSort()->sort()->getSortField());
        $this->assertSame('asc', $restfulApi->resourceSort()->sort()->getDirection());
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

        $this->assertSame('not_in_sort_fields', $restfulApi->resourceSort()->sort()->getSortField());
        $this->assertSame('asc', $restfulApi->resourceSort()->sort()->getDirection());
    }
}
