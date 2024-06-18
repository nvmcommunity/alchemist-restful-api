<?php

namespace Nvmcommunity\Alchemist\RestfulApi\Common\Integrations\Adapters;

use Nvmcommunity\Alchemist\RestfulApi\AlchemistRestfulApi;
use Nvmcommunity\Alchemist\RestfulApi\Common\Notification\CompoundErrors;
use Nvmcommunity\Alchemist\RestfulApi\Common\Notification\ErrorBag;
use Nvmcommunity\Alchemist\RestfulApi\FieldSelector\Handlers\FieldSelector;
use Nvmcommunity\Alchemist\RestfulApi\FieldSelector\Notifications\FieldSelectorErrorBag;
use Nvmcommunity\Alchemist\RestfulApi\ResourceFilter\Handlers\ResourceFilter;
use Nvmcommunity\Alchemist\RestfulApi\ResourcePaginations\OffsetPaginator\Handlers\ResourceOffsetPaginator;
use Nvmcommunity\Alchemist\RestfulApi\ResourceSearch\Handlers\ResourceSearch;
use Nvmcommunity\Alchemist\RestfulApi\ResourceSort\Handlers\ResourceSort;

class AlchemistAdapter
{
    /**
     * @var AlchemistRestfulApi
     */
    protected AlchemistRestfulApi $alchemistRestfulApi;

    /**
     * @param AlchemistRestfulApi $alchemistRestfulApi
     * @return void
     */
    public function for(AlchemistRestfulApi $alchemistRestfulApi): void
    {
        $this->alchemistRestfulApi = $alchemistRestfulApi;
    }

    /**
     * @param ErrorBag $errorBag
     * @return void
     */
    public function errorHandler(ErrorBag $errorBag): void {
        $errorBag->setErrorHandler(function (CompoundErrors $errors) {
            $messages = [];

            foreach ($this->componentUses() as $componentClass) {
                $componentPropertyName = $this->alchemistRestfulApi->componentPropertyName($componentClass);

                if ($errors->{$componentPropertyName} === null) {
                    continue;
                }

                if ($componentClass === FieldSelector::class) {
                    $messages[$errors->fieldSelector->errorKey()] = $errors->fieldSelector->getMessages();

                    foreach ($messages[$errors->fieldSelector->errorKey()] as &$error) {
                        if ($error['error_code'] === FieldSelectorErrorBag::UNSELECTABLE_FIELD) {
                            $fieldStruct = $this->alchemistRestfulApi->fieldSelector()->getFieldStructure($error['error_namespace']);

                            if ($fieldStruct) {
                                $error['selectable'] = array_keys($fieldStruct['sub']);
                            }
                        }
                    }

                    unset($error);
                } else {
                    $messages[$errors->{$componentPropertyName}->errorKey()] = $errors->{$componentPropertyName}->getMessages();
                }
            }

            return $messages;
        });
    }

    /**
     * @return string[]
     */
    public function componentUses(): array
    {
        return [
            FieldSelector::class,
            ResourceFilter::class,
            ResourceOffsetPaginator::class,
            ResourceSort::class,
            ResourceSearch::class
        ];
    }

    /**
     * @return array
     */
    public function componentConfigs(): array
    {
        return [
            FieldSelector::class => [
                'request_params' => [
                    'fields_param' => 'fields',
                ]
            ],
            ResourceFilter::class => [
                'request_params' => [
                    'filtering_param' => 'filtering',
                ]
            ],
            ResourceOffsetPaginator::class => [
                'request_params' => [
                    'limit_param' => 'limit',
                    'offset_param' => 'offset',
                ]
            ],
            ResourceSort::class => [
                'request_params' => [
                    'sort_param' => 'sort',
                    'direction_param' => 'direction',
                ]
            ],
            ResourceSearch::class => [
                'request_params' => [
                    'search_param' => 'search',
                ]
            ]
        ];
    }
}