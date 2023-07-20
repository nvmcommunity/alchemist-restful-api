<?php

namespace Nvmcommunity\Alchemist\RestfulApi\FieldSelector\Handlers;

use Nvmcommunity\Alchemist\RestfulApi\FieldSelector\Notifications\FieldSelectorErrorBag;
use Nvmcommunity\Alchemist\RestfulApi\Common\Exceptions\AlchemistRestfulApiException;
use Nvmcommunity\Alchemist\RestfulApi\FieldSelector\Objects\FieldObject;

class FieldSelector
{
    /**
     * @var FieldObject[]
     */
    private array $fields;

    /**
     * @var array
     */
    private array $selectableFields = [];

    /**
     * @var FieldObject[]
     */
    private array $defaultFields = [];

    /**
     * @param string $fields
     */
    public function __construct(string $fields)
    {
        $this->fields = $this->parseFields($fields);
    }

    /**
     * @param array $selectableFields
     * @return FieldSelector
     */
    public function defineFieldStructure(array $selectableFields): self
    {
        $this->selectableFields = $this->parseSelectable($selectableFields);

        return $this;
    }

    public function getFieldStructure(string $namespace = '$')
    {
        return $this->namespaceFieldStructure(['type' => 'root','sub' => $this->selectableFields], $namespace);
    }

    /**
     * @param array $selectableFields
     * @return array
     */
    private function parseSelectable(array $selectableFields): array
    {
        $res = [];

        foreach ($selectableFields as $selectableFieldKey => $selectableFieldValue) {
            $isAtomicField = is_numeric($selectableFieldKey);

            $fieldWithType = $this->parseFieldWithType(
                $isAtomicField ? $selectableFieldValue : $selectableFieldKey,
                is_array($selectableFieldValue)
            );

            $fieldName = $fieldWithType['field'];
            $fieldType = $fieldWithType['type'];

            if ($isAtomicField) {
                $res[$fieldName] = $fieldType;
            } else {
                $subFields = $selectableFieldValue;

                $res[$fieldName] = [
                    'type' => $fieldType,
                    'sub' => $this->parseSelectable($subFields)
                ];
            }
        }

        return $res;
    }

    /**
     * @param string $fieldWithType
     * @param bool $isObject
     * @return string[]
     */
    private function parseFieldWithType(string $fieldWithType, bool $isObject): array
    {
        $fieldWithTypeArr = explode('@', $fieldWithType);

        if (empty($fieldWithTypeArr[1])) {
            return ['field' => $fieldWithType, 'type' => $isObject ? 'object' : 'atomic'];
        }

        return ['field' => $fieldWithTypeArr[0], 'type' => $fieldWithTypeArr[1]];
    }

    /**
     * @return array
     */
    public function sampleStructure(): array
    {
        return $this->deepSampleStructure($this->selectableFields);
    }

    /**
     * @param array $fields
     * @return array
     */
    private function deepSampleStructure(array $fields): array
    {
        $res = [];

        foreach ($fields as $field => $value) {
            if (is_string($value)) {
                $res[$field] = $this->sampleGenerator($value);
            } else {
                if ($value['type'] === 'collection') {
                    $res[$field] = [$this->deepSampleStructure($value['sub'])];
                } else {
                    $res[$field] = $this->deepSampleStructure($value['sub']);
                }
            }
        }

        return $res;
    }

    /**
     * @param string $type
     * @return mixed
     */
    private function sampleGenerator(string $type)
    {
        $sampleType = [
            'atomic' => fn() => "",
            'date' => fn() => "",
            'datetime' => fn() => "",
            'integer' => fn() => 0,
            'string' => fn() => 0,
        ];

        return $sampleType[$type]();
    }


    /**
     * @param string[] $fields
     * @return FieldSelector
     */
    public function defineDefaultFields(array $fields): self
    {
        $this->defaultFields = $this->parseFields(implode(',', $fields));

        return $this;
    }

    /**
     * @param string $namespace
     * @param string[] $withFieldNames
     * @return FieldObject[]
     * @throws AlchemistRestfulApiException
     */
    public function fields(string $namespace = '$', array $withFieldNames = []): array
    {
        $fields = $this->fields ?: $this->defaultFields;

        $namespaceFields = $this->namespaceFields($fields, $namespace);

        if (! $namespaceFields) {
            throw new AlchemistRestfulApiException(
                sprintf("cannot find fields with `%s` namespace", $namespace)
            );
        }


        if (empty($withFieldNames)) {
            return $namespaceFields;
        }

        $result = [];

        $includeFieldNamesMap = array_flip($withFieldNames);

        foreach ($namespaceFields as $fieldName => $fieldObj) {
            if (array_key_exists($fieldName, $includeFieldNamesMap)) {
                $result[$fieldName] = $fieldObj;
            }
        }

        return $result;
    }

    /**
     * @param string $namespace
     * @param string[] $withFieldNames
     * @return string[]
     * @throws AlchemistRestfulApiException
     */
    public function flatFields(string $namespace = '$', array $withFieldNames = []): array
    {
        $fields = $this->fields($namespace, $withFieldNames);

        $result = [];

        foreach ($fields as $field) {
            $result[] = $field->getName();
        }

        return $result;
    }

    /**
     * @param string $namespace
     * @param string[] $withoutFieldNames
     * @return FieldObject[]
     * @throws AlchemistRestfulApiException
     */
    public function withoutFields(string $namespace = '$', array $withoutFieldNames = []): array
    {
        $fields = $this->fields ?: $this->defaultFields;

        $namespaceFields = $this->namespaceFields($fields, $namespace);

        if (! $namespaceFields) {
            throw new AlchemistRestfulApiException(
                sprintf("cannot find fields with `%s` namespace", $namespace)
            );
        }


        if (empty($withoutFieldNames)) {
            return $namespaceFields;
        }

        $result = [];

        $excludeFieldNamesMap = array_flip($withoutFieldNames);

        foreach ($namespaceFields as $fieldName => $fieldObj) {
            if (! array_key_exists($fieldName, $excludeFieldNamesMap)) {
                $result[$fieldName] = $fieldObj;
            }
        }

        return $result;
    }

    /**
     * @param string $namespace
     * @param string[] $withoutFieldNames
     * @return string[]
     * @throws AlchemistRestfulApiException
     */
    public function flatWithoutFields(string $namespace = '$', array $withoutFieldNames = []): array
    {
        $fields = $this->withoutFields($namespace, $withoutFieldNames);

        $result = [];

        foreach ($fields as $field) {
            $result[] = $field->getName();
        }

        return $result;
    }

    /**
     * @param $notification
     * @return FieldSelectorErrorBag
     */
    public function validate(&$notification = null): FieldSelectorErrorBag
    {
        $fields = $this->fields ?: $this->defaultFields;

        if (! $this->deepCompareSelectableFields('$', $fields, $this->selectableFields, $errors)) {
            return $notification = new FieldSelectorErrorBag(false, $errors['namespace'], $errors['fields']);
        }

        return $notification = new FieldSelectorErrorBag(true);
    }

    /**
     * @param string $namespace
     * @param array $fields
     * @param $selectableFields
     * @param $errors
     * @return bool
     */
    private function deepCompareSelectableFields(string $namespace, array $fields, $selectableFields, &$errors): bool
    {
        $isFieldValid = true;

        /** @var FieldObject $fieldObject */
        foreach ($fields as $fieldObject) {
            $diffWithSelectableFields = array_diff_key($fields, $selectableFields);

            if (! empty($diffWithSelectableFields)) {
                $isFieldValid = false;

                $errors = ['namespace' => $namespace, 'fields' => array_keys(array_map(fn(FieldObject $item) => 'ignored', $diffWithSelectableFields))];

                break;
            }
            if ($fieldObject->getSubFields()) {
                return $this->deepCompareSelectableFields(
                    "$namespace.{$fieldObject->getName()}",
                    $fieldObject->getSubFields(),
                    $selectableFields[$fieldObject->getName()]['sub'],
                    $errors
                );
            }
        }

        return $isFieldValid;
    }

    /**
     * @param string $fields
     * @return array
     */
    private function parseFields(string $fields): array
    {
        $result = [];

        $regex = '/([\w\s]+)(?:\.limit\((\d+)\))?(?:\{(.+?)\})?(?:,|$)/';

        preg_match_all($regex, $fields, $matches, PREG_SET_ORDER);

        foreach ($matches as $match) {
            $field          = trim($match[1]);
            $limit          = isset($match[2]) ? intval($match[2]) : null;
            $nestedFields   = $match[3] ?? null;

            if (! isset($result[$field])) {
                $result[$field] = new FieldObject($field, $limit ?? 0, $nestedFields ? $this->parseFields($nestedFields) : []);
            }
        }

        return $result;
    }

    /**
     * @param array $fields
     * @param string $namespace
     * @return array|null
     */
    private function namespaceFields(array $fields, string $namespace): ?array
    {
        if (empty($namespace)) {
            return $fields;
        }

        $namespaceArr = explode('.', $namespace);

        if (! empty($namespaceArr)) {

            $current = reset($namespaceArr);

            if ($current === '$') {
                unset($namespaceArr[0]);
                return $this->namespaceFields($fields, implode('.', $namespaceArr));
            }

            if (isset($fields[$current])) {
                unset($namespaceArr[0]);
                /** @var FieldObject[] $fields */
                return $this->namespaceFields($fields[$current]->getSubFields(), implode('.', $namespaceArr));
            }
        }

        return null;
    }

    /**
     * @param array $fields
     * @param string $namespace
     * @return array|null
     */
    private function namespaceFieldStructure(array $fieldStruct, string $namespace): ?array
    {
        if (empty($namespace)) {
            return [
                'type' => $fieldStruct['type'],
                'sub' => array_map(fn($e) => ! is_array($e) ? $e : $e['type'], $fieldStruct['sub'])
            ];
        }

        $namespaceArr = explode('.', $namespace);
        if (! empty($namespaceArr)) {

            $current = reset($namespaceArr);

            if ($current === '$') {
                unset($namespaceArr[0]);
                return $this->namespaceFieldStructure($fieldStruct, implode('.', $namespaceArr));
            }

            if (isset($fieldStruct['sub'][$current])) {
                unset($namespaceArr[0]);
                return $this->namespaceFieldStructure($fieldStruct['sub'][$current], implode('.', $namespaceArr));
            }
        }

        return null;
    }
}