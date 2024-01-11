<?php

namespace Nvmcommunity\Alchemist\RestfulApi\Response\Compose\Handlers;

use Nvmcommunity\Alchemist\RestfulApi\AlchemistRestfulApi;
use Nvmcommunity\Alchemist\RestfulApi\Common\Exceptions\AlchemistRestfulApiException;

class ResponseCompose
{
    private AlchemistRestfulApi $alchemist;

    /**
     * @var array
     */
    private array $responseData = [];

    /**
     * value: object|collection
     *
     * @var string
     */
    private string $responseDataType;

    /**
     * @param AlchemistRestfulApi $alchemist
     */
    public function __construct(AlchemistRestfulApi $alchemist)
    {
        $this->alchemist = $alchemist;
    }

    /**
     * @return array
     */
    public function responseData(): array
    {
        return $this->responseData;
    }

    /**
     * @throws AlchemistRestfulApiException
     */
    public function collection(array $data): void
    {
        $this->deepValidateFieldStructure('collection', '$', $data);

        $this->responseData = $data;

        $this->responseDataType = 'collection';
    }

    /**
     * @throws AlchemistRestfulApiException
     */
    public function object(array $data): void
    {
        $this->deepValidateFieldStructure('object', '$', $data);

        $this->responseData = $data;

        $this->responseDataType = 'object';
    }

    /**
     * @throws AlchemistRestfulApiException
     */
    public function composeByDataMap(string $namespace, string $composeFieldName, string $mapType, string $compareField, array $incomingDataMap): void
    {
        $namespaceArr = explode('.', $namespace);

        $namespaceData = &$this->responseData;

        $previous = [];
        foreach ($namespaceArr as $item) {
            $previous[] = $item;
            $fieldStructure = $this->alchemist->fieldSelector()->getFieldStructure(implode('.', $previous));

            if ($fieldStructure['type'] === 'root') {
                continue;
            }

            $namespaceData = &$namespaceData[$item];
        }

        if (in_array($fieldStructure['type'], ['collection', 'object', 'root']) === false) {
            throw new AlchemistRestfulApiException(
                sprintf("The `%s` namespace is not a collection or object", implode('.', $previous))
            );
        }

        foreach ($incomingDataMap as $incomingDatumMap) {
            if ($fieldStructure['sub'][$composeFieldName] === 'atomic') {
                continue;
            }

            if (is_array($incomingDatumMap)) {
                $this->deepValidateFieldStructure(null, implode('.', $namespaceArr) .".$composeFieldName", $incomingDatumMap);
            }
        }

        $responseDataType = $fieldStructure['type'];

        if ($fieldStructure['type'] === 'root') {
            $responseDataType = $this->responseDataType;
        }

        if ($responseDataType === 'collection') {
            $namespaceCollection = &$namespaceData;

            foreach ($namespaceCollection as &$namespaceCollectionItem) {
                $namespaceCollectionItem = $this->mapIncomingDataIntoNamespaceData(
                    $namespace, $mapType, $incomingDataMap, $namespaceCollectionItem, $compareField, $composeFieldName
                );
            }

            unset($namespaceCollectionItem);
        }

        if ($responseDataType === 'object') {
            $namespaceObject = &$namespaceData;

            $namespaceObject = $this->mapIncomingDataIntoNamespaceData(
                $namespace, $mapType, $incomingDataMap, $namespaceObject, $compareField, $composeFieldName
            );
        }
    }

    /**
     * @param string|null $type
     * @param string $namespace
     * @param array $data
     * @return void
     * @throws AlchemistRestfulApiException
     */
    private function deepValidateFieldStructure(?string $type, string $namespace, array $data): void
    {
        $structure = $this->alchemist->fieldSelector()->getFieldStructure($namespace);

        if ($structure['type'] === 'root') {
            $structure['type'] = $type;
        }

        if ($structure['type'] === 'collection') {
            if (! array_is_list($data)) {
                throw new AlchemistRestfulApiException(
                    sprintf("Data with `%s` namespace must be a list", $namespace)
                );
            }

            foreach ($data as $object) {
                foreach ($object as $field => $value) {
                    if (! array_key_exists($field, $structure['sub'])) {
                        throw new AlchemistRestfulApiException(
                            sprintf("`%s` field doesn't exist at `%s` namespace collection", $field, $namespace)
                        );
                    }

                    if (in_array($structure['sub'][$field], ['collection', 'object'])) {
                        $this->deepValidateFieldStructure(null, $namespace.".$field", $value);
                    }
                }

                break;
            }
        } else { // type object
            foreach ($data as $field => $value) {
                if (! array_key_exists($field, $structure['sub'])) {
                    throw new AlchemistRestfulApiException(
                        sprintf("`%s` field doesn't exist at `%s` namespace collection", $field, $namespace)
                    );
                }
                if (in_array($structure['sub'][$field], ['collection', 'object'])) {
                    $this->deepValidateFieldStructure(null, $namespace.".$field", $value);
                }
            }
        }
    }

    /**
     * @param string $namespace
     * @param string $mapType
     * @param array $incomingDataMap
     * @param $namespaceData
     * @param string $compareField
     * @param string $composeFieldName
     * @return array
     * @throws AlchemistRestfulApiException
     */
    private function mapIncomingDataIntoNamespaceData(string $namespace, string $mapType, array $incomingDataMap, $namespaceData, string $compareField, string $composeFieldName): array
    {
        if (! isset($namespaceData[$compareField])) {
            throw new AlchemistRestfulApiException(
                sprintf("The `%s` field doesn't exist at `%s` namespace", $compareField, $namespace)
            );
        }

        if ($mapType === 'VALUE') {
            $namespaceData[$composeFieldName] = null;

            if (isset($namespaceData[$compareField])) {
                $namespaceData[$composeFieldName] = $incomingDataMap[$namespaceData[$compareField]] ?? null;
            }
        } else if ($mapType === 'INNER_VALUE') {
            if (! is_array($namespaceData[$compareField])) {
                throw new AlchemistRestfulApiException(
                    sprintf("By using `INNER_VALUE` map type, the `%s` field at `%s` namespace must be type of array", $compareField, $namespace)
                );
            }

            foreach ($namespaceData[$compareField] as $key => $value) {
                if (!isset($incomingDataMap[$value])) {
                    continue;
                }

                if (!isset($namespaceData[$composeFieldName])) {
                    $namespaceData[$composeFieldName] = [];
                }

                $namespaceData[$composeFieldName][$key] = $incomingDataMap[$value];
            }
        }
        return $namespaceData;
    }
}