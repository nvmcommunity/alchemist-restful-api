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
    }

    /**
     * @throws AlchemistRestfulApiException
     */
    public function object(array $data): void
    {
        $this->deepValidateFieldStructure('object', '$', $data);

        $this->responseData = $data;
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

        if ($structure['type'] !== 'collection') {
            throw new AlchemistRestfulApiException(
                sprintf("The `%s` namespace is not a collection", implode('.', $previous))
            );
        }

        $fieldName = $lastKey;

        foreach ($data as $value) {
            $this->deepValidateFieldStructure(null, implode('.', $namespaceArr) .".$fieldName", $value);
        }

        foreach ($control as &$j) {
            $j[$fieldName] = $data[$j[$compareField]];
        }
    }

    /**
     * @param string|null $type
     * @param string $namespace
     * @param array $data
     * @return void
     * @throws AlchemistRestfulApiException
     */
    public function deepValidateFieldStructure(?string $type, string $namespace, array $data): void
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