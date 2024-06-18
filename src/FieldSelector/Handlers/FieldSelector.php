<?php

namespace Nvmcommunity\Alchemist\RestfulApi\FieldSelector\Handlers;

use Nvmcommunity\Alchemist\RestfulApi\Common\Objects\BaseAlchemistComponent;
use Nvmcommunity\Alchemist\RestfulApi\FieldSelector\Notifications\FieldSelectorErrorBag;
use Nvmcommunity\Alchemist\RestfulApi\Common\Exceptions\AlchemistRestfulApiException;
use Nvmcommunity\Alchemist\RestfulApi\FieldSelector\Objects\FieldObject;
use Nvmcommunity\Alchemist\RestfulApi\FieldSelector\Objects\Structure\Abstracts\FieldStructure;
use Nvmcommunity\Alchemist\RestfulApi\FieldSelector\Objects\Structure\CollectionStructure;
use Nvmcommunity\Alchemist\RestfulApi\FieldSelector\Objects\Structure\ObjectStructure;

class FieldSelector extends BaseAlchemistComponent
{
    /**
     * @var mixed
     */
    private $originalInput;

    /**
     * @var FieldObject[]
     */
    private array $fields = [];

    /**
     * @var array
     */
    private array $selectableFields = [];

    /**
     * @var FieldObject[]
     */
    private array $defaultFields = [];

    /**
     * @param mixed $fields
     */
    public function __construct($fields)
    {
        $this->originalInput = $fields;

        if (is_string($fields)) {
            $this->fields = $this->parseFields($fields);
        }
    }

    /**
     * @param array $selectableFields
     * @return FieldSelector
     */
    public function defineFieldStructure(array $selectableFields): self
    {
        $this->selectableFields = [
            'type' => 'collection',
            'sub' => $this->parseSelectable($selectableFields),
            'substitute' => null,
            'defaultFields' => [],
        ];

        return $this;
    }

    /**
     * @param string $namespace
     * @return array|null
     */
    public function getFieldStructure(string $namespace = '$'): ?array
    {
        if (empty($this->selectableFields)) {
            return null;
        }

        return $this->namespaceFieldStructure(['type' => 'root', 'sub' => $this->selectableFields['sub']], $namespace);
    }

    /**
     * @param string $namespace
     * @return array|null
     */
    public function getSubstitutes(string $namespace = '$'): ?array
    {
        return $this->namespaceSubstitutes($this->selectableFields, $namespace);
    }

    /**
     * @param array $selectableFields
     * @return array
     */
    private function parseSelectable(array $selectableFields): array
    {
        $res = [];

        foreach ($selectableFields as $selectableFieldKey => $selectableFieldValue) {
            $substitute = null;
            $defaultFields = [];

            if ($selectableFieldValue instanceof ObjectStructure) {
                $fieldName = $selectableFieldValue->getName();
                $defaultFields = $selectableFieldValue->getDefaultFields();
                $fieldType = 'object';

                if ($selectableFieldValue->getSubstitute()) {
                    $substitute = $selectableFieldValue->getSubstitute();
                }
            } elseif ($selectableFieldValue instanceof CollectionStructure) {
                $fieldName = $selectableFieldValue->getName();
                $defaultFields = $selectableFieldValue->getDefaultFields();
                $fieldType = 'collection';

                if ($selectableFieldValue->getSubstitute()) {
                    $substitute = $selectableFieldValue->getSubstitute();
                }
            } else {
                $fieldName = $selectableFieldValue;
                $fieldType = 'atomic';
            }

            # start legacy
            if (is_string($selectableFieldKey)) {
                $fieldWithType = $this->parseFieldWithType($selectableFieldKey, is_array($selectableFieldValue));

                $fieldName = $fieldWithType['field'];
                $fieldType = $fieldWithType['type'];
            }
            # end legacy

            if ($fieldType === 'atomic') {
                $res[$fieldName] = $fieldType;
            } else {
                $subFields = $selectableFieldValue;

                if (! is_array($subFields)) {
                    $subFields = $subFields->getFields();
                }

                $res[$fieldName] = [
                    'type' => $fieldType,
                    'sub' => $this->parseSelectable($subFields),
                    'substitute' => $substitute,
                    'defaultFields' => $defaultFields,
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
     */
    public function fields(string $namespace = '$', array $withFieldNames = []): array
    {
        $fields = $this->fields ?: $this->defaultFields;

        $namespaceFields = $this->namespaceFields($fields, $namespace);

        if (empty($namespaceFields)) {
            $namespaceFields = $this->namespaceDefaultFields($this->selectableFields, $namespace);
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
     * @param bool $substitute
     * @param string[] $withFieldNames
     * @return string[]
     */
    public function flatFields(string $namespace = '$', bool $substitute = true, array $withFieldNames = []): array
    {
        $fields = $this->fields($namespace, $withFieldNames);

        $substitutes = $this->namespaceSubstitutes($this->selectableFields, $namespace);

        $result = [];
        foreach ($fields as $field) {
            if ($substitute) {
                if (($substitutes[$field->getName()] ?? null) === '&') {
                    continue;
                }

                $result[] = ($substitutes[$field->getName()] ?? $field->getName());
            } else {
                $result[] = $field->getName();
            }
        }

        return $result;
    }

    /**
     * @param string $namespace
     * @param string[] $withoutFieldNames
     * @return FieldObject[]
     */
    public function withoutFields(string $namespace = '$', array $withoutFieldNames = []): array
    {
        $fields = $this->fields ?: $this->defaultFields;

        $namespaceFields = $this->namespaceFields($fields, $namespace);

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
        if (! is_null($this->originalInput) && ! is_string($this->originalInput)) {
            return $notification = new FieldSelectorErrorBag(false, '', [], true);
        }

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
            $diffWithSelectableFields = array_diff_key($fields, $selectableFields['sub'] ?? []);

            if (! empty($diffWithSelectableFields)) {
                $isFieldValid = false;

                $errors = ['namespace' => $namespace, 'fields' => array_keys(array_map(fn(FieldObject $item) => 'ignored', $diffWithSelectableFields))];

                break;
            }

            if ($fieldObject->getSubFields()) {
                return $this->deepCompareSelectableFields(
                    "$namespace.{$fieldObject->getName()}",
                    $fieldObject->getSubFields(),
                    $selectableFields['sub'][$fieldObject->getName()],
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

        $fields = str_replace('{}', '', $fields);

        if (empty($fields)) {
            return [];
        }

        $regex = '/(?:([^,{}\n]*)\{(?:[^{}\n]*(?R)?)*\}[^,{}\n]*)+|(?<=,|^)[^,{}\n]*/';

        preg_match_all($regex, $fields, $matches, PREG_SET_ORDER);

        foreach ($matches as $match) {
            if (isset($match[1])) {
                $fieldWithOptions = $this->parseFieldOptions($match[1]);
                $nestedFields = $this->removeBraces($this->removePrefix($match[0], $match[1]));
            } else {
                $fieldWithOptions = $this->parseFieldOptions($match[0]);
                $nestedFields = null;
            }

            $field          = $fieldWithOptions['field'];
            $limit          = isset($fieldWithOptions['options']['limit']) ? (int)$fieldWithOptions['options']['limit'] : null;

            if (! isset($result[$field])) {
                $result[$field] = new FieldObject($field, $limit ?? 0, $nestedFields ? $this->parseFields($nestedFields) : []);
            }
        }

        return $result;
    }

    /**
     * @param string $string
     * @param string $prefix
     * @return string
     */
    private function removePrefix(string $string, string $prefix): string
    {
        if (substr($string, 0, strlen($prefix)) === $prefix) {
            $string = substr($string, strlen($prefix));
        }

        return $string;
    }

    /**
     * @param string $string
     * @return string
     */
    private function removeBraces(string $string): string
    {
        if ($string[0] === '{') {
            $string = substr($string, 1);
        }

        if (substr($string, -1) === '}') {
            $string = substr($string, 0, -1);
        }

        return $string;
    }

    /**
     * @param string $fieldWithOptions
     * @return array
     */
    private function parseFieldOptions(string $fieldWithOptions): array
    {
        $regex = '/(?:[^.()\n]*\((?:[^()\n]*(?R)?)*\)[^.()\n]*)+|(?<=,|^)[^.()\n]*/';

        preg_match_all($regex, $fieldWithOptions, $matches, PREG_SET_ORDER);

        $fieldName = '';
        $options = [];

        foreach ($matches as $match) {
            if (empty($fieldName)) {
                $fieldName = trim($match[0]);
            } else {
                $res = $this->parseFieldOneOption($match[0]);
                $options[$res[0]] = $res[1];
            }
        }

        return [
            'field' => $fieldName,
            'options' => $options
        ];
    }

    /**
     * @param string $fieldWithOptions
     * @return array
     */
    private function parseFieldOneOption(string $fieldWithOptions): array
    {
        $regex = '/(?:([^.(),\n]+))\((\d+)\)/';

        preg_match($regex, $fieldWithOptions, $match);

        return [$match[1], $match[2]];
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
     * @param array $selectableFields
     * @param string $namespace
     * @return array|null
     */
    private function namespaceSubstitutes(array $selectableFields, string $namespace): ?array
    {
        if (empty($namespace)) {
            $subs = [];

            foreach ($selectableFields['sub'] ?? [] as $field => $structure) {
                if (isset($structure['substitute'])) {
                    $subs[$field] = $structure['substitute'];
                }
            }

            return $subs;
        }

        $namespaceArr = explode('.', $namespace);

        if (! empty($namespaceArr)) {

            $current = reset($namespaceArr);

            if ($current === '$') {
                unset($namespaceArr[0]);
                return $this->namespaceSubstitutes($selectableFields, implode('.', $namespaceArr));
            }

            if (isset($selectableFields['sub'][$current])) {
                unset($namespaceArr[0]);
                return $this->namespaceSubstitutes($selectableFields['sub'][$current], implode('.', $namespaceArr));
            }
        }

        return null;
    }

    /**
     * @param array $fieldStructures
     * @param string $namespace
     * @return array|null
     */
    private function namespaceDefaultFields(array $fieldStructures, string $namespace): ?array
    {
        if (empty($namespace)) {
            return $this->parseFields(implode(',', $fieldStructures['defaultFields'] ?? []));
        }

        $namespaceArr = explode('.', $namespace);

        if (! empty($namespaceArr)) {

            $current = reset($namespaceArr);

            if ($current === '$') {
                unset($namespaceArr[0]);
                return $this->namespaceDefaultFields($fieldStructures, implode('.', $namespaceArr));
            }

            if (isset($fieldStructures['sub'][$current])) {
                unset($namespaceArr[0]);
                return $this->namespaceDefaultFields($fieldStructures['sub'][$current], implode('.', $namespaceArr));
            }
        }

        return null;
    }

    /**
     * @param array $fieldStruct
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

                if ($fieldStruct['sub'][$current] === 'atomic') {
                    return ['type' => 'atomic', 'sub' => []];
                }

                return $this->namespaceFieldStructure($fieldStruct['sub'][$current], implode('.', $namespaceArr));
            }
        }

        return null;
    }

    /**
     * @param string $fieldName
     * @param string $namespace
     * @return bool
     */
    public function hasField(string $fieldName, string $namespace = '$'): bool
    {
        try {
            $namespaceFields = $this->flatFields($namespace, false);
        } catch (AlchemistRestfulApiException $e) {
            return false;
        }

        return in_array($fieldName, $namespaceFields);
    }
}