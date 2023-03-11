<?php

namespace Nvmcommunity\Alchemist\RestfulApi\ResourceFilter\Handlers;

use Closure;
use Nvmcommunity\Alchemist\RestfulApi\Common\Helpers\Arrays;
use Nvmcommunity\Alchemist\RestfulApi\ResourceFilter\Notifications\ResourceFilterValidationNotification;
use Nvmcommunity\Alchemist\RestfulApi\ResourceFilter\Exceptions\FilteringException;
use Nvmcommunity\Alchemist\RestfulApi\ResourceFilter\Exceptions\FilteringInvalidException;
use Nvmcommunity\Alchemist\RestfulApi\ResourceFilter\Exceptions\FilteringInvalidRuleException;
use Nvmcommunity\Alchemist\RestfulApi\ResourceFilter\Exceptions\FilteringInvalidRuleOperatorException;
use Nvmcommunity\Alchemist\RestfulApi\ResourceFilter\Exceptions\FilteringInvalidValueSyntaxException;
use Nvmcommunity\Alchemist\RestfulApi\ResourceFilter\Exceptions\FilteringInvalidValueTypeException;
use Nvmcommunity\Alchemist\RestfulApi\ResourceFilter\Exceptions\FilteringRequiredException;
use Nvmcommunity\Alchemist\RestfulApi\ResourceFilter\Exceptions\FilteringRequiredIfException;
use Nvmcommunity\Alchemist\RestfulApi\ResourceFilter\Exceptions\FilteringRequiredWithException;
use Nvmcommunity\Alchemist\RestfulApi\ResourceFilter\Exceptions\FilteringRuleAlreadyDefinedException;
use Nvmcommunity\Alchemist\RestfulApi\ResourceFilter\Objects\FilteringObject;
use Nvmcommunity\Alchemist\RestfulApi\ResourceFilter\Objects\FilteringOptions;
use Nvmcommunity\Alchemist\RestfulApi\ResourceFilter\Objects\FilteringRules;
use Nvmcommunity\Alchemist\RestfulApi\ResourceFilter\Objects\InvalidFilteringValue;

class ResourceFilter
{
    private const MAP_OPERATOR_BETWEEN_FILTER_VS_CONDITION = [
        'eq' => '=',
        'is' => '=',
        'ne' => '!=',
        'gte' => '>=',
        'lte' => '<=',
        'gt' => '>',
        'lt' => '<',
        'in' => 'in',
        'not_in' => 'not_in',
        'contains' => 'contains',
        'between' => 'between',
        'not_between' => 'not_between',
        'empty' => 'empty',
        'duplicate' => 'duplicate',
    ];

    private array $filtering;
    private array $filteringRules = [];
    private ?FilteringOptions $filteringOption = null;
    private array $mapFilteringRules = [];
    /**
     * @var array map[<dot_name>][<operator>] = <value>
     *              + dot_name = key1.key2.key3
     *              + operator = eq, lte, gte, ...
     */
    private array $filteringMap = [];

    /**
     * @param array $filtering
     */
    public function __construct(array $filtering)
    {
        $this->filtering = $filtering;
    }

    /**
     * @param FilteringRules[] $filteringRules
     *
     * @return ResourceFilter
     */
    public function defineFilteringRules(array $filteringRules): ResourceFilter
    {
        $this->initParams($filteringRules);

        $this->initRecursiveFiltering($this->filtering);

        return $this;
    }

    /**
     * @param FilteringOptions $filteringOption
     * @return $this
     */
    public function defineFilteringOptions(FilteringOptions $filteringOption): ResourceFilter
    {
        $this->filteringOption = $filteringOption;

        return $this;
    }

    #---------------------------------------------------------------------------#
    # Public Methods                                                            #
    #---------------------------------------------------------------------------#

    /**
     * @param string $filteringDotName
     * @param string|null $filteringOperator
     *
     * @return bool
     */
    public function hasFiltering(string $filteringDotName, ?string $filteringOperator = null): bool
    {
        if (empty($filteringOperator)) {
            return isset($this->filteringMap[$filteringDotName]);
        }

        if ($filteringOperator === 'eq' || $filteringOperator === 'is') {
            return isset($this->filteringMap[$filteringDotName]['eq'])
                || isset($this->filteringMap[$filteringDotName]['is']);
        }

        return isset($this->filteringMap[$filteringDotName][$filteringOperator]);
    }

    /**
     * @param string $filteringDotName
     * @param string $filteringOperator
     *
     * @return mixed
     *
     * @throws FilteringInvalidRuleException
     * @throws FilteringInvalidRuleOperatorException
     */
    private function pullFilteringValue(string $filteringDotName, string $filteringOperator)
    {
        $filteringRule = $this->getFilteringRuleByDotName($filteringDotName);

        if ($filteringRule === null) {
            throw new FilteringInvalidRuleException($filteringDotName);
        }

        if (! $filteringRule->hasOperator($filteringOperator)) {
            throw new FilteringInvalidRuleOperatorException($filteringDotName, $filteringOperator);
        }

        $filteringValue = $this->collectFilteringValueByDotName($filteringDotName, $filteringOperator);

        unset($this->filteringMap[$filteringDotName][$filteringOperator]);
        Arrays::dotUnset($this->filtering, "{$filteringDotName}:{$filteringOperator}");

        return $filteringValue;
    }

    /**
     * @param string $filteringDotName
     * @param string $filteringOperator
     * @param $filteringValue
     * @return ResourceFilter
     *
     * @throws FilteringInvalidRuleException
     */
    public function addFilteringIfNotExists(string $filteringDotName, string $filteringOperator, $filteringValue): ResourceFilter
    {
        if ($this->hasFiltering($filteringDotName, $filteringOperator)) {
            return $this;
        }

        Arrays::dotSet($this->filtering, "{$filteringDotName}:{$filteringOperator}", $filteringValue);

        $filteringRule = $this->getFilteringRuleByDotName($filteringDotName);

        if ($filteringRule === null) {
            throw new FilteringInvalidRuleException($filteringDotName);
        }

        Arrays::initFirst($this->filteringMap, $filteringDotName, []);

        $this->filteringMap[$filteringDotName][$filteringOperator] = $filteringValue;

        return $this;
    }

    /**
     * @return array
     */
    public function filtering(): array
    {
        $conditions = [];

        foreach ($this->filteringMap as $filteringDotName => $map) {
            $filteringRule = $this->getFilteringRuleByDotName($filteringDotName);

            foreach ($map as $filteringOperator => $filteringValue) {
                $conditionOperator = $this->getConditionOperatorByFilteringOperator($filteringOperator);

                switch ($filteringRule->getType()) {
                    case FilteringRules::TYPE_NUMBER:
                        $conditionValue = is_numeric($filteringValue) ? (float) $filteringValue : $filteringValue;
                        break;
                    case FilteringRules::TYPE_INTEGER:
                        $conditionValue = filter_var($filteringValue, FILTER_VALIDATE_INT) ?: $filteringValue;
                        break;
                    default:
                        $conditionValue = $filteringValue;
                        break;
                }

                // $conditionNameWithOperator = ($conditionOperator !== '=') ? "{$filteringDotName}#{$conditionOperator}" : $filteringDotName;

                $conditions[] = new FilteringObject($filteringDotName, $conditionOperator, $filteringValue);

                //Arrays::dotSet($conditions, $conditionNameWithOperator, $conditionValue);
            }
        }

        return $conditions;
    }

    /**
     * @param mixed $notification
     * @return ResourceFilterValidationNotification
     */
    public function validate(&$notification = null): ResourceFilterValidationNotification
    {
        $optionValidationNotification = $this->validateOption();

        if ($optionValidationNotification->passes()) {
            return $notification = $this->validateFiltering();
        }

        $validateFiltering = $this->validateFiltering();

        return $notification = new ResourceFilterValidationNotification(
            false, $optionValidationNotification->getMissingRequiredFiltering(),
            $validateFiltering->getInvalidFiltering(),
            $validateFiltering->getInvalidFilteringValue(),
        );
    }

    /**
     * @return FilteringOptions|null
     */
    private function getFilteringOption(): ?FilteringOptions
    {
        return $this->filteringOption;
    }

    #---------------------------------------------------------------------------#
    # Helper Methods                                                            #
    #---------------------------------------------------------------------------#

    /**
     * @param array $filteringRules
     * @return void
     *
     * @throws FilteringRuleAlreadyDefinedException
     */
    private function initParams(array $filteringRules): void
    {
        $this->filteringRules = $filteringRules;

        $this->initFilteringRules($this->filteringRules);
    }

    /**
     * @param array $filteringRules
     * @return void
     *
     * @throws FilteringRuleAlreadyDefinedException
     */
    private function mergeParams(array $filteringRules): void
    {
        $this->filteringRules = array_merge_recursive($this->filteringRules, $filteringRules);

        $this->initFilteringRules($this->filteringRules);
    }

    /**
     * @param FilteringRules[] $filteringRules
     * @return void
     */
    private function initFilteringRules(array $filteringRules): void
    {
        $this->mapFilteringRules = [
            'name' => [],
            'dot_name' => [],
        ];

        foreach ($filteringRules as $filteringRule) {
            if (isset($this->mapFilteringRules['name'][$filteringRule->getName()])) {
                throw new FilteringRuleAlreadyDefinedException($filteringRule->getName());
            }

            $this->mapFilteringRules['name'][$filteringRule->getName()] = $filteringRule;
        }

        $this->initRecursiveFilteringRules($filteringRules);
    }

    /**
     * @param FilteringRules[] $filteringRules
     * @param string $filteringCollectionName
     *
     * @return void
     */
    private function initRecursiveFilteringRules(array $filteringRules, string $filteringCollectionName = ''): void
    {
        foreach ($filteringRules as $filteringRule) {
            $filteringKey = ! empty($filteringCollectionName) ? "{$filteringCollectionName}.{$filteringRule->getName()}" : $filteringRule->getName();

            if (isset($this->mapFilteringRules['dot_name'][$filteringKey])) {
                throw new FilteringRuleAlreadyDefinedException($filteringKey);
            }

            if ($filteringRule->getType() === FilteringRules::TYPE_GROUP) {
                $this->initRecursiveFilteringRules($filteringRule->getItems(), $filteringKey);

                continue;
            }

            $this->mapFilteringRules['dot_name'][$filteringKey] = $filteringRule;
        }
    }

    /**
     * @param array $filtering
     * @param FilteringRules|null $filteringGroup
     * @param string $filteringGroupName
     * @return void
     */
    private function initRecursiveFiltering(array $filtering, ?FilteringRules $filteringGroup = null, string $filteringGroupName = ''): void
    {
        foreach ($filtering as $filteringNameWithOperator => $filteringValue) {
            [$filteringName, $filteringOperator] = $this->extractFilteringNameAndOperator($filteringNameWithOperator);
            $filteringName = addcslashes($filteringName, '.');

            $filteringKey = ! empty($filteringGroupName) ? "{$filteringGroupName}.{$filteringName}" : $filteringName;

            $filteringRule = $filteringGroup
                ? $filteringGroup->collectItemByName($filteringName)
                : $this->getFilteringRuleByName($filteringName);

            if (! $filteringRule) {
                continue;
            }

            if ($filteringRule->getType() === FilteringRules::TYPE_GROUP) {
                if (! is_array($filteringValue)) {
                    continue;
                }

                $this->initRecursiveFiltering($filteringValue, $filteringRule, $filteringKey);

                continue;
            }

            Arrays::initFirst($this->filteringMap, $filteringKey, []);

            $this->filteringMap[$filteringKey][$filteringOperator] = $filteringValue;
        }
    }

    /**
     * @param string $filteringNameWithOperator
     * @return array
     */
    private function extractFilteringNameAndOperator(string $filteringNameWithOperator): array
    {
        $extract = explode(':', $filteringNameWithOperator);

        return [$extract[0], $extract[1] ?? 'eq'];
    }

    /**
     * @param string $filteringOperator
     * @return string|null
     */
    private function getConditionOperatorByFilteringOperator(string $filteringOperator): ?string
    {
        return self::MAP_OPERATOR_BETWEEN_FILTER_VS_CONDITION[$filteringOperator] ?? null;
    }

    /**
     * @param string $filteringName
     * @return FilteringRules|null
     */
    private function getFilteringRuleByName(string $filteringName): ?FilteringRules
    {
        return $this->mapFilteringRules['name'][$filteringName] ?? null;
    }

    /**
     * @param string $filteringDotName
     * @return FilteringRules|null
     */
    private function getFilteringRuleByDotName(string $filteringDotName): ?FilteringRules
    {
        return $this->mapFilteringRules['dot_name'][$filteringDotName] ?? null;
    }

    /**
     * @param string $filteringDotName
     * @return array
     */
    private function collectFilteringOperatorWithValues(string $filteringDotName): array
    {
        return $this->filteringMap[$filteringDotName] ?? [];
    }

    /**
     * @param string $filteringDotName
     * @param string $filteringOperator
     * @return mixed
     */
    private function collectFilteringValueByDotName(string $filteringDotName, string $filteringOperator)
    {
        return $this->filteringMap[$filteringDotName][$filteringOperator] ?? null;
    }

    /**
     * @return ResourceFilterValidationNotification
     */
    private function validateFiltering(): ResourceFilterValidationNotification
    {
        return $this->validateRecursiveFiltering($this->filtering);
    }

    /**
     * @return ResourceFilterValidationNotification
     */
    private function validateOption(): ResourceFilterValidationNotification
    {
        $option = $this->getFilteringOption();

        if ($option && ! empty($validateOptionRequired = $this->validateOptionRequired())) {
            return new ResourceFilterValidationNotification(false, $validateOptionRequired);
        }

        return new ResourceFilterValidationNotification(true);

    }

    /**
     * @param string $filteringTitle
     * @param FilteringRules $filteringRule
     * @param string $filteringOperator
     * @param $filteringValue
     * @param Closure $callbackValidateFilteringType
     * @return void
     * @throws FilteringInvalidValueTypeException
     */
    private function validateFilteringRuleOperator(string $filteringTitle, FilteringRules $filteringRule, string $filteringOperator, $filteringValue, Closure $callbackValidateFilteringType): void
    {
        ([
            'eq'            => fn() => $this->validateStringValue($filteringTitle, $filteringValue),
            'ne'            => fn() => $this->validateStringValue($filteringTitle, $filteringValue),
            'gte'           => fn() => $this->validateStringValue($filteringTitle, $filteringValue),
            'lte'           => fn() => $this->validateStringValue($filteringTitle, $filteringValue),
            'gt'            => fn() => $this->validateStringValue($filteringTitle, $filteringValue),
            'lt'            => fn() => $this->validateStringValue($filteringTitle, $filteringValue),
            'contains'      => fn() => $this->validateStringValue($filteringTitle, $filteringValue),
            'in'            => fn() => $this->validateArrayValue($filteringTitle, $filteringValue),
            'not_in'        => fn() => $this->validateArrayValue($filteringTitle, $filteringValue),
            'between'       => fn() => $this->validateBetweenValue($filteringTitle, $filteringValue),
            'not_between'   => fn() => $this->validateBetweenValue($filteringTitle, $filteringValue),
            'is'            => fn() => $this->validateBooleanValue($filteringTitle, $filteringValue),
            'empty'         => fn() => $this->validateBooleanValue($filteringTitle, $filteringValue),
            'duplicate'     => fn() => $this->validateBooleanValue($filteringTitle, $filteringValue),
        ][$filteringOperator])();

        if (is_array($filteringValue)) {
            foreach ($filteringValue as $index => $oneFilteringValue) {
                $oneFilteringTitle = "{$filteringTitle}[{$index}]";

                $callbackValidateFilteringType($oneFilteringTitle, $oneFilteringValue);
            }
        } else {
            $callbackValidateFilteringType($filteringTitle, $filteringValue);
        }
    }

    /**
     * @param string $filteringTitle
     * @param $filteringValue
     * @return void
     * @throws FilteringInvalidValueTypeException
     */
    private function validateBooleanValue(string $filteringTitle, $filteringValue): void
    {
        if (! $this->isValidBoolValue($filteringValue)) {
            throw new FilteringInvalidValueTypeException($filteringTitle, [0, 1]);
        }
    }

    /**
     * @param string $filteringTitle
     * @param $filteringValue
     * @return void
     * @throws FilteringInvalidValueSyntaxException
     */
    private function validateBetweenValue(string $filteringTitle, $filteringValue): void
    {
        if (! $this->isValidBetweenValue($filteringValue)) {
            throw new FilteringInvalidValueSyntaxException($filteringTitle, 'array of range two value', '[0, 1]');
        }
    }

    /**
     * @param string $filteringTitle
     * @param $filteringValue
     * @return void
     * @throws FilteringInvalidValueTypeException
     */
    private function validateArrayValue(string $filteringTitle, $filteringValue): void
    {
        if (! is_array($filteringValue)) {
            throw new FilteringInvalidValueTypeException($filteringTitle, ['array']);
        }
    }

    /**
     * @param string $filteringTitle
     * @param $filteringValue
     * @return void
     * @throws FilteringInvalidValueTypeException
     */
    private function validateStringValue(string $filteringTitle, $filteringValue): void
    {
        if (! $this->isValidStringValue($filteringValue)) {
            throw new FilteringInvalidValueTypeException($filteringTitle, ['string']);
        }
    }

    /**
     * @param string $filteringTitle
     * @param FilteringRules $filteringRule
     * @param string $filteringOperator
     * @param $filteringValue
     * @return InvalidFilteringValue|null
     */
    private function validateStringTypeValue(string $filteringTitle, FilteringRules $filteringRule, string $filteringOperator, $filteringValue): ?InvalidFilteringValue
    {
        $validate = null;

        try {
            $this->validateFilteringRuleOperator($filteringTitle, $filteringRule, $filteringOperator, $filteringValue,
                function ($filteringTitle, $filteringValue) use (&$validate) {
                    if (!$this->isValidStringValue($filteringValue)) {
                        $validate = new InvalidFilteringValue($filteringTitle, ['string']);
                    }
                }
            );
        } catch (FilteringInvalidValueTypeException $e) {
            $validate = new InvalidFilteringValue($filteringTitle, $e->getValidValueTypes());
        }

        return $validate;
    }

    /**
     * @param string $filteringTitle
     * @param FilteringRules $filteringRule
     * @param string $filteringOperator
     * @param mixed $filteringValue
     * @return InvalidFilteringValue|null
     * @throws FilteringException
     */
    private function validateGroupTypeValue(string $filteringTitle, FilteringRules $filteringRule, string $filteringOperator, $filteringValue): ?InvalidFilteringValue
    {
        if ($filteringOperator !== 'eq' && $filteringRule->getType() === FilteringRules::TYPE_GROUP) {
            throw new FilteringException('Filtering Rule group just support only operator: eq');
        }

        if (! $this->isValidGroupValue($filteringValue)) {
            return new InvalidFilteringValue($filteringTitle, ['array']);
        }

        $this->validateRecursiveFiltering($filteringValue, $filteringRule, $filteringTitle);

        return null;
    }

    /**
     * @param string $filteringTitle
     * @param FilteringRules $filteringRule
     * @param string $filteringOperator
     * @param $filteringValue
     * @return InvalidFilteringValue|null
     */
    private function validateIntegerTypeValue(string $filteringTitle, FilteringRules $filteringRule, string $filteringOperator, $filteringValue): ?InvalidFilteringValue
    {
        $validate = null;

        try {
            $this->validateFilteringRuleOperator($filteringTitle, $filteringRule, $filteringOperator, $filteringValue,
                function ($filteringTitle, $filteringValue) use (&$validate) {
                    if (!$this->isValidIntegerValue($filteringValue)) {
                        $validate = new InvalidFilteringValue($filteringTitle, ['integer']);
                    }
                }
            );
        } catch (FilteringInvalidValueTypeException $e) {
            $validate = new InvalidFilteringValue($filteringTitle, $e->getValidValueTypes());
        }

        return $validate;
    }

    /**
     * @param string $filteringTitle
     * @param FilteringRules $filteringRule
     * @param string $filteringOperator
     * @param $filteringValue
     * @return InvalidFilteringValue|null
     */
    private function validateNumberTypeValue(string $filteringTitle, FilteringRules $filteringRule, string $filteringOperator, $filteringValue): ?InvalidFilteringValue
    {
        $validate = null;

        try {
            $this->validateFilteringRuleOperator($filteringTitle, $filteringRule, $filteringOperator, $filteringValue,
                function ($filteringTitle, $filteringValue) use (&$validate) {
                    if (!$this->isValidNumberValue($filteringValue)) {
                        $validate = new InvalidFilteringValue($filteringTitle, ['integer', 'float']);
                    }
                }
            );
        } catch (FilteringInvalidValueTypeException $e) {
            $validate = new InvalidFilteringValue($filteringTitle, $e->getValidValueTypes());
        }

        return $validate;
    }

    /**
     * @param string $filteringTitle
     * @param FilteringRules $filteringRule
     * @param string $filteringOperator
     * @param $filteringValue
     * @return InvalidFilteringValue|null
     */
    private function validateEnumTypeValue(string $filteringTitle, FilteringRules $filteringRule, string $filteringOperator, $filteringValue): ?InvalidFilteringValue
    {
        $validate = null;

        try {
            $this->validateFilteringRuleOperator($filteringTitle, $filteringRule, $filteringOperator, $filteringValue,
                function ($filteringTitle, $filteringValue) use ($filteringRule, &$validate) {
                    if (!$this->isValidEnumValue($filteringValue)) {
                        $validate = new InvalidFilteringValue($filteringTitle, ['string', 'int']);
                    }

                    if (!$filteringRule->hasEnum($filteringValue)) {
                        $validate = new InvalidFilteringValue($filteringTitle, ['string', 'int'], $filteringRule->getEnums());
                    }
                }
            );
        } catch (FilteringInvalidValueTypeException $e) {
            $validate = new InvalidFilteringValue($filteringTitle, $e->getValidValueTypes());
        }

        return $validate;
    }

    /**
     * @param string $filteringTitle
     * @param FilteringRules $filteringRule
     * @param string $filteringOperator
     * @param $filteringValue
     * @return InvalidFilteringValue|null
     */
    private function validateDateTypeValue(string $filteringTitle, FilteringRules $filteringRule, string $filteringOperator, $filteringValue): ?InvalidFilteringValue
    {
        $validate = null;

        try {
            $this->validateFilteringRuleOperator($filteringTitle, $filteringRule, $filteringOperator, $filteringValue,
                function ($filteringTitle, $filteringValue) use ($filteringRule, &$validate) {
                    if (!$this->isValidDateValue($filteringRule, $filteringValue)) {
                        $validate = new InvalidFilteringValue($filteringTitle, ['string'], null, $filteringRule->getFormats());
                    }
                }
            );
        } catch (FilteringInvalidValueTypeException $e) {
            $validate = new InvalidFilteringValue($filteringTitle, $e->getValidValueTypes());
        }

        return $validate;
    }

    /**
     * @param string $filteringTitle
     * @param FilteringRules $filteringRule
     * @param string $filteringOperator
     * @param $filteringValue
     * @return InvalidFilteringValue|null
     */
    private function validateDatetimeTypeValue(string $filteringTitle, FilteringRules $filteringRule, string $filteringOperator, $filteringValue): ?InvalidFilteringValue
    {
        $validate = null;

        try {
            $this->validateFilteringRuleOperator($filteringTitle, $filteringRule, $filteringOperator, $filteringValue,
                function ($filteringTitle, $filteringValue) use ($filteringRule, &$validate) {
                    if (!$this->isValidDatetimeValue($filteringRule, $filteringValue)) {
                        $validate = new InvalidFilteringValue($filteringTitle, ['string'], null, $filteringRule->getFormats());
                    }
                }
            );
        } catch (FilteringInvalidValueTypeException $e) {
            $validate = new InvalidFilteringValue($filteringTitle, $e->getValidValueTypes());
        }

        return $validate;
    }

    /**
     * @param $filteringValue
     * @return bool
     */
    private function isValidGroupValue($filteringValue): bool
    {
        return is_array($filteringValue);
    }

    /**
     * @param $filteringValue
     * @return bool
     */
    private function isValidStringValue($filteringValue): bool
    {
        return ! is_array($filteringValue);
    }

    /**
     * @param $value
     * @return bool
     */
    private function isValidBoolValue($value): bool
    {
        return (is_string($value) || is_int($value)) && in_array((string) $value, ['0', '1']);
    }

    /**
     * @param $filteringValue
     * @return bool
     */
    private function isValidBetweenValue($filteringValue): bool
    {
        return is_array($filteringValue) && array_key_exists(0, $filteringValue) && array_key_exists(1, $filteringValue) && (count($filteringValue) === 2);
    }

    /**
     * @param $filteringValue
     * @return bool
     */
    private function isValidIntegerValue($filteringValue): bool
    {
        return ((is_string($filteringValue) && ctype_digit((string)$filteringValue)) || is_int($filteringValue));
    }

    /**
     * @param $filteringValue
     * @return bool
     */
    private function isValidNumberValue($filteringValue): bool
    {
        return ((is_string($filteringValue) && ctype_digit((string)$filteringValue)) || is_int($filteringValue) || is_numeric($filteringValue));
    }

    /**
     * @param $filteringValue
     * @return bool
     */
    private function isValidEnumValue($filteringValue): bool
    {
        return is_string($filteringValue) || is_int($filteringValue);
    }

    /**
     * @param FilteringRules $filteringRule
     * @param $filteringValue
     * @return bool
     */
    private function isValidDateValue(FilteringRules $filteringRule, $filteringValue): bool
    {
        foreach ($filteringRule->getFormats() as $format) {
            $datetime = date($format, strtotime($filteringValue));

            if ($datetime === $filteringValue) {
                return true;
            }
        }

        return false;
    }

    /**
     * @param FilteringRules $filteringRule
     * @param $filteringValue
     * @return bool
     */
    private function isValidDatetimeValue(FilteringRules $filteringRule, $filteringValue): bool
    {
        foreach ($filteringRule->getFormats() as $format) {
            $datetime = date($format, strtotime($filteringValue));

            if ($datetime === $filteringValue) {
                return true;
            }
        }

        return false;
    }

    /**
     * @param array $filtering
     * @param FilteringRules|null $filteringRuleGroup
     * @param string|null $filteringGroupTitle
     * @return ResourceFilterValidationNotification
     */
    private function validateRecursiveFiltering(array $filtering, ?FilteringRules $filteringRuleGroup = null, ?string $filteringGroupTitle = null): ResourceFilterValidationNotification
    {
        $filteringErrors = [];

        foreach ($filtering as $filteringNameWithOperator => $filteringValue) {
            $filteringTitle = $filteringGroupTitle ? "{$filteringGroupTitle}[{$filteringNameWithOperator}]" : $filteringNameWithOperator;

            [$filteringName, $filteringOperator] = $this->extractFilteringNameAndOperator($filteringNameWithOperator);

            $filteringName = addcslashes($filteringName, '.');

            $filteringRule = $filteringRuleGroup ? $filteringRuleGroup->collectItemByName($filteringName) : $this->getFilteringRuleByName($filteringName);
            $conditionOperator = $this->getConditionOperatorByFilteringOperator($filteringOperator);

            if ($filteringRule === null
                || $conditionOperator === null
                || ! $filteringRule->hasOperator($filteringOperator)
            ) {
                Arrays::initFirst($filteringErrors, 'invalid_filtering', []);

                $filteringErrors['invalid_filtering'][] = $filteringTitle;

                continue;
            }

            $supportedFilteringValue = null;

            $ruleHandlerMap = [
                FilteringRules::TYPE_GROUP => fn() => $this->validateGroupTypeValue($filteringTitle, $filteringRule, $filteringOperator, $filteringValue),
                FilteringRules::TYPE_STRING => fn() => $this->validateStringTypeValue($filteringTitle, $filteringRule, $filteringOperator, $filteringValue),
                FilteringRules::TYPE_ENUM => fn() => $this->validateEnumTypeValue($filteringTitle, $filteringRule, $filteringOperator, $filteringValue),
                FilteringRules::TYPE_DATE => fn() => $this->validateDateTypeValue($filteringTitle, $filteringRule, $filteringOperator, $filteringValue),
                FilteringRules::TYPE_DATETIME => fn() => $this->validateDatetimeTypeValue($filteringTitle, $filteringRule, $filteringOperator, $filteringValue),
                FilteringRules::TYPE_INTEGER => fn() => $this->validateIntegerTypeValue($filteringTitle, $filteringRule, $filteringOperator, $filteringValue),
                FilteringRules::TYPE_NUMBER => fn() => $this->validateNumberTypeValue($filteringTitle, $filteringRule, $filteringOperator, $filteringValue),
            ];

            $supportedFilteringValue = ($ruleHandlerMap[$filteringRule->getType()])();

            if (! empty($supportedFilteringValue)) {
                Arrays::initFirst($filteringErrors, 'invalid_filtering_value', []);

                $filteringErrors['invalid_filtering_value'][] = $supportedFilteringValue;
            }
        }

        if (! empty($filteringErrors)) {
            return new ResourceFilterValidationNotification(false, [], $filteringErrors['invalid_filtering'] ?? [], $filteringErrors['invalid_filtering_value'] ?? []);
        }

        return new ResourceFilterValidationNotification(true);
    }

    /**
     * @param $filteringValue
     * @param $compareFilteringValue
     * @return bool
     */
    private function isEqualFilteringValue($filteringValue, $compareFilteringValue): bool
    {
        if (is_array($filteringValue)) {
            return is_array($compareFilteringValue) && Arrays::isEqual($filteringValue, $compareFilteringValue);
        }

        return ((string) $filteringValue === (string) $compareFilteringValue);
    }

    /**
     * @return array
     * @throws FilteringException
     */
    private function validateOptionRequired(): array
    {
        $option = $this->getFilteringOption();

        if (! $option) {
            return [];
        }

        $missingRequiredFiltering = [];

        foreach ($option->getRequired() as $filteringNameWithOperator) {
            [$filteringDotName, $filteringOperator] = $this->extractFilteringNameAndOperator($filteringNameWithOperator);
            $withAnyOperator = ($filteringOperator === 'any');

            $filteringRule = $this->getFilteringRuleByDotName($filteringDotName);

            if ($filteringRule === null) {
                throw new FilteringException("Can not found filtering rule from defined filtering {$filteringDotName}");
            }

            if (! $withAnyOperator && ! $filteringRule->hasOperator($filteringOperator)) {
                throw new FilteringException("Can not found filtering rule with operator from required filtering {$filteringNameWithOperator}");
            }

            if (! $this->hasFiltering($filteringDotName, ! $withAnyOperator ? $filteringOperator : null)) {
                $missingRequiredFiltering[] = $filteringNameWithOperator;
            }
        }

        return $missingRequiredFiltering;
    }

    /**
     * @return void
     */
    private function validateOptionRequiredIf(): void
    {
        $option = $this->getFilteringOption();

        if (! $option) {
            return;
        }

        foreach ($option->getRequiredIf() as $requiredFiltering => $ifFilteringConditions) {
            [$filteringDotName, $filteringOperator] = $this->extractFilteringNameAndOperator($requiredFiltering);
            $withAnyOperator = ($filteringOperator === 'any');

            if ($this->hasFiltering($filteringDotName, ! $withAnyOperator ? $filteringOperator : null)) {
                continue;
            }

            $filteringRule = $this->getFilteringRuleByDotName($filteringDotName);

            if ($filteringRule === null) {
                throw new FilteringException("Can not found filtering rule from  from required_if filtering {$filteringDotName}");
            }

            if (! $withAnyOperator && ! $filteringRule->hasOperator($filteringOperator)) {
                throw new FilteringException("Can not found filtering rule with operator from required_if filtering {$requiredFiltering}");
            }

            foreach ($ifFilteringConditions as $ifFilteringNameWithOperator => $ifFilteringCondition) {
                [$ifFilteringDotName, $ifFilteringOperator] = $this->extractFilteringNameAndOperator($ifFilteringNameWithOperator);
                $ifAnyOperator = ($ifFilteringOperator === 'any');

                $filteringRule = $this->getFilteringRuleByDotName($ifFilteringDotName);

                if ($filteringRule === null) {
                    throw new FilteringException("Can not found filtering rule from required_if filtering {$ifFilteringDotName}");
                }

                if (! $ifAnyOperator && ! $filteringRule->hasOperator($ifFilteringOperator)) {
                    throw new FilteringException("Can not found filtering rule with operator from required_if filtering {$ifFilteringNameWithOperator}");
                }

                if (
                    $this->hasFiltering($ifFilteringDotName, ! $ifAnyOperator ? $ifFilteringOperator : null)
                ) {
                    if ($ifFilteringOperator === 'any') {
                        $filteringOperatorWithValues = $this->collectFilteringOperatorWithValues($ifFilteringDotName);

                            foreach ($filteringOperatorWithValues as $ifOneOperator => $ifOneValue) {
                            if ($this->isEqualFilteringValue($ifFilteringCondition, $ifOneValue)) {
                                throw new FilteringRequiredIfException($requiredFiltering, $ifFilteringNameWithOperator, is_array($ifFilteringCondition)
                                    ? '[' . implode(',', $ifFilteringCondition) . ']'
                                    : $ifFilteringCondition
                                );
                            }
                        }
                    } elseif ($this->isEqualFilteringValue($ifFilteringCondition, $this->collectFilteringValueByDotName($ifFilteringDotName, $ifFilteringOperator))) {
                        throw new FilteringRequiredIfException($requiredFiltering, $ifFilteringNameWithOperator, is_array($ifFilteringCondition)
                            ? '[' . implode(',', $ifFilteringCondition) . ']'
                            : $ifFilteringCondition
                        );
                    }
                }
            }
        }
    }

    /**
     * @return void
     */
    private function validateOptionRequiredWith(): void
    {
        $option = $this->getFilteringOption();

        if (! $option) {
            return;
        }

        foreach ($option->getRequiredWith() as $requiredFiltering => $withPresentFilterings) {
            [$filteringDotName, $filteringOperator] = $this->extractFilteringNameAndOperator($requiredFiltering);
            $withAnyOperator = ($filteringOperator === 'any');

            if ($this->hasFiltering($filteringDotName, ! $withAnyOperator ? $filteringOperator : null)) {
                continue;
            }

            $filteringRule = $this->getFilteringRuleByDotName($filteringDotName);

            if ($filteringRule === null) {
                throw new FilteringException("Can not found filtering rule from require_with filtering {$filteringDotName}");
            }

            if (! $withAnyOperator && ! $filteringRule->hasOperator($filteringOperator)) {
                throw new FilteringException("Can not found filtering rule with operator from require_with {$requiredFiltering}");
            }

            foreach ($withPresentFilterings as $withPresentFiltering) {
                [$withFilteringDotName, $withFilteringOperator] = $this->extractFilteringNameAndOperator($withPresentFiltering);
                $withAnyOperator = ($withFilteringOperator === 'any');

                $filteringRule = $this->getFilteringRuleByDotName($withFilteringDotName);

                if ($filteringRule === null) {
                    throw new FilteringException("Can not found filtering rule from require_with filtering {$withFilteringDotName}");
                }

                if (! $withAnyOperator && ! $filteringRule->hasOperator($withFilteringOperator)) {
                    throw new FilteringException("Can not found filtering rule with operator from require_with {$withPresentFiltering}");
                }

                if ($this->hasFiltering($withFilteringDotName, ! $withAnyOperator ? $withFilteringOperator : null)) {
                    throw new FilteringRequiredWithException($requiredFiltering, $withPresentFiltering);
                }
            }
        }
    }
}