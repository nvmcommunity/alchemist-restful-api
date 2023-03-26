<?php

namespace Nvmcommunity\Alchemist\RestfulApi\FieldSelector\Handlers;

use Nvmcommunity\Alchemist\RestfulApi\Common\Helpers\Strings;
use Nvmcommunity\Alchemist\RestfulApi\FieldSelector\Notifications\FieldSelectorErrorBag;
use Nvmcommunity\Alchemist\RestfulApi\FieldSelector\Exceptions\FieldSelectorSyntaxErrorException;
use Nvmcommunity\Alchemist\RestfulApi\Common\Exceptions\AlchemistRestfulApiException;
use Nvmcommunity\Alchemist\RestfulApi\FieldSelector\Objects\FieldObject;

class FieldSelector
{
    /**
     * @var FieldObject[]
     */
    private array $fields;

    /**
     * @var string[]
     */
    private array $selectableFields = [];

    /**
     * @var FieldObject[]
     */
    private array $defaultFields = [];

    /**
     * @var string[][]
     */
    private array $selectableSubFields = [];

    /**
     * @param string $fields
     * @throws FieldSelectorSyntaxErrorException
     */
    public function __construct(string $fields)
    {
        $this->fields = $this->parseFields($fields);
    }

    /**
     * @param string[] $selectableFields
     * @return FieldSelector
     */
    public function defineSelectableFields(array $selectableFields): self
    {
        $this->selectableFields = $selectableFields;

        return $this;
    }

    /**
     * @param string[] $fields
     * @return FieldSelector
     * @throws FieldSelectorSyntaxErrorException
     */
    public function defineDefaultFields(array $fields): self
    {
        $this->defaultFields = $this->parseFields(implode(',', $fields));

        return $this;
    }

    /**
     * @param string $fieldName
     * @param string[] $selectableSubFields
     * @return FieldSelector
     * @throws AlchemistRestfulApiException
     */
    public function defineSelectableSubFields(string $fieldName, array $selectableSubFields): self
    {
        if (empty($this->selectableFields)) {
            throw new AlchemistRestfulApiException('You need to define the selectable fields first.');
        }

        if (! in_array($fieldName, $this->selectableFields, true)) {
            throw new AlchemistRestfulApiException(
                sprintf('The declared field (%s) is not in the selectable fields.', $fieldName)
            );
        }

        $this->selectableSubFields[$fieldName] = $selectableSubFields;

        return $this;
    }

    /**
     * @return FieldObject[]
     */
    public function fields(array $withoutFieldNames = []): array
    {
        if (! empty($withoutFieldNames)) {
            return $this->withoutFields($withoutFieldNames);
        }

        return $this->fields ?: $this->defaultFields;
    }

    /**
     * @return string[]
     */
    public function flatFields(array $withoutFieldNames = []): array
    {
        $fields = [];

        $withoutFieldsMap = array_flip($withoutFieldNames);

        foreach ($this->fields ?: $this->defaultFields as $field) {
            if (! array_key_exists($field->getName(), $withoutFieldsMap)) {
                $fields[] = $field;
            }
        }

        return array_map(static fn($field) => (string) $field, $fields);
    }

    /**
     * @return string[]
     */
    public function subFields(string $fieldName): array
    {
        if (! array_key_exists($fieldName, $this->fields)) {
            return [];
        }

        if ($this->fields) {
            return $this->fields[$fieldName]->getSubFields();
        }

        return $this->defaultFields[$fieldName]->getSubFields();
    }

    /**
     * @param string[] $withoutFieldNames
     * @return FieldObject[]
     */
    private function withoutFields(array $withoutFieldNames): array
    {
        $excludedFields = [];

        $withoutFieldNameMap = array_flip($withoutFieldNames);

        foreach ($this->fields ?: $this->defaultFields as $fieldName => $fieldObj) {
            if (! array_key_exists($fieldName, $withoutFieldNameMap)) {
                $excludedFields[$fieldName] = $fieldObj;
            }
        }

        return $excludedFields;
    }

    /**
     * @param $notification
     * @return FieldSelectorErrorBag
     */
    public function validate(&$notification = null): FieldSelectorErrorBag
    {
        /** @var string[] $unselectableFields */
        $unselectableFields = [];

        /** @var string[][] $unselectableSubFields */
        $unselectableSubFields = [];

        /** @var string[] $subsidiarySelectErrors */
        $subsidiarySelectErrors = [];

        if (empty($this->selectableFields)) {
            return $notification = new FieldSelectorErrorBag(true);
        }

        $selectableFieldMap = array_flip($this->selectableFields);

        $fields = $this->fields ?: $this->defaultFields;

        foreach ($fields as $fieldName => $fieldObj) {
            if (! array_key_exists($fieldName, $selectableFieldMap)) {
                $unselectableFields[] = $fieldName;

                continue;
            }

            $isSubFieldDefined = ! empty($this->selectableSubFields[$fieldName]);

            if ($isSubFieldDefined) {
                if (empty($fieldObj->getSubFields())) {
                    continue;
                }

                $selectableSubFieldMap = array_flip($this->selectableSubFields[$fieldName]);

                foreach ($fieldObj->getSubFields() as $subField) {
                    if (! array_key_exists($subField, $selectableSubFieldMap)) {
                        if (! array_key_exists($fieldName, $unselectableSubFields)) {
                            $unselectableSubFields[$fieldName] = [];
                        }

                        $unselectableSubFields[$fieldName][] = $subField;
                    }
                }
            } elseif (! empty($fieldObj->getSubFields())) {
                $subsidiarySelectErrors[] = $fieldObj->getName();
            }
        }

        if ($subsidiarySelectErrors) {
            return $notification = new FieldSelectorErrorBag(
                false, $unselectableFields, $unselectableSubFields, $subsidiarySelectErrors
            );
        }

        if ($unselectableSubFields) {
            return $notification = new FieldSelectorErrorBag(false, $unselectableFields, $unselectableSubFields);
        }

        if ($unselectableFields) {
            return $notification = new FieldSelectorErrorBag(false, $unselectableFields);
        }

        return $notification = new FieldSelectorErrorBag(true);
    }

    /**
     * @param string $field
     * @return FieldObject
     */
    private function parseOption(string $field): FieldObject
    {
        preg_match('/([\w\s]+)\.limit\((\d+)\){([\w,\s]+)?}/', $field, $matches);

        if (! empty($matches)) {
            $field = trim($matches[1]);
            $limit = (int) $matches[2];

            $subfields = [];

            if (isset($matches[3])) {
                foreach (explode(',', $matches[3]) as &$subfield) {
                    $subfield = trim($subfield);
                    if (! empty($subfield)) {

                        $subfields[] = $subfield;
                    }
                }
            }

            return new FieldObject($field, $limit, $subfields);
        }

        preg_match('/([\w\s]+){([\w,\s]+)?}/', $field, $matches);

        if (! empty($matches)) {

            $field = trim($matches[1]);

            $subfields = [];

            if (isset($matches[2])) {
                foreach (explode(',', $matches[2]) as &$subfield) {
                    $subfield = trim($subfield);
                    if (! empty($subfield)) {

                        $subfields[] = $subfield;
                    }
                }
            }

            return new FieldObject($field, 0, $subfields);
        }

        return new FieldObject($field);
    }

    /**
     * @param string $fields
     * @return FieldObject[]
     * @throws FieldSelectorSyntaxErrorException
     */
    private function parseFields(string $fields): array
    {
        $result = [];

        preg_match_all(
            '/(([\w\s]+)|(([\w\s]+)\.limit\(([\d\s]+)\){([\w,\s]+)?})|(([\w\s]+){([\w,\s]+)?}))(?:,|$)/',
            $fields,
            $matches
        );

        if (! (implode('', $matches[0]) === $fields)) {
            throw new FieldSelectorSyntaxErrorException();
        }

        foreach ($matches[1] as $field) {
            $field = trim($field);

            if (! empty($field)) {
                if (Strings::contains($field, '.')) {
                    $fieldName = Strings::start($field, '.');
                } else {
                    $fieldName = Strings::start($field, '{');
                }

                $result[$fieldName] = $this->parseOption($field);
            }
        }

        return $result;
    }
}