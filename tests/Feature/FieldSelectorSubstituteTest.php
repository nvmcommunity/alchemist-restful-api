<?php

namespace Feature;

use Nvmcommunity\Alchemist\RestfulApi\AlchemistRestfulApi;
use Nvmcommunity\Alchemist\RestfulApi\Common\Exceptions\AlchemistRestfulApiException;
use Nvmcommunity\Alchemist\RestfulApi\FieldSelector\Objects\Structure\CollectionStructure;
use Nvmcommunity\Alchemist\RestfulApi\FieldSelector\Objects\Structure\ObjectStructure;
use PHPUnit\Framework\TestCase;

class FieldSelectorSubstituteTest extends TestCase
{
    /**
     * @throws AlchemistRestfulApiException
     */
    public function test_GivenObjectStructure_WhenSubstituteIsNull_ThenFieldNameUnchanged(): void
    {
        $restfulApi = new AlchemistRestfulApi([
            'fields' => 'id,product{product_id,product_name}'
        ]);

        $restfulApi->fieldSelector()->defineFieldStructure([
            'id',
            new ObjectStructure('product', null, [
                'product_id',
                'product_name'
            ]),
        ]);

        $this->assertTrue($restfulApi->validate()->passes());

        $flatFields = $restfulApi->fieldSelector()->flatFields();

        $this->assertIsArray($flatFields);

        $this->assertCount(2, $flatFields);

        $this->assertContains('id', $flatFields);
        $this->assertContains('product', $flatFields);

        $this->assertNotContains(null, $flatFields);

    }

    /**
     * @throws AlchemistRestfulApiException
     */
    public function test_GivenNestedObjectStructure_WhenSubstituteIsNull_ThenFieldNameUnchanged(): void
    {
        $restfulApi = new AlchemistRestfulApi([
            'fields' => 'id,product{product_id,product_name,category{category_id,category_name}}'
        ]);

        $restfulApi->fieldSelector()->defineFieldStructure([
            'id',
            new ObjectStructure('product', null, [
                'product_id',
                'product_name',
                new ObjectStructure('category', null, [
                    'category_id',
                    'category_name',
                ]),
            ]),
        ]);

        $this->assertTrue($restfulApi->validate()->passes());

        $flatFields = $restfulApi->fieldSelector()->flatFields('$.product');

        $this->assertIsArray($flatFields);

        $this->assertCount(3, $flatFields);

        $this->assertContains('product_id', $flatFields);
        $this->assertContains('product_name', $flatFields);
        $this->assertContains('category', $flatFields);

        $this->assertNotContains(null, $flatFields);
    }

    /**
     * @throws AlchemistRestfulApiException
     */
    public function test_GivenThreeNestedObjectStructure_WhenSubstituteIsNull_ThenFieldNameUnchanged(): void
    {
        $restfulApi = new AlchemistRestfulApi([
            'fields' => 'id,product{product_id,product_name,category{category_id,category_name,creator{creator_id,creator_name}}}}'
        ]);

        $restfulApi->fieldSelector()->defineFieldStructure([
            'id',
            new ObjectStructure('product', null, [
                'product_id',
                'product_name',
                new ObjectStructure('category', null, [
                    'category_id',
                    'category_name',
                    new ObjectStructure('creator', null, [
                        'creator_id',
                        'creator_name',
                    ]),
                ]),
            ]),
        ]);

        $this->assertTrue($restfulApi->validate()->passes());

        $flatFields = $restfulApi->fieldSelector()->flatFields('$.product.category');

        $this->assertIsArray($flatFields);

        $this->assertCount(3, $flatFields);

        $this->assertContains('category_id', $flatFields);
        $this->assertContains('category_name', $flatFields);
        $this->assertContains('creator', $flatFields);

        $this->assertNotContains(null, $flatFields);
    }

    /**
     * @throws AlchemistRestfulApiException
     */
    public function test_GivenCollectionStructure_WhenSubstituteIsNull_ThenFieldNameUnchanged(): void
    {
        $restfulApi = new AlchemistRestfulApi([
            'fields' => 'id,product{product_id,product_name}'
        ]);

        $restfulApi->fieldSelector()->defineFieldStructure([
            'id',
            new CollectionStructure('product', null, [
                'product_id',
                'product_name'
            ]),
        ]);

        $this->assertTrue($restfulApi->validate()->passes());

        $flatFields = $restfulApi->fieldSelector()->flatFields();

        $this->assertIsArray($flatFields);

        $this->assertCount(2, $flatFields);

        $this->assertContains('product', $flatFields);
    }

    /**
     * @throws AlchemistRestfulApiException
     */
    public function test_GivenNestedCollectionStructure_WhenSubstituteIsNull_ThenFieldNameUnchanged(): void
    {
        $restfulApi = new AlchemistRestfulApi([
            'fields' => 'id,product{product_id,product_name,category{category_id,category_name}}'
        ]);

        $restfulApi->fieldSelector()->defineFieldStructure([
            'id',
            new CollectionStructure('product', null, [
                'product_id',
                'product_name',
                new CollectionStructure('category', null, [
                    'category_id',
                    'category_name',
                ]),
            ]),
        ]);

        $this->assertTrue($restfulApi->validate()->passes());

        $flatFields = $restfulApi->fieldSelector()->flatFields('$.product');

        $this->assertIsArray($flatFields);

        $this->assertCount(3, $flatFields);

        $this->assertContains('product_id', $flatFields);
        $this->assertContains('product_name', $flatFields);
        $this->assertContains('category', $flatFields);

        $this->assertNotContains(null, $flatFields);
    }

    /**
     * @throws AlchemistRestfulApiException
     */
    public function test_GivenThreeNestedCollectionStructure_WhenSubstituteIsNull_ThenFieldNameUnchanged(): void
    {
        $restfulApi = new AlchemistRestfulApi([
            'fields' => 'id,product{product_id,product_name,category{category_id,category_name,creator{creator_id,creator_name}}}'
        ]);

        $restfulApi->fieldSelector()->defineFieldStructure([
            'id',
            new CollectionStructure('product', null, [
                'product_id',
                'product_name',
                new CollectionStructure('category', null, [
                    'category_id',
                    'category_name',
                    new CollectionStructure('creator', null, [
                        'creator_id',
                        'creator_name',
                    ]),
                ]),
            ]),
        ]);

        $this->assertTrue($restfulApi->validate()->passes());

        $flatFields = $restfulApi->fieldSelector()->flatFields('$.product.category');

        $this->assertIsArray($flatFields);

        $this->assertCount(3, $flatFields);

        $this->assertContains('category_id', $flatFields);
        $this->assertContains('category_name', $flatFields);
        $this->assertContains('creator', $flatFields);

        $this->assertNotContains(null, $flatFields);
    }

    /**
     * @throws AlchemistRestfulApiException
     */
    public function test_GivenObjectStructure_WhenSubstituteIsAnyName_ThenFieldNameChangeToSubstitute(): void
    {
        $restfulApi = new AlchemistRestfulApi([
            'fields' => 'id,product{product_id,product_name}'
        ]);

        $restfulApi->fieldSelector()->defineFieldStructure([
            'id',
            new ObjectStructure('product', 'product_substitute', [
                'product_id',
                'product_name'
            ]),
        ]);

        $this->assertTrue($restfulApi->validate()->passes());

        $flatFields = $restfulApi->fieldSelector()->flatFields();

        $this->assertIsArray($flatFields);

        $this->assertCount(2, $flatFields);

        $this->assertContains('product_substitute', $flatFields);

        $this->assertNotContains('product', $flatFields);
    }

    /**
     * @throws AlchemistRestfulApiException
     */
    public function test_GivenNestedObjectStructure_WhenSubstituteIsAnyName_ThenFieldNameChangeToSubstitute(): void
    {
        $restfulApi = new AlchemistRestfulApi([
            'fields' => 'id,product{product_id,product_name,category{category_id,category_name}}'
        ]);

        $restfulApi->fieldSelector()->defineFieldStructure([
            'id',
            new ObjectStructure('product', 'product_substitute', [
                'product_id',
                'product_name',
                new ObjectStructure('category', 'category_substitute', [
                    'category_id',
                    'category_name',
                ]),
            ]),
        ]);

        $this->assertTrue($restfulApi->validate()->passes());

        $nestedFlatFields = $restfulApi->fieldSelector()->flatFields('$.product');

        $this->assertIsArray($nestedFlatFields);

        $this->assertCount(3, $nestedFlatFields);

        $this->assertContains('category_substitute', $nestedFlatFields);

        $this->assertNotContains('category', $nestedFlatFields);
    }

    /**
     * @throws AlchemistRestfulApiException
     */
    public function test_GivenThreeTimesNestedObjectStructure_WhenSubstituteIsAnyName_ThenFieldNameChangeToSubstitute(): void
    {
        $restfulApi = new AlchemistRestfulApi([
            'fields' => 'id,product{product_id,product_name,category{category_id,category_name,creator{creator_id,creator_name}}}'
        ]);

        $restfulApi->fieldSelector()->defineFieldStructure([
            'id',
            new ObjectStructure('product', 'product_substitute', [
                'product_id',
                'product_name',
                new ObjectStructure('category', 'category_substitute', [
                    'category_id',
                    'category_name',
                    new ObjectStructure('creator', 'creator_substitute', [
                        'creator_id',
                        'creator_name',
                    ]),
                ]),
            ]),
        ]);

        $this->assertTrue($restfulApi->validate()->passes());

        $nestedFlatFields = $restfulApi->fieldSelector()->flatFields('$.product.category');

        $this->assertIsArray($nestedFlatFields);

        $this->assertCount(3, $nestedFlatFields);

        $this->assertContains('creator_substitute', $nestedFlatFields);

        $this->assertNotContains('creator', $nestedFlatFields);
    }

    /**
     * @throws AlchemistRestfulApiException
     */
    public function test_GivenCollectionStructure_WhenSubstituteIsAnyName_ThenFieldNameChangeToSubstitute(): void
    {
        $restfulApi = new AlchemistRestfulApi([
            'fields' => 'id,product{product_id,product_name}'
        ]);

        $restfulApi->fieldSelector()->defineFieldStructure([
            'id',
            new CollectionStructure('product', 'product_substitute', [
                'product_id',
                'product_name'
            ]),
        ]);

        $this->assertTrue($restfulApi->validate()->passes());

        $flatFields = $restfulApi->fieldSelector()->flatFields();

        $this->assertIsArray($flatFields);

        $this->assertCount(2, $flatFields);

        $this->assertContains('product_substitute', $flatFields);
    }

    /**
     * @throws AlchemistRestfulApiException
     */
    public function test_GivenNestedCollectionStructure_WhenSubstituteIsAnyName_ThenFieldNameChangeToSubstitute(): void
    {
        $restfulApi = new AlchemistRestfulApi([
            'fields' => 'id,product{product_id,product_name,category{category_id,category_name}}'
        ]);

        $restfulApi->fieldSelector()->defineFieldStructure([
            'id',
            new CollectionStructure('product', 'product_substitute', [
                'product_id',
                'product_name',
                new CollectionStructure('category', 'category_substitute', [
                    'category_id',
                    'category_name',
                ]),
            ]),
        ]);

        $this->assertTrue($restfulApi->validate()->passes());

        $nestedFlatFields = $restfulApi->fieldSelector()->flatFields('$.product');

        $this->assertIsArray($nestedFlatFields);

        $this->assertCount(3, $nestedFlatFields);

        $this->assertContains('category_substitute', $nestedFlatFields);

        $this->assertNotContains('category', $nestedFlatFields);
    }

    /**
     * @throws AlchemistRestfulApiException
     */
    public function test_GivenThreeTimesNestedCollectionStructure_WhenSubstituteIsAnyName_ThenFieldNameChangeToSubstitute(): void
    {
        $restfulApi = new AlchemistRestfulApi([
            'fields' => 'id,product{product_id,product_name,category{category_id,category_name,creator{creator_id,creator_name}}}'
        ]);

        $restfulApi->fieldSelector()->defineFieldStructure([
            'id',
            new CollectionStructure('product', 'product_substitute', [
                'product_id',
                'product_name',
                new CollectionStructure('category', 'category_substitute', [
                    'category_id',
                    'category_name',
                    new CollectionStructure('creator', 'creator_substitute', [
                        'creator_id',
                        'creator_name',
                    ]),
                ]),
            ]),
        ]);

        $this->assertTrue($restfulApi->validate()->passes());

        $nestedFlatFields = $restfulApi->fieldSelector()->flatFields('$.product.category');

        $this->assertIsArray($nestedFlatFields);

        $this->assertCount(3, $nestedFlatFields);

        $this->assertContains('creator_substitute', $nestedFlatFields);

        $this->assertNotContains('creator', $nestedFlatFields);
    }

    /**
     * @throws AlchemistRestfulApiException
     */
    public function test_GivenObjectStructure_WhenSubstituteIsAmpersandSymbol_ThenGivenFieldMustBeRemoved(): void
    {
        $restfulApi = new AlchemistRestfulApi([
            'fields' => 'id,product{product_id,product_name}'
        ]);

        $restfulApi->fieldSelector()->defineFieldStructure([
            'id',
            new ObjectStructure('product', '&', [
                'product_id',
                'product_name'
            ]),
        ]);

        $this->assertTrue($restfulApi->validate()->passes());

        $flatFields = $restfulApi->fieldSelector()->flatFields();

        $this->assertIsArray($flatFields);

        $this->assertCount(1, $flatFields);

        $this->assertContains('id', $flatFields);

        $this->assertNotContains('product', $flatFields);

        $this->assertNotContains('&', $flatFields);
    }

    /**
     * @throws AlchemistRestfulApiException
     */
    public function test_GivenNestedObjectStructure_WhenSubstituteIsAmpersandSymbol_ThenGivenFieldMustBeRemoved(): void
    {
        $restfulApi = new AlchemistRestfulApi([
            'fields' => 'id,product{product_id,product_name,category{category_id,category_name}}'
        ]);

        $restfulApi->fieldSelector()->defineFieldStructure([
            'id',
            new ObjectStructure('product', '&', [
                'product_id',
                'product_name',
                new ObjectStructure('category', '&', [
                    'category_id',
                    'category_name',
                ]),
            ]),
        ]);

        $this->assertTrue($restfulApi->validate()->passes());

        $nestedFlatFields = $restfulApi->fieldSelector()->flatFields('$.product');

        $this->assertIsArray($nestedFlatFields);

        $this->assertCount(2, $nestedFlatFields);

        $this->assertNotContains('category', $nestedFlatFields);

        $this->assertNotContains('&', $nestedFlatFields);
    }

    /**
     * @throws AlchemistRestfulApiException
     */
    public function test_GivenThreeTimesNestedObjectStructure_WhenSubstituteIsAmpersandSymbol_ThenGivenFieldMustBeRemoved(): void
    {
        $restfulApi = new AlchemistRestfulApi([
            'fields' => 'id,product{product_id,product_name,category{category_id,category_name,creator{creator_id,creator_name}}}'
        ]);

        $restfulApi->fieldSelector()->defineFieldStructure([
            'id',
            new ObjectStructure('product', '&', [
                'product_id',
                'product_name',
                new ObjectStructure('category', '&', [
                    'category_id',
                    'category_name',
                    new ObjectStructure('creator', '&', [
                        'creator_id',
                        'creator_name',
                    ]),
                ]),
            ]),
        ]);

        $this->assertTrue($restfulApi->validate()->passes());

        $nestedFlatFields = $restfulApi->fieldSelector()->flatFields('$.product.category');

        $this->assertIsArray($nestedFlatFields);

        $this->assertCount(2, $nestedFlatFields);

        $this->assertNotContains('creator', $nestedFlatFields);

        $this->assertNotContains('&', $nestedFlatFields);
    }

    /**
     * @throws AlchemistRestfulApiException
     */
    public function test_GivenCollectionStructure_WhenSubstituteIsAmpersandSymbol_ThenGivenFieldMustBeRemoved(): void
    {
        $restfulApi = new AlchemistRestfulApi([
            'fields' => 'id,product{product_id,product_name}'
        ]);

        $restfulApi->fieldSelector()->defineFieldStructure([
            'id',
            new CollectionStructure('product', '&', [
                'product_id',
                'product_name'
            ]),
        ]);

        $this->assertTrue($restfulApi->validate()->passes());

        $flatFields = $restfulApi->fieldSelector()->flatFields();

        $this->assertIsArray($flatFields);

        $this->assertCount(1, $flatFields);

        $this->assertContains('id', $flatFields);

        $this->assertNotContains('product', $flatFields);

        $this->assertNotContains('&', $flatFields);
    }

    /**
     * @throws AlchemistRestfulApiException
     */
    public function test_GivenNestedCollectionStructure_WhenSubstituteIsAmpersandSymbol_ThenGivenFieldMustBeRemoved(): void
    {
        $restfulApi = new AlchemistRestfulApi([
            'fields' => 'id,product{product_id,product_name}'
        ]);

        $restfulApi->fieldSelector()->defineFieldStructure([
            'id',
            new CollectionStructure('product', '&', [
                'product_id',
                'product_name',
                new CollectionStructure('category', '&', [
                    'category_id',
                    'category_name',
                ]),
            ]),
        ]);

        $this->assertTrue($restfulApi->validate()->passes());

        $nestedFlatFields = $restfulApi->fieldSelector()->flatFields('$.product');

        $this->assertIsArray($nestedFlatFields);

        $this->assertCount(2, $nestedFlatFields);

        $this->assertNotContains('category', $nestedFlatFields);

        $this->assertNotContains('&', $nestedFlatFields);
    }

    /**
     * @throws AlchemistRestfulApiException
     */
    public function test_GivenThreeTimesNestedCollectionStructure_WhenSubstituteIsAmpersandSymbol_ThenGivenFieldMustBeRemoved(): void
    {
        $restfulApi = new AlchemistRestfulApi([
            'fields' => 'id,product{product_id,product_name,category{category_id,category_name,creator{creator_id,creator_name}}}'
        ]);

        $restfulApi->fieldSelector()->defineFieldStructure([
            'id',
            new CollectionStructure('product', '&', [
                'product_id',
                'product_name',
                new CollectionStructure('category', '&', [
                    'category_id',
                    'category_name',
                    new CollectionStructure('creator', '&', [
                        'creator_id',
                        'creator_name',
                    ]),
                ]),
            ]),
        ]);

        $this->assertTrue($restfulApi->validate()->passes());

        $nestedFlatFields = $restfulApi->fieldSelector()->flatFields('$.product.category');

        $this->assertIsArray($nestedFlatFields);

        $this->assertCount(2, $nestedFlatFields);

        $this->assertNotContains('creator', $nestedFlatFields);

        $this->assertNotContains('&', $nestedFlatFields);
    }
}
