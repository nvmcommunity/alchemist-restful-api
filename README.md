# Alchemist Restful API

<!-- Project badges -->
![Project Contributors](https://img.shields.io/github/contributors/nvmcommunity/alchemist-restful-api)
![Project License](https://img.shields.io/github/license/nvmcommunity/alchemist-restful-api)

A feature-rich library implementing RESTful API interface for PHP.

## Table of Contents

- [Table of Contents](#table-of-contents)
- [Introduction](#introduction)
- [Basic usage](#basic-usage)
- [Release](#release)
- [License](#license)

## Introduction

Based on practical experiences from implementing API interfaces on complex software systems, I have build this library with set of components that cover serveral common use cases for API interface. This library will help you quickly get a robust and flexible RESTful-based API interface and add many necessary features for your application.

## Basic usage

### Field Selector

The `FieldSelector` is one of the features of Alchemist Restful API, it allows your API client selecting the fields that they want retrieved, ensuring that all retrieved fields are within your control. In addition, you can do the same with subsidiary fields.

```php
use Nvmcommunity\Alchemist\RestfulApi\AlchemistRestfulApi;

$restfulApi = new AlchemistRestfulApi([
    // The fields are passed in from the input, fields are separated by commas, and subsidiary fields are enclosed in `{}`.
    'fields' => 'id,order_date,order_status,order_items{product_id,product_name,quality}'
]);

// Call `$restfulApi->fieldSelector()` to start the field selector builder, then your API definitions can be defined based on the chain of builder.
$fieldSelector = $restfulApi->fieldSelector()
    ->defineSelectableFields([
        'id', 'order_date', 'order_status', 'order_items'
    ])
    ->defineSelectableSubFields('order_items', [
        'id', 'product_id', 'product_name', 'quality', 'price'
    ]);

// The important thing here is that your API will be rigorously checked by the validator, which will check things like whether the selected fields
// are in the list of "selectable" fields, the same for subsidiary fields, and also whether your API client is selecting subsidiary fields on atomic
// fields that do not have subsidiary fields, for example: "id{something}", where id is an atomic field and has no subsidiary fields.
if (! $fieldSelector->validate()->passes()) {
    // var_dump($fieldSelector->validate()->toArray());
    echo "validate failed"; die();
}

// And finally, what you will receive is a list of field objects, and everything has been carefully checked.
var_dump($fieldSelector->fields());
```

### Resource Filtering

As a core feature of Alchemist Restful API, it focuses on checking whether the filters that your API client are using are in the defined filterable list or not. In addition, it also checks data types, valid filter operations, which filters are required, etc.


document in complete...


## Release

(Expected release date: March 06, 2023)

## License

This Project is [MIT](./LICENSE) Licensed
