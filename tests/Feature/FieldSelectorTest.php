<?php

namespace Nvmcommunity\Alchemist\RestfulApi\FieldSelector\Handlers;

use Nvmcommunity\Alchemist\RestfulApi\AlchemistRestfulApi;
use Nvmcommunity\Alchemist\RestfulApi\Common\Exceptions\AlchemistRestfulApiException;
use Nvmcommunity\Alchemist\RestfulApi\FieldSelector\Objects\Structure\ObjectStructure;
use PHPUnit\Framework\TestCase;

class FieldSelectorTest extends TestCase
{
    /**
     * @throws AlchemistRestfulApiException
     */
    public function test_select_field_not_in_structure_must_not_pass()
    {
        $restfulApi = new AlchemistRestfulApi([
            'fields' => 'not_exist_field'
        ]);

        $restfulApi->fieldSelector()
            ->defineFieldStructure([
                'id',
                'order_date',
            ]);

        $this->assertFalse($restfulApi->validate($errorBag)->passes());
    }

    /**
     * @throws AlchemistRestfulApiException
     */
    public function test_when_fields_parameter_is_empty_then_default_fields_must_be_returned()
    {
        $restfulApi = new AlchemistRestfulApi([
            'fields' => ''
        ]);

        $restfulApi->fieldSelector()
            ->defineDefaultFields(['id'])
            ->defineFieldStructure([
                'id',
            ]);

        $this->assertTrue($restfulApi->validate($errorBag)->passes());
        $this->assertContains('id', $restfulApi->fieldSelector()->flatFields());
    }

    /**
     * @throws AlchemistRestfulApiException
     */
    public function test_when_fields_parameter_with_object_field_must_pass()
    {
        $restfulApi = new AlchemistRestfulApi([
            'fields' => 'id,product{id,name}'
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

        $this->assertTrue($restfulApi->validate($errorBag)->passes());
    }

    /**
     * @throws AlchemistRestfulApiException
     */
    public function test_when_fields_parameter_with_object_field_and_their_sub_fields_then_corresponding_field_must_present()
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

        $this->assertTrue($restfulApi->validate($errorBag)->passes());
        $this->assertContains('product', $restfulApi->fieldSelector()->flatFields());

        $productFields = $restfulApi->fieldSelector()->flatFields('$.product');

        $this->assertContains('id', $productFields);
        $this->assertContains('name', $productFields);
    }
}
