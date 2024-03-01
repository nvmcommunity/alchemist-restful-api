<?php
namespace Nvmcommunity\Alchemist\RestfulApi\Response\Compose\Handlers;

use Nvmcommunity\Alchemist\RestfulApi\AlchemistRestfulApi;
use Nvmcommunity\Alchemist\RestfulApi\Common\Exceptions\AlchemistRestfulApiException;
use Nvmcommunity\Alchemist\RestfulApi\Common\Helpers\DotArrays;
use Nvmcommunity\Alchemist\RestfulApi\Common\Helpers\Arrays;

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
     * @param array $dataCollection
     * @return $this
     */
    public function fromCollection(array $dataCollection): self
    {
        $this->responseData = $dataCollection;

        $this->responseDataType = 'collection';

        return $this;
    }

    /**
     * @param array $data
     * @return $this
     */
    public function fromObject(array $data): self
    {
        $this->responseData = $data;

        $this->responseDataType = 'object';

        return $this;
    }

    public function compose(
        string $namespace, string $composeFieldName, string $compareField, array $incomingDataMap): self
    {
        $dotFlatKey = $this->calculateDotFlatKey($namespace, $compareField);
        $pattern = $this->calculateRegexPatternFromDotFlatKey($dotFlatKey);

        $data = $this->responseData;

        $flatData = DotArrays::array_dot_flatten($data, true, true, [$dotFlatKey]);

        $composeFieldStruct = $this->alchemist->fieldSelector()->getFieldStructure($namespace.'.'.$composeFieldName);
        $composeFieldStructType = $composeFieldStruct['type'];

        foreach ($flatData as $dotKey => $value) {
            $hasMatched = (bool)  preg_match("/^{$pattern}$/", $dotKey, $matches);

            if ($hasMatched) {
                $dotComposeKey = str_replace($compareField, $composeFieldName, $dotKey);

                if (! is_array($value)) {
                    if (array_key_exists($value, $incomingDataMap)) {
                        $flatData[$dotComposeKey] = $incomingDataMap[$value];
                    } else {
                        $flatData[$dotComposeKey] = ($composeFieldStructType === 'collection') ? [] : null;
                    }
                } else {
                    $flatData[$dotComposeKey] = array_reduce($value, function ($res, $identifier) use ($incomingDataMap) {
                        if (isset($incomingDataMap[$identifier])) {
                            $res[] = $incomingDataMap[$identifier];
                        }

                        return $res;
                    }, []);
                }
            }
        }

        $this->responseData = DotArrays::array_dot_unflatten($flatData);

        return $this;
    }

    /**
     * Clean redundant data from response data
     *
     * @return $this
     */
    public function cleanRedundantData(): self
    {
        if ($this->responseDataType === 'collection') {
            $this->responseData = $this->cleanRedundantDataCollection($this->responseData, '$');
        } else {
            $this->responseData = $this->cleanRedundantDataObject($this->responseData, '$');
        }

        return $this;
    }

    /**
     * @param array $data
     * @param string $namespace
     *
     * @return array
     */
    private function cleanRedundantDataObject(array $data, string $namespace): array
    {
        $fieldStructure = $this->alchemist->fieldSelector()->getFieldStructure($namespace);
        $inputFields = $this->alchemist->fieldSelector()->flatFields($namespace, false);

        $data = Arrays::array_with_keys($data, $inputFields);

        foreach ($data as $subField => $subData) {
            $subDataType = $fieldStructure['sub'][$subField];

            if (! empty($subData)) {
                switch ($subDataType) {
                    case 'collection':
                        $subData = $this->cleanRedundantDataCollection($subData, "{$namespace}.{$subField}");
                        break;
                    case 'object':
                        $subData = $this->cleanRedundantDataObject($subData, "{$namespace}.{$subField}");
                        break;
                }
            }

            $data[$subField] = $subData;
        }

        return $data;
    }

    /**
     * @param array $dataCollection
     * @param string $namespace
     *
     * @return array
     */
    private function cleanRedundantDataCollection(array $dataCollection, string $namespace): array
    {
        foreach ($dataCollection as $index => $data) {
            $dataCollection[$index] = $this->cleanRedundantDataObject($data, $namespace);
        }

        return $dataCollection;
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
     * @param string $compareField
     * @param string $mapType
     *
     * @return string
     */
    private function calculateDotFlatKey(string $namespace, string $compareField): string
    {
        $namespaceFragments = explode('.', $namespace);

        $pattern = '';
        $subNamespace = '';
        foreach ($namespaceFragments as $namespaceFragment) {
            $subNamespace .= $namespaceFragment;

            if ($namespaceFragment === '$') {
                $subNamespace .= '.';

                if ($this->responseDataType === 'collection') {
                    $pattern = '*.';
                }

                continue;
            }

            $fieldStructure = $this->alchemist->fieldSelector()->getFieldStructure($subNamespace);

            $subNamespace .= '.';

            $pattern .= "{$namespaceFragment}.";

            if ($fieldStructure['type'] === 'collection') {
                $pattern .= '*.';
            }
        }

        $pattern .= $compareField;

        return $pattern;
    }

    /**
     * @param string $dotFlatKey
     * @return string
     */
    public function calculateRegexPatternFromDotFlatKey(string $dotFlatKey): string
    {
        $pattern = str_replace('.', '\.', $dotFlatKey);
        $pattern = str_replace('*', '\d+', $pattern);

        return $pattern;
    }
}