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
    public function compose(string $namespace, string $compareField, array $data): void
    {
        $namespaceArr = explode('.', $namespace);

        $lastKey = array_pop($namespaceArr);

        $control = &$this->responseData;

        $previous = [];
        foreach ($namespaceArr as $item) {
            $previous[] = $item;
            $structure = $this->alchemist->fieldSelector()->getFieldStructure(implode('.', $previous));

            if ($structure['type'] === 'root') {
                continue;
            }

            $control = &$control[$item];
        }

        if (in_array($structure['type'], ['collection', 'object', 'root']) === false) {
            throw new AlchemistRestfulApiException(
                sprintf("The `%s` namespace is not a collection or object", implode('.', $previous))
            );
        }

        $fieldName = $lastKey;

        foreach ($data as $value) {
            if ($structure['sub'][$fieldName] === 'atomic') {
                continue;
            }

            if (is_array($value)) {
                $this->deepValidateFieldStructure(null, implode('.', $namespaceArr) .".$fieldName", $value);
            }
        }

        $o = $structure['type'];

        if ($structure['type'] === 'root') {
            $o = $this->responseDataType;
        }

        if ($o === 'collection') {
            foreach ($control as &$j) {
                $j[$fieldName] = $data[$j[$compareField]];
            }
        }

        if ($o === 'object') {
            $control[$fieldName] = $data[$control[$compareField]];
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
}