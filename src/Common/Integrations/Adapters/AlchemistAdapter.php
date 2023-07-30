<?php

namespace Nvmcommunity\Alchemist\RestfulApi\Common\Integrations\Adapters;

use Nvmcommunity\Alchemist\RestfulApi\AlchemistRestfulApi;
use Nvmcommunity\Alchemist\RestfulApi\Common\Notification\CompoundErrors;
use Nvmcommunity\Alchemist\RestfulApi\Common\Notification\ErrorBag;

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
    function for(AlchemistRestfulApi $alchemistRestfulApi): void
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

            if (isset($errors->fieldSelector)) {
                $messages['fields'] = $errors->fieldSelector->getMessages();

                foreach ($messages['fields'] as &$error) {
                    if ($error['error_code'] === 'UNSELECTABLE_FIELD') {
                        $fieldStruct = $this->alchemistRestfulApi->fieldSelector()->getFieldStructure($error['error_namespace'])['sub'];
                        $error['selectable'] = array_keys($fieldStruct);
                    }
                }

                unset($error);
            }

            if (isset($errors->resourceFilter)) {
                $messages['filtering'] = $errors->resourceFilter->getMessages();
            }

            if (isset($errors->resourceOffsetPaginator)) {
                $messages['paginator'] = $errors->resourceOffsetPaginator->getMessages();
            }

            if (isset($errors->resourceSort)) {
                $messages['sort'] = $errors->resourceSort->getMessages();
            }


            return $messages;
        });
    }
}