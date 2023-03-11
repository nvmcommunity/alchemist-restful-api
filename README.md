# Alchemist Restful API

<!-- Project badges -->
![Project Contributors](https://img.shields.io/github/contributors/nvmcommunity/alchemist-restful-api)
![Project License](https://img.shields.io/github/license/nvmcommunity/alchemist-restful-api)

A feature-rich library implementing RESTful API interface for PHP.

## Table of Contents

- [Table of Contents](#table-of-contents)
- [Introduction](#introduction)
- [Basic usage](#basic-usage)
- [Prerequisites](#prerequisites)
- [Installation](#installation)
- [Todos](#todos)
- [License](#license)

## Introduction

Based on practical experiences from implementing API interfaces on complex software systems, I have build this library with set of components that cover serveral common use cases for API interface. This library will help you quickly get a robust and flexible RESTful-based API interface and add many necessary features for your application.

## Prerequisites

- PHP 7.4

## Installation

```bash
composer require nvmcommunity/alchemist-restful-api
```

## Basic usage

### Field Selector

The `FieldSelector` is one of the features of Alchemist Restful API, it allows your API client selecting the fields that they want retrieved, ensuring that all retrieved fields are within your control. In addition, you can do the same with subsidiary fields.

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
if (! $fieldSelector->validate($notification)->passes()) {
    // var_dump($notification);
    
    // Note: $notification object is set of aggregated errors,
    // you need to implement your own comprehensive error message,
    // combined with your own multilingual support.
    
    echo "validate failed"; die();
}

// Finally, what you will receive by call the `$fieldSelector->fields()` method is a list of field objects, and everything has been carefully checked.

// Combine with the use of an ORM/Query Builder
$withOutFields = ['order_items'];
$result = ExampleOrderQueryBuilder::select($fieldSelector->flatFields($withOutFields))->get();

// For other purposes, use `$fieldSelector->fields()` to obtain a complete map list of field object.

var_dump($fieldSelector->fields());
```

### Resource Filtering

As a core feature of Alchemist Restful API, it focuses on checking whether the filters that your API client are using are in the defined filterable list or not. In addition, it also checks data types, valid filter operations, which filters are required, etc.


```php
use Nvmcommunity\Alchemist\RestfulApi\AlchemistRestfulApi;

$restfulApi = new AlchemistRestfulApi([
    // The filtering are passed in from the request input.
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
        // be an integer type with value of: `0` (represent for false) or 1 (represent for true)
        FilteringRules::Boolean('is_best_sales', ['eq']),
    ]);

// Support for setting a default filtering in case your API client does not pass data to a specific filtering.
$resourceFilter->addFilteringIfNotExists('is_best_sales', 'eq', 1);

// Validate your API client filtering, the same concept with field selector above
if (! $resourceFilter->validate($notification)->passes()) {
    // var_dump($notification);

    // Note: $notification object is set of aggregated errors,
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

## Todos

- [x] Document of basic concept
- [ ] Document about list of supported operation of resource filtering
- [ ] Adding Feature: Pagination, Search, Sorting
- [ ] Integration with Laravel Framework

## License

This Project is [MIT](./LICENSE) Licensed
