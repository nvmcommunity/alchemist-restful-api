# Alchemist Restful API

<!-- Project badges -->
![Project Contributors](https://img.shields.io/github/contributors/nvmcommunity/alchemist-restful-api)
![Project License](https://img.shields.io/github/license/nvmcommunity/alchemist-restful-api)


![Project Stars](https://img.shields.io/github/stars/nvmcommunity/alchemist-restful-api)
![Project Forks](https://img.shields.io/github/forks/nvmcommunity/alchemist-restful-api)
![Project Watchers](https://img.shields.io/github/watchers/nvmcommunity/alchemist-restful-api)
![Project Issues](https://img.shields.io/github/issues/nvmcommunity/alchemist-restful-api)

<!-- Project title -->
A library that helps you quickly get a rigorous and flexible RESTful-based API interface for your application.

## Testing

This package is production-ready with 193 tests and 1151 assertions

```bash
./vendor/phpunit/phpunit/phpunit
```

<!-- Project description -->
## Table of Contents

- [Table of Contents](#table-of-contents)
- [Changelog](#changelog)
- [Introduction](#introduction)
- [Prerequisites](#prerequisites)
- [Installation](#installation)
- [Laravel Integration](#laravel-integration)
- [Basic usage](#basic-usage)
- [More explanation about each component](#more-explanation-about-each-component)
  - [Field Selector](#field-selector)
  - [Resource Filtering](#resource-filtering)
  - [Resource Pagination](#resource-pagination)
  - [Resource Search](#resource-search)
  - [Resource Sort](#resource-sort)
- [License](#license)

## Changelog

- **1.0.0**: Initial release
- **2.0.0**: Add support for new way to define object and collection field structure
- **2.0.1**: [Add support for AlchemistAdapter to change parameter name in request input of default components](./docs/changelogs/2_0_1_Add_support_for_Adapter_to_change_parameter_name_in_request_input.md)
- **2.0.16**: (2024-04-28) Add more tests & fix unexpected behaviors
- **2.0.17**: (2024-06-14) Fix the incorrect pattern in the field parser
- **2.0.19**: (2024-06-14) Field parsing syntax supports spaces

## Introduction

Alchemist Restful API is a library that helps you quickly get a rigorous and flexible RESTful-based API interface for your application.

The library provides a set of components that you can use to build your API interface, including:

- **Field Selector**: Allows your API client to select the fields they want to retrieve, ensuring that all retrieved fields are within your control.
- **Resource Filtering**: Focuses on checking whether the filtering that your API client is using is in the defined filterable list or not.
- **Resource Pagination**: Support pagination through the offset and limit mechanism.
- **Resource Sort**: Support for flexible result returns with data sorted based on the sort and direction specified by the API client.
- **Resource Search**: When filtering through filter, the API client needs to clearly specify the filtering criteria. However, in the case of searching, the API client only needs to pass in the value to be searched for, and the backend will automatically define the filtering criteria from within.
- **Resource Offset Paginator**: Support pagination through the offset and limit mechanism.

## Prerequisites

- PHP 7.4
- Composer installed ([https://getcomposer.org](https://getcomposer.org/))

## Installation

```bash
composer require nvmcommunity/alchemist-restful-api
```

## Laravel Integration

If you are using Laravel, you can install another package called `laravel-eloquent-api` to integrate Alchemist Restful API with Eloquent ORM in Laravel.

```bash
composer require nvmcommunity/laravel-eloquent-api
```
See more details about how to use `nvmcommunity/laravel-eloquent-api` package at  in the [Laravel Eloquent API Documentation](https://packagist.org/packages/nvmcommunity/laravel-eloquent-api)
## Basic usage

Here is an example of how to use Alchemist Restful API to build a RESTful API interface for an Order API.

### Step 1: Define the API class

You need to define an API class that extends the `AlchemistQueryable` class and implement the methods for field selector, resource filtering, resource pagination, resource search, and resource sort.

```php
<?php

use Nvmcommunity\Alchemist\RestfulApi\Common\Integrations\AlchemistQueryable;
use Nvmcommunity\Alchemist\RestfulApi\FieldSelector\Handlers\FieldSelector;
use Nvmcommunity\Alchemist\RestfulApi\ResourceFilter\Handlers\ResourceFilter;
use Nvmcommunity\Alchemist\RestfulApi\ResourceFilter\Objects\FilteringRules;
use Nvmcommunity\Alchemist\RestfulApi\ResourcePaginations\OffsetPaginator\Handlers\ResourceOffsetPaginator;
use Nvmcommunity\Alchemist\RestfulApi\ResourceSearch\Handlers\ResourceSearch;
use Nvmcommunity\Alchemist\RestfulApi\ResourceSort\Handlers\ResourceSort;
/**
 * Example of Order Api Query
 */
class OrderApiQuery extends AlchemistQueryable
{
    /**
     * @param FieldSelector $fieldSelector
     * @return void
     */
    public static function fieldSelector(FieldSelector $fieldSelector): void
    {
        $fieldSelector->defineFieldStructure([
            'id',
            'order_date',
            'order_status',
            'order_items' => [
                'item_id',
                'product_id',
                'price',
                'quantity'
            ]
        ]);
    }

    /**
     * @param ResourceFilter $resourceFilter
     * @return void
     */
    public static function resourceFilter(ResourceFilter $resourceFilter): void
    {
        $resourceFilter->defineFilteringRules([
            FilteringRules::String('product_name', ['eq', 'contains']),
            FilteringRules::Date('order_date', ['eq', 'lte', 'gte'], ['Y-m-d']),
            FilteringRules::Integer('product_id', ['eq']),
            FilteringRules::Boolean('is_best_sales', ['eq']),
        ]);
    }

    /**
     * @param ResourceOffsetPaginator $resourceOffsetPaginator
     * @return void
     */
    public static function resourceOffsetPaginator(ResourceOffsetPaginator $resourceOffsetPaginator): void
    {
        $resourceOffsetPaginator
            ->defineDefaultLimit(10)
            ->defineMaxLimit(1000);
    }

    /**
     * @param ResourceSearch $resourceSearch
     * @return void
     */
    public static function resourceSearch(ResourceSearch $resourceSearch): void
    {
        $resourceSearch
            ->defineSearchCondition('product_name');
    }

    /**
     * @param ResourceSort $resourceSort
     * @return void
     */
    public static function resourceSort(ResourceSort $resourceSort): void
    {
        $resourceSort
            ->defineDefaultSort('id')
            ->defineDefaultDirection('desc')
            ->defineSortableFields(['id', 'created_at']);
    }
}
```
### Step 2: Validate the input parameters

Make sure to validate the input parameters passed in from the request input by using the `validate` method.

```php
<?php

use Nvmcommunity\Alchemist\RestfulApi\AlchemistRestfulApi;

// Assuming that the input parameters are passed in from the request input
$input = [
    'fields' => 'id,order_date,order_status,order_items{item_id,product_id,price,quantity}',
    'filtering' => [
        'order_date:lte' => '2023-02-26',
        'product_name:contains' => 'clothes hanger'
    ],
    'search' => 'clothes hanger',
    'sort' => 'id',
    'direction' => 'desc',
    'limit' => 10,
    'offset' => 0,
];

$restfulApi = AlchemistRestfulApi::for(OrderApiQuery::class, $input);

// Check if the input parameters are not valid, the validator will collect all errors and return them to you.
if (! $restfulApi->validate($errorBag)->passes()) {
    // var_dump(json_encode($errorBag->getErrors()));
    
    echo "validate failed"; die();
}
```

### Step 3: Alright, All the input parameters have been carefully validated

And finally, what you will receive here is fields, filtering, offset, search, and sort parameters from the API client.
All have been carefully validated.

```php
// Get all string fields that user want to retrieve
$restfulApi->fieldSelector()->flatFields();

// Get all filtering conditions that client passed in
$restfulApi->resourceFilter()->filtering();

$offsetPaginate = $restfulApi->resourceOffsetPaginator()->offsetPaginate();

// Get pagination limit and offset
$offsetPaginate->getLimit();
$offsetPaginate->getOffset();

$search = $restfulApi->resourceSearch()->search();

// Get search condition (defined in the API class) and search value (provided by user)
$search->getSearchCondition();
$search->getSearchValue();

$sort = $restfulApi->resourceSort()->sort();

// Get sort field and direction
$sort->getSortField();
$sort->getDirection();
```

## More explanation about each component

Below are the detailed explanations of each component that you can use to build your API interface.

### Field Selector

The Field Selector component allows your API client to select the fields they want to retrieve, ensuring that all retrieved fields are within your control.

```php
use Nvmcommunity\Alchemist\RestfulApi\AlchemistRestfulApi;

$restfulApi = new AlchemistRestfulApi([
    // The fields are passed in from the request input, fields are separated by commas, and subsidiary fields
    // are enclosed in `{}`.
    'fields' => 'id,order_date,order_status,order_items{item_id,product_id,price,quantity}'
]);

// Call `$restfulApi->fieldSelector()` to start the field selector builder, then your API definitions can be
// defined based on the chain of builder.
$fieldSelector = $restfulApi->fieldSelector()
    // If no field is passed up by the API Client, the default field will present.
    ->defineDefaultFields(['id'])
    
    // Your API client can be able to retrieve any fields in the list
    ->defineFieldStructure([
            'id',
            'order_date',
            'order_status',
            'order_items' => [
                'item_id',
                'product_id',
                'price',
                'quantity'
            ]
        ]);

// The important thing here is that your API will be rigorously checked by the validator, which will check things like
// whether the selected fields are in the list of "selectable" fields, the same for subsidiary fields, and also
// whether your API client is selecting subsidiary fields on atomic fields that do not have subsidiary fields,
// for example: "id{something}", where id is an atomic field and has no subsidiary fields.
if (! $restfulApi->validate($errorBag)->passes()) {
    // var_dump(json_encode($errorBag->getErrors()));
    
    echo "validate failed"; die();
}

// Finally, what you will receive by call the `$fieldSelector->fields()` method is a list of field objects, and everything has been carefully checked.

// Combine with the use of an ORM/Query Builder

$result = ExampleOrderQueryBuilder::select($fieldSelector->flatFields())->get();

// For other purposes, use `$fieldSelector->fields()` to obtain a complete map list of field object.

var_dump($fieldSelector->fields());
```

### Resource Filtering

Resource Filtering focuses on checking whether the filtering that your API client is using is in the defined filterable list or not.

```php
use Nvmcommunity\Alchemist\RestfulApi\AlchemistRestfulApi;
use Nvmcommunity\Alchemist\RestfulApi\ResourceFilter\Objects\FilteringRules;
use Nvmcommunity\Alchemist\RestfulApi\ResourceFilter\Objects\FilteringOptions;

$restfulApi = new AlchemistRestfulApi([
    // The filtering are passed in from the request input.
    // Use a colon `:` to separate filtering and operator.
    'filtering' => [
        'order_date:lte' => '2023-02-26',
        'product_name:contains' => 'clothes hanger'
    ]
]);

$resourceFilter = $restfulApi->resourceFilter()
    // Defining options for your API
    ->defineFilteringOptions(new FilteringOptions([
        // Your API client needs to pass order_date filtering with any operation in order to pass the validator
        'required' => ['order_date:any']
    ]))
    // Defining filtering for your API
    ->defineFilteringRules([
        // Your API allows filtering by product_name with the operations "eq" and "contains", and the data of the
        // filtering must be a string type
        FilteringRules::String('product_name', ['eq', 'contains']),

        // Your API allows filtering by product_name with the operations "eq", "lte" and "gte", and the data of
        // the filtering must be a valid date in `Y-m-d` format
        FilteringRules::Date('order_date', ['eq', 'lte', 'gte'], ['Y-m-d']),

        // Your API allows filtering by product_id with the operations "eq" and the data of the filtering must
        // be an integer type
        FilteringRules::Integer('product_id', ['eq']),

        // Your API allows filtering by is_best_sales with the operations "eq" and the data of the filtering must
        // be an integer type with value of: `0` (represent for false) or `1` (represent for true)
        FilteringRules::Boolean('is_best_sales', ['eq']),
    ]);

// Support for setting a default filtering in case your API client does not pass data to a specific filtering.
$resourceFilter->addFilteringIfNotExists('is_best_sales', 'eq', 1);

// Validate your API client filtering, the same concept with field selector above
if (! $restfulApi->validate($errorBag)->passes()) {
    // var_dump(json_encode($errorBag->getErrors()));

    echo "validate failed"; die();
}

// And finally, what you will receive is a list of filtering objects, and everything has been carefully checked.

// Combine with the use of an ORM/Query Builder

$conditions = array_map(static fn($filteringObj) => $filteringObj->flatArray(), $resourceFilter->filtering());

$result = ExampleOrderQueryBuilder::where($conditions)->get();

// For other purposes, use `$resourceFilter->filtering()` to obtain a complete map list of filtering object.

var_dump($resourceFilter->filtering());
```

#### Filtering Rules

The filtering rules are defined based on the `FilteringRules` class, which contains the following information:

- Filtering name: The name of the filtering that your API client will pass in.
- Supported operators: The list of operators that your API client can use to filter the data.
- Data type: The data type of the filtering data.

**Supported filtering rules**

```php
// `String` type is a special type that allows you to filter the data based on the string value.
FilteringRules::String(string $filtering, array $supportedOperators)

// `Integer` type is a special type that allows you to filter the data based on the integer value.
FilteringRules::Integer(string $filtering, array $supportedOperators)

// `Number` type is a special type that allows you to filter the data based on the numeric value.
FilteringRules::Number(string $filtering, array $supportedOperators)

// `Date` type allow you to define the date format that your API client can use to filter the data, default format: 'Y-m-d'
FilteringRules::Date(string $filtering, array $supportedOperators, array $formats = ['Y-m-d'])

// // `Datetime` type allow you to define the date and time format that your API client can use to filter the data, default format: 'Y-m-d H:i:s'
FilteringRules::Datetime(string $filtering, array $supportedOperators, array $formats = ['Y-m-d H:i:s'])

// `Enum` type is a special type that allows you to define a list of values that your API client can use to filter the data.
FilteringRules::Enum(string $filtering, array $supportedOperators, array $enums)

// `Boolean` type is a special type that allows you to define the boolean value that your API client can use to filter the data.
FilteringRules::Boolean(string $filtering, array $supportedOperators = [])
```

#### Filtering operators

Filtering with operator in request input can be represented in form of: `<filtering>:<operator>`

The operators passed in from the request input (Request Operator) will be converted to the target operator.
This table also describes the structure of filtering values for special data types such as:
`between`, `not between`, `in`, `not in`

**Supported operators**

| Request Operator | Target Operator    | Meaning                | Value Structure                        |
|------------------|--------------------|------------------------|----------------------------------------|
| eq               | \=                 | equal                  | \<value\>                              |
| ne               | \!=                | not equal              | \<value\>                              |
| lt               | \<                 | lower than             | \<value\>                              |
| gt               | \>                 | greater than           | \<value\>                              |
| lte              | \<=                | lower than or equal    | \<value\>                              |
| gte              | \>=                | greater than or equal  | \<value\>                              |
| contains         | contains _(*)_     | contains               | \<value\>                              |
| between          | between _(*)_      | between                | array(\<value[0]\>, \<value[1]\>)      |
| not_between      | not_between _(*)_  | not between            | array(\<value[0]\>, \<value[1]\>)      |
| in               | in _(*)_           | in                     | array(\<value[0]\>, \<value[1]\>, ...) |
| not_in           | not_in _(*)_       | not in                 | array(\<value[0]\>, \<value[1]\>, ...) |

_(*) Be careful with these operators, because they are not native operators in any database, you need to handle them manually._

### Resource Pagination

Support pagination through the offset and limit mechanism.

```php
$restfulApi = new AlchemistRestfulApi([
    // The limit and offset are passed in from the request input.
    'limit' => 10,
    'offset' => 0,
]);

$resourceOffsetPaginator = $restfulApi->resourceOffsetPaginator()
    // Define default limit for resource
    ->defineDefaultLimit(10)

     // Define max limit for resource (set max limit to `0` or not define it will disable max limit)
    ->defineMaxLimit(1000);


// Validate your API client pagination parameters (limit, offset), Check if the offset value passed in is negative
// or not, and whether the limit parameter passed in exceeds the max limit (if max limit defined).
if (! $resourceOffsetPaginator->validate($notification)->passes()) {
    // var_dump(json_encode($errorBag->getErrors()));

    echo "validate failed"; die();
}

// Receive an object containing parameters for offset, limit, and max limit.
$offsetPaginate = $resourceOffsetPaginator->offsetPaginate();

// Combine with the use of an ORM/Query Builder
$result = ExampleOrderQueryBuilder::limit($offsetPaginate->getLimit())->offset($offsetPaginate->getOffset())->get();
```

### Resource Sort

Support for flexible result returns with data sorted based on the sort and direction specified by the API client.

```php
$restfulApi = new AlchemistRestfulApi([
    // The sort and direction are passed in from the request input.
    'sort' => 'id',
    'direction' => 'desc',
]);

$resourceSort = $restfulApi->resourceSort()
    // define default sort field
    ->defineDefaultSort('id')
    
    // define default sort direction
    ->defineDefaultDirection('desc')

    // define list of field that client able to sort
    ->defineSortableFields(['id', 'created_at']);

// Validate your API client sort parameters (sort, direction), Check if the sort field passed in is in the list of
// sortable fields, and whether the direction parameter passed in is valid.
if (! $restfulApi->validate($errorBag)->passes()) {
    // var_dump(json_encode($errorBag->getErrors()));

    echo "validate failed"; die();
}

$sort = $resourceSort->sort();

// Combine with the use of an ORM/Query Builder

if (! empty($sort->getSortField())) {
    ExampleOrderQueryBuilder::orderBy($sort->getSortField(), $sort->getDirection());
}
```

### Resource Search

When filtering through filter, the API client needs to clearly specify the filtering criteria. However, in the case of searching, the API client only needs to pass in the value to be searched for, and the backend will automatically define the filtering criteria from within.

```php
$restfulApi = new AlchemistRestfulApi([
    // The search are passed in from the request input.
    'search' => 'clothes hanger',
]);

$resourceSearch = $restfulApi->resourceSearch()
    // define the search criteria
    ->defineSearchCondition('product_name');
    
$search = $resourceSearch->search();

// Combine with the use of an ORM/Query Builder
ExampleOrderQueryBuilder::where($search->getSearchCondition(), 'like', "%{$search->getSearchValue()}%");
```

## License

This Project is [MIT](./LICENSE) Licensed
