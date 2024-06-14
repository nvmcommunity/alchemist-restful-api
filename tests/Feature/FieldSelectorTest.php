<?php

namespace Feature;

use Nvmcommunity\Alchemist\RestfulApi\AlchemistRestfulApi;
use Nvmcommunity\Alchemist\RestfulApi\Common\Exceptions\AlchemistRestfulApiException;
use Nvmcommunity\Alchemist\RestfulApi\FieldSelector\Objects\FieldObject;
use Nvmcommunity\Alchemist\RestfulApi\FieldSelector\Objects\Structure\CollectionStructure;
use Nvmcommunity\Alchemist\RestfulApi\FieldSelector\Objects\Structure\ObjectStructure;
use PHPUnit\Framework\TestCase;

class FieldSelectorTest extends TestCase
{
    /**
     * @throws AlchemistRestfulApiException
     */
    public function test_FieldSelector_in_NormalCase_must_pass(): void
    {
        $restfulApi = new AlchemistRestfulApi([
            'fields' => 'id,order_date,product{product_id,product_name,attributes{attribute_id,attribute_name}}'
        ]);

        $restfulApi->fieldSelector()
            ->defineFieldStructure([
                'id',
                'order_date',
                new ObjectStructure('product', null, [
                    'product_id',
                    'product_name',
                    new CollectionStructure('attributes', null, [
                        'attribute_id',
                        'attribute_name',
                    ]),
                ]),
            ]);

        $this->assertTrue($restfulApi->validate()->passes());

        $this->assertIsArray($restfulApi->fieldSelector()->fields());

        $this->assertCount(3, $restfulApi->fieldSelector()->fields());

        $this->assertContainsOnlyInstancesOf(FieldObject::class, $restfulApi->fieldSelector()->fields());

        $this->assertSame(['id', 'order_date', 'product'], $restfulApi->fieldSelector()->flatFields());

        $this->assertSame(['product_id', 'product_name', 'attributes'], $restfulApi->fieldSelector()->flatFields('$.product'));

        $this->assertSame(['attribute_id', 'attribute_name'], $restfulApi->fieldSelector()->flatFields('$.product.attributes'));
    }

    /**
     * @throws AlchemistRestfulApiException
     */
    public function test_FieldSelector_in_NormalCase_WithNullParamValue_must_pass(): void
    {
        $restfulApi = new AlchemistRestfulApi([
            'fields' => null
        ]);

        $this->assertTrue($restfulApi->validate()->passes());

        $this->assertSame([], $restfulApi->fieldSelector()->fields());
    }

    /**
     * @throws AlchemistRestfulApiException
     */
    public function test_FieldSelector_when_FieldsParameterIsEmpty_then_DefaultFields_returned(): void
    {
        $restfulApi = new AlchemistRestfulApi([
            'fields' => ''
        ]);

        $restfulApi->fieldSelector()
            ->defineDefaultFields(['id'])
            ->defineFieldStructure([
                'id',
            ]);

        $this->assertTrue($restfulApi->validate()->passes());

        $this->assertContains('id', $restfulApi->fieldSelector()->flatFields());
    }

    /**
     * @throws AlchemistRestfulApiException
     */
    public function test_FieldSelector_in_NormalCase_with_ObjectFieldParameter_must_pass(): void
    {
        $restfulApi = new AlchemistRestfulApi([
            'fields' => 'product{id,name}'
        ]);

        $restfulApi->fieldSelector()
            ->defineDefaultFields(['id'])
            ->defineFieldStructure([
                'id',
                new ObjectStructure('product', null, [
                    'id',
                    'name',
                ]),
            ]);

        $this->assertTrue($restfulApi->validate()->passes());

        $this->assertSame(['product'], $restfulApi->fieldSelector()->flatFields());
        $this->assertSame(['id', 'name'], $restfulApi->fieldSelector()->flatFields('$.product'));
    }

    /**
     * @throws AlchemistRestfulApiException
     */
    public function test_FieldSelector_when_SelectFieldHasSpace_must_pass(): void
    {
        $restfulApi = new AlchemistRestfulApi([
            'fields' => 'id, order_date, product{id, name}'
        ]);

        $restfulApi->fieldSelector()
            ->defineFieldStructure([
                'id',
                'order_date',
                new ObjectStructure('product', null, [
                    'id',
                    'name',
                ]),
            ]);

        $this->assertTrue($restfulApi->validate()->passes());

        $this->assertSame(['id', 'order_date', 'product'], $restfulApi->fieldSelector()->flatFields());
        $this->assertSame(['id', 'name'], $restfulApi->fieldSelector()->flatFields('$.product'));
    }

    /**
     * @throws AlchemistRestfulApiException
     */
    public function test_FieldSelector_when_ObjectFieldWithEmptySubFields_then_DefaultFields_must_be_returned(): void
    {
        $restfulApi = new AlchemistRestfulApi([
            'fields' => 'product{}'
        ]);

        $restfulApi->fieldSelector()
            ->defineFieldStructure([
                'id',
                new ObjectStructure('product', null, [
                    'id',
                    'name',
                ], ['id']),
            ]);

        $this->assertTrue($restfulApi->validate()->passes());

        $this->assertContains('product', $restfulApi->fieldSelector()->flatFields());

        $this->assertEquals(['id'], $restfulApi->fieldSelector()->flatFields('$.product'));
    }

    /**
     * @throws AlchemistRestfulApiException
     */
    public function test_FieldSelector_when_NestedObjectFieldWithEmptySubFields_then_DefaultFields_must_be_returned(): void
    {
        $restfulApi = new AlchemistRestfulApi([
            'fields' => 'product{category}'
        ]);

        $restfulApi->fieldSelector()
            ->defineFieldStructure([
                'id',
                new ObjectStructure('product', null, [
                    'id',
                    'name',
                    new ObjectStructure('category', null, [
                        'id',
                        'name',
                    ], ['id']),
                ]),
            ]);

        $this->assertTrue($restfulApi->validate()->passes());

        $this->assertContains('product', $restfulApi->fieldSelector()->flatFields());
        $this->assertEquals(['id'], $restfulApi->fieldSelector()->flatFields('$.product.category'));
    }

    /**
     * @throws AlchemistRestfulApiException
     */
    public function test_FieldSelector_when_SelectNotDefinedField_must_False_validation(): void
    {
        $restfulApi = new AlchemistRestfulApi([
            'fields' => 'not_exist_field'
        ]);

        $restfulApi->fieldSelector()
            ->defineFieldStructure([
                'id',
                'order_date',
            ]);

        $this->assertFalse($restfulApi->validate()->passes());

        $this->assertContains('not_exist_field', $restfulApi->fieldSelector()->validate()->getUnselectableFields());

        $this->assertSame(['not_exist_field'], $restfulApi->fieldSelector()->flatFields());
    }

    /**
     * @throws AlchemistRestfulApiException
     */
    public function test_FieldSelector_when_SelectNestedFieldNotInStructure_must_False_validation(): void
    {
        $restfulApi = new AlchemistRestfulApi([
            'fields' => 'id,order_date,product{id,name,not_exist_field}'
        ]);

        $restfulApi->fieldSelector()
            ->defineFieldStructure([
                'id',
                'order_date',
                new ObjectStructure('product', null, [
                    'id',
                    'name',
                ]),
            ]);

        $this->assertFalse($restfulApi->validate()->passes());

        $this->assertSame(['id', 'order_date', 'product'], $restfulApi->fieldSelector()->flatFields());
        $this->assertSame(['id', 'name', 'not_exist_field'], $restfulApi->fieldSelector()->flatFields('$.product'));
    }

    /**
     * @throws AlchemistRestfulApiException
     */
    public function test_FieldSelector_WithObjectParamValue_must_False_validation(): void
    {
        $restfulApi = new AlchemistRestfulApi([
            'fields' => new \stdClass()
        ]);

        $this->assertFalse($restfulApi->validate()->passes());
        $this->assertNotNull($restfulApi->validate()->getErrors()['fields']);
        $this->assertTrue($restfulApi->fieldSelector()->validate()->isInvalidInputType());

        $this->assertSame([], $restfulApi->fieldSelector()->fields());
    }

    /**
     * @throws AlchemistRestfulApiException
     */
    public function test_FieldSelector_WithArrayParamValue_must_False_validation(): void
    {
        $restfulApi = new AlchemistRestfulApi([
            'fields' => []
        ]);

        $this->assertFalse($restfulApi->validate()->passes());
        $this->assertNotNull($restfulApi->validate()->getErrors()['fields']);
        $this->assertTrue($restfulApi->fieldSelector()->validate()->isInvalidInputType());

        $this->assertSame([], $restfulApi->fieldSelector()->fields());
    }

    /**
     * @throws AlchemistRestfulApiException
     */
    public function test_FieldSelector_WithIntegerParamValue_must_False_validation(): void
    {
        $restfulApi = new AlchemistRestfulApi([
            'fields' => 123
        ]);

        $this->assertFalse($restfulApi->validate()->passes());
        $this->assertNotNull($restfulApi->validate()->getErrors()['fields']);
        $this->assertTrue($restfulApi->fieldSelector()->validate()->isInvalidInputType());

        $this->assertSame([], $restfulApi->fieldSelector()->fields());
    }

    /**
     * @throws AlchemistRestfulApiException
     */
    public function test_FieldSelector_WithNumericParamValue_must_False_validation(): void
    {
        $restfulApi = new AlchemistRestfulApi([
            'fields' => 123.1
        ]);

        $this->assertFalse($restfulApi->validate()->passes());
        $this->assertNotNull($restfulApi->validate()->getErrors()['fields']);
        $this->assertTrue($restfulApi->fieldSelector()->validate()->isInvalidInputType());

        $this->assertSame([], $restfulApi->fieldSelector()->fields());
    }

    /**
     * @throws AlchemistRestfulApiException
     */
    public function test_FieldSelector_WithTrueBooleanParamValue_must_False_validation(): void
    {
        $restfulApi = new AlchemistRestfulApi([
            'fields' => true
        ]);

        $this->assertFalse($restfulApi->validate()->passes());
        $this->assertNotNull($restfulApi->validate()->getErrors()['fields']);
        $this->assertTrue($restfulApi->fieldSelector()->validate()->isInvalidInputType());

        $this->assertSame([], $restfulApi->fieldSelector()->fields());
    }

    /**
     * @throws AlchemistRestfulApiException
     */
    public function test_FieldSelector_WithFalseBooleanParamValue_must_False_validation(): void
    {
        $restfulApi = new AlchemistRestfulApi([
            'fields' => false
        ]);

        $this->assertFalse($restfulApi->validate()->passes());
        $this->assertNotNull($restfulApi->validate()->getErrors()['fields']);
        $this->assertTrue($restfulApi->fieldSelector()->validate()->isInvalidInputType());

        $this->assertSame([], $restfulApi->fieldSelector()->fields());
    }

    /**
     * @throws AlchemistRestfulApiException
     */
    public function test_FieldSelector_when_SelectSubFieldsOnAtomicFields_must_False_validation(): void
    {
        $restfulApi = new AlchemistRestfulApi([
            'fields' => 'id{category}'
        ]);

        $restfulApi->fieldSelector()
            ->defineFieldStructure([
                'id',
            ]);

        $this->assertFalse($restfulApi->validate()->passes());
        $this->assertNotNull($restfulApi->validate()->getErrors()['fields']);
        $this->assertTrue($restfulApi->fieldSelector()->validate()->hasUnselectableFields());

        $this->assertSame(['id'], $restfulApi->fieldSelector()->flatFields());

        //$this->expectException(AlchemistRestfulApiException::class);
        //$restfulApi->fieldSelector()->flatFields('$.id');
    }
}
