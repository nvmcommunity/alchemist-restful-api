# Alchemist Restful API

<!-- Project badges -->
![Project Contributors](https://img.shields.io/github/contributors/nvmcommunity/alchemist-restful-api)
![Project License](https://img.shields.io/github/license/nvmcommunity/alchemist-restful-api)

A feature-rich library implementing RESTful API interface for PHP.

## Table of Contents

- [Table of Contents](#table-of-contents)
- [Introduction](#introduction)
- [Prerequisites](#prerequisites)
- [Installation](#installation)
- [Basic usage](#basic-usage)
  - [Field Selector](#field-selector)
  - [Resource Filtering](#resource-filtering)
  - [Resource Pagination](#resource-pagination)
  - [Resource Search](#resource-search)
  - [Resource Sort](#resource-sort)
- [Combine all definitions](#combine-all-definitions)
- [Todos](#todos)
- [License](#license)

## Introduction

Based on practical experiences from implementing API interfaces on complex software systems, I founded the project with my colleagues in NVM Community core team who built this library with set of components that cover serveral common use cases for API interface. This library will help you quickly get a robust and flexible RESTful-based API interface and add many necessary features for your application.

## Prerequisites

- PHP 7.4
- Composer installed ([https://getcomposer.org](https://getcomposer.org/))

## Installation

```bash
composer require nvmcommunity/alchemist-restful-api
```

## Basic usage

This library acts as a layer that processes input parameters from the API client and returns the corresponding object results based on the parameters passed for processing in the later stage.

Based on the modular design concept, this library is divided into separate modules that handle independently to each other, from receiving request input parameters to validating data and returning corresponding results.

### Field Selector

The Field Selector is one of the features of Alchemist Restful API, it allows your API client selecting the fields that they want retrieved, ensuring that all retrieved fields are within your control. In addition, you can do the same with subsidiary fields.

```php
use Nvmcommunity\Alchemist\RestfulApi\AlchemistRestfulApi;

$restfulApi = new AlchemistRestfulApi([
    // The fields are passed in from the request input, fields are separated by commas, and subsidiary fields
    // are enclosed in `{}`.
    'fields' => 'id,order_date,order_status,order_items{product_id,product_name,quality}'
]);

// Call `$restfulApi->fieldSelector()` to start the field selector builder, then your API definitions can be
// defined based on the chain of builder.
$fieldSelector = $restfulApi->fieldSelector()
    // If no field is passed up by the API Client, the default field will present.
    ->defineDefaultFields(['id'])
    
    // Your API client can be able to retrieve any fields in the list
    ->defineSelectableFields([
        'id', 'order_date', 'order_status', 'order_items'
    ])
    
    // Defining the list of subsidiary field of `order_items` field
    ->defineSelectableSubFields('order_items', [
        'id', 'product_id', 'product_name', 'quality', 'price'
    ]);

// The important thing here is that your API will be rigorously checked by the validator, which will check things like
// whether the selected fields are in the list of "selectable" fields, the same for subsidiary fields, and also
// whether your API client is selecting subsidiary fields on atomic fields that do not have subsidiary fields,
// for example: "id{something}", where id is an atomic field and has no subsidiary fields.
if (! $restfulApi->validate($errorBag)->passes()) {
    // var_dump($errorBag);
    
    // Note: $errorBag object contains set of aggregated errors,
    // you need to implement your own comprehensive error message,
    // combined with your own multilingual support.
    
    echo "validate failed"; die();
}

// Finally, what you will receive by call the `$fieldSelector->fields()` method is a list of field objects, and everything has been carefully checked.

// Combine with the use of an ORM/Query Builder

$result = ExampleOrderQueryBuilder::select($fieldSelector->flatFields($withOutFields = ['order_items']))->get();

// For other purposes, use `$fieldSelector->fields()` to obtain a complete map list of field object.

var_dump($fieldSelector->fields());
```

### Resource Filtering

As a core feature of Alchemist Restful API, it focuses on checking whether the filtering that your API client are using are in the defined filterable list or not. In addition, it also checks data types, valid filtering operations, which filtering are required, etc.

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
    // var_dump($errorBag);

    // Note: $errorBag object contains set of aggregated errors,
    // you need to implement your own comprehensive error message,
    // combined with your own multilingual support.
    echo "validate failed"; die();
}

// And finally, what you will receive is a list of filtering objects, and everything has been carefully checked.

// Combine with the use of an ORM/Query Builder

$conditions = array_map(static fn($filteringObj) => $filteringObj->flatArray(), $resourceFilter->filtering());

$result = ExampleOrderQueryBuilder::where($conditions)->get();

// For other purposes, use `$resourceFilter->filtering()` to obtain a complete map list of filtering object.

var_dump($resourceFilter->filtering());
```

### Filtering Rules

A Filtering Rules Object is used to define filtering with the following information:

- The name of the filtering
- Supported operations of the filtering
- Data type of the filtering value
- Format of the filtering data
- Fixed values allowed to be passed in for the filtering.

**Supported filtering rules**

```php
// String type filtering
FilteringRules::String(string $filtering, array $supportedOperators)

// Integer type filtering
FilteringRules::Integer(string $filtering, array $supportedOperators)

// Numeric type filtering
FilteringRules::Number(string $filtering, array $supportedOperators)

// Date type filtering, default format: 'Y-m-d'
FilteringRules::Date(string $filtering, array $supportedOperators, array $formats = ['Y-m-d'])

// Datetime type date time, default format: 'Y-m-d H:i:s'
FilteringRules::Datetime(string $filtering, array $supportedOperators, array $formats = ['Y-m-d H:i:s'])

// Enum type
FilteringRules::Enum(string $filtering, array $supportedOperators, array $enums)

// Boolean type filtering: `0` (represent for false) or `1` (represent for true)
FilteringRules::Boolean(string $filtering, array $supportedOperators = [])
```

### Filtering operators

Filtering with operator in request input can be represented in form of: `<filtering>:<operator>`

The operators passed in from the request input (Request Operator) will be converted to the target operator. 
This table also describes the structure of filtering values for special data types such as:
`between`, `not between`, `in`, `not in`

**Supported operators**

| Request Operator | Target Operator | Meaning                | Value Structure                        |
|------------------|-----------------|------------------------|----------------------------------------|
| eq               | \=              | equal                  | \<value\>                              |
| ne               | \!=             | not equal              | \<value\>                              |
| lt               | \<              | lower than             | \<value\>                              |
| gt               | \>              | greater than           | \<value\>                              |
| lte              | \<=             | lower than or equal    | \<value\>                              |
| gte              | \>=             | greater than or equal  | \<value\>                              |
| contains         | contains _(*)_  | contains               | \<value\>                              |
| between          | between         | between                | array(\<value[0]\>, \<value[1]\>)      |
| not_between      | not between     | not between            | array(\<value[0]\>, \<value[1]\>)      |
| in               | in              | in                     | array(\<value[0]\>, \<value[1]\>, ...) |
| not_in           | not in          | not in                 | array(\<value[0]\>, \<value[1]\>, ...) |

_(*) Be careful with this operation, you need to handle additional processing to convert it to the native operator of the database management system._

## Resource Pagination

Support pagination through the offset and limit mechanism.

```php
$restfulApi = new AlchemistRestfulApi([
    // The limit and offset are passed in from the request input.
    'limit' => 10,
    'offset' => 0,
]);

$resourceOffsetPaginator = $restfulApi->resourceOffsetPaginator()
    // Set max limit for resource (don't call this method or set max limit to `0` to make your resource unlimited),
    // if limit is not passed in from the request input, the max limit parameter will override the limit parameter.
    ->defineMaxLimit(1000);


// Validate your API client pagination parameters (limit, offset), Check if the offset value passed in is negative
// or not, and whether the limit parameter passed in exceeds the max limit (if max limit defined).
if (! $resourceOffsetPaginator->validate($notification)->passes()) {
    // var_dump($notification);

    // Note: $errorBag object contains set of aggregated errors,
    // you need to implement your own comprehensive error message,
    // combined with your own multilingual support.

    echo "validate failed"; die();
}

// Receive an object containing parameters for offset, limit, and max limit.
$offsetPaginate = $resourceOffsetPaginator->offsetPaginate();

// Combine with the use of an ORM/Query Builder
$result = ExampleOrderQueryBuilder::limit($offsetPaginate->getLimit())->offset($offsetPaginate->getOffset())->get();
```

## Resource Sort

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

if (! $restfulApi->validate($errorBag)->passes()) {
    // var_dump($errorBag);

    // Note: $errorBag object contains set of aggregated errors,
    // you need to implement your own comprehensive error message,
    // combined with your own multilingual support.

    echo "validate failed"; die();
}

$sort = $resourceSort->sort();

// Combine with the use of an ORM/Query Builder

if (! empty($sort->getSortField())) {
    ExampleOrderQueryBuilder::orderBy($sort->getSortField(), $sort->getDirection());
}
```

## Resource Search

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

## Combine all definitions

In the "basic concept" section, we have been provided with detailed usage of each module of this library. In reality, we
will be doing everything at once, so the following instructions are an example of how we can easily implement an API.

### Step 1: Define all the details about an API
Define all the details of an API on the same class, apply polymorphism in object-oriented programming.

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
        $fieldSelector->defineSelectableFields([
            'id', 'order_date', 'order_status', 'order_items'
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
### Step 2: Extract the detailed definitions of an API.

Extract details of an API taken from any class that implements AlchemistQueryable.

```php
<?php

use Nvmcommunity\Alchemist\RestfulApi\AlchemistRestfulApi;

// Input from the client.
$input = [
    'fields' => 'id,order_date,order_status,order_items{product_id,product_name,quality}',
    'filtering' => [
        'order_date:lte' => '2023-02-26',
        'product_name:contains' => 'clothes hanger'
    ],
    'search' => 'clothes hanger',
    'sort' => 'id',
    'direction' => 'desc',
    'offset' => 0,
    'limit' => 10,
];

$restfulApi = AlchemistRestfulApi::for(OrderApiQuery::class, $input);

// Check all errors
if (! $restfulApi->validate($errorBag)->passes()) {
    // var_dump($errorBag);
    
    echo "validate failed"; die();
}

// Warning: The following code is just pseudo-code aimed at making it easy to visualize how to use this library.
// You need to implement it yourself to achieve the desired functionality.
$result = ExampleOrderQueryBuilder::select($fieldSelector->flatFields($withOutFields = ['order_items']))
            ->where(array_map(static fn($filteringObj) => $filteringObj->flatArray(), $resourceFilter->filtering()))
            ->limit($offsetPaginate->getLimit())->offset($offsetPaginate->getOffset())
            ->orderBy($sort->getSortField(), $sort->getDirection());
            ->where($search->getSearchCondition(), 'like', "%{$search->getSearchValue()}%")->get();

```

## Todos

- [x] Document of basic concepts
- [ ] Integration with Laravel Framework

## License

This Project is [MIT](./LICENSE) Licensed
