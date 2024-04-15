# Change Log

## [2.0.1] - 2024-04-15

## Add support for AlchemistAdapter to change parameter name in request input of default components

### Problem

Currently, all default components of the Alchemist Restful API use hardcoded parameter names in the request input. This makes it difficult for users to customize the parameter names to suit their needs.

### Solution

Add support for the `AlchemistAdapter` to change the parameter name in the request input of the default components. This will allow users to customize the parameter names to suit their needs.

### Implementation

Create your own `MyAdapter` class extending `AlchemistAdapter` and override the `componentConfigs` method to change the overall configuration of the default components that. Also, change the parameter name in the request input of those components.

```php

use Nvmcommunity\Alchemist\RestfulApi\FieldSelector\Handlers\FieldSelector;
use Nvmcommunity\Alchemist\RestfulApi\ResourceFilter\Handlers\ResourceFilter;
use Nvmcommunity\Alchemist\RestfulApi\ResourcePaginations\OffsetPaginator\Handlers\ResourceOffsetPaginator;
use Nvmcommunity\Alchemist\RestfulApi\ResourceSearch\Handlers\ResourceSearch;
use Nvmcommunity\Alchemist\RestfulApi\ResourceSort\Handlers\ResourceSort;

class MyAdapter extends AlchemistAdapter
{
    /**
     * @return array
     */
    public function componentConfigs(): array
    {
        return [
            FieldSelector::class => [
                'request_params' => [
                    // change the fields parameter to whatever you want
                    'fields_param' => 'fields',
                ]
            ],
            ResourceFilter::class => [
                'request_params' => [
                    // change the filtering parameter to whatever you want
                    'filtering_param' => 'filtering',
                ]
            ],
            ResourceOffsetPaginator::class => [
                'request_params' => [
                    // change the limit and offset parameter to whatever you want
                    'limit_param' => 'limit',
                    'offset_param' => 'offset',
                ]
            ],
            ResourceSort::class => [
                'request_params' => [
                    // change the sort and direction parameter to whatever you want
                    'sort_param' => 'sort',
                    'direction_param' => 'direction',
                ]
            ],
            ResourceSearch::class => [
                'request_params' => [
                    // change the search parameter to whatever you want
                    'search_param' => 'search',
                ]
            ]
        ];
    }
}
```

Then, use the `MyAdapter` class when creating the `AlchemistRestfulApi` instance.

```php
$restfulApi = AlchemistRestfulApi::for(ExampleOrderApiQuery::class, $requestInput, new MyAdapter());
```

[Go back to home](../../README.md)