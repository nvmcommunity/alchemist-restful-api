<?php

namespace Nvmcommunity\Alchemist\RestfulApi\Common\Helpers;

use RecursiveArrayIterator;
use RecursiveIteratorIterator;

class Arrays
{
    /**
     * @param array $array
     * @param $key
     * @param $value
     *
     * @return bool is first install?
     */
    public static function initFirst(array &$array, $key, $value): bool
    {
        $isFirstInstall = ! array_key_exists($key, $array);

        if ($isFirstInstall) {
            $array[$key] = $value;
        }

        return $isFirstInstall;
    }

    /**
     * @param array $array
     * @param string $key
     *
     * @return mixed
     */
    public static function pullValue(array $array, string $key): mixed
    {
        $value = $array[$key];

        unset($array[$key]);

        return $value;
    }

    /**
     * @param array $array1
     * @param array $array2
     *
     * @return bool
     */
    public static function isEqual(array $array1, array $array2): bool
    {
        return (
            count($array1) == count($array2)
            && array_diff($array1, $array2) === array_diff($array2, $array1)
        );
    }

    /**
     * @param array $array
     * @param array $values
     *
     * @return array
     */
    public static function removeValues(array $array, array $values): array
    {
        foreach ($array as $key => $value) {
            foreach ($values as $oneValue) {
                if ($value === $oneValue) {
                    unset($array[$key]);

                    break;
                }
            }
        }

        return $array;
    }

    /**
     * @param array $array
     *
     * @return array
     */
    public static function removeUndefined(array $array): array
    {
        return Arrays::removeValues($array, ['@UNDEFINED!']);
    }

    /**
     * @param array $array
     * @param array $sortBy
     *
     * @return array
     */
    public static function sort2D(array $array, array $sortBy): array
    {
        $mapSortDirections = [
            'desc' => SORT_DESC,
            'asc' => SORT_ASC,
        ];

        $sortParams = [];

        foreach ($sortBy as $sort => $direction) {
            $sortColumns = array_column($array, $sort);
            $direction = $mapSortDirections[strtolower($direction)];

            $sortParams[] = $sortColumns;
            $sortParams[] = $direction;
        }

        $sortParams[] = $array;

        array_multisort(...$sortParams);

        return $sortParams[array_key_last($sortParams)];
    }


    /**
     * @param array $array
     * @param string $dotKey
     * @param mixed $value
     * @param bool $force
     *
     * @return void
     */
    public static function dotSet(array &$array, string $dotKey, $value, bool $force = false): void
    {
        $matches = [];
        preg_match_all("/(?:(?:\\\\\\.)|(?!\.).|\s)+/", $dotKey, $matches);
        $keys = str_replace('\.', '.', $matches[0]);

        foreach ($keys as $key) {
            if ($force && ! is_array($array)) {
                $array = [];
            }

            $array = &$array[$key];
        }

        $array = $value;
    }

    /**
     * @param array $array
     * @param string $dotKey
     *
     * @return bool
     */
    public static function dotUnset(array &$array, string $dotKey): bool
    {
        if (! Arrays::dotKeyExists($array, $dotKey)) {
            return false;
        }

        if (array_key_exists($dotKey, $array)) {
            unset($array[$dotKey]);

            return true;
        }

        $matches = [];
        preg_match_all("/(?:(?:\\\\\\.)|(?!\.).|\s)+/", $dotKey, $matches);
        $keys = str_replace('\.', '.', $matches[0]);
        $lastKey = array_pop($keys);

        foreach ($keys as $key) {
            if (! isset($array[$key])) {
                continue;
            }

            $array = &$array[$key];
        }

        unset($array[$lastKey]);

        return true;
    }

    /**
     * @param array $array
     * @param string $dotKey
     *
     * @return bool
     */
    public static function dotKeyExists(array $array, string $dotKey): bool
    {
        $matches = [];
        preg_match_all("/(?:(?:\\\\\\.)|(?!\.).|\s)+/", $dotKey, $matches);
        $keys = str_replace('\.', '.', $matches[0]);

        foreach ($keys as $key) {
            if (! is_array($array) || ! array_key_exists($key, $array)) {
                return false;
            }

            $array = &$array[$key];
        }

        return true;
    }

    /**
     * @param array $array
     * @param string $dotKey
     *
     * @return mixed
     */
    public static function dotValue(array $array, string $dotKey)
    {
        $matches = [];
        preg_match_all("/(?:(?:\\\\\\.)|(?!\.).|\s)+/", $dotKey, $matches);
        $keys = str_replace('\.', '.', $matches[0]);

        foreach ($keys as $key) {
            if (! is_array($array) || ! array_key_exists($key, $array)) {
                return null;
            }

            $array = &$array[$key];
        }

        return $array;
    }

    /**
     * @param array $array
     * @param bool $slashOnDotKey
     *
     * @return array
     */
    public static function dotFlatten(array $array, bool $slashOnDotKey = true): array
    {
        $arrayRecursiveIterator = new RecursiveIteratorIterator(new RecursiveArrayIterator($array));

        $flatten = [];

        foreach ($arrayRecursiveIterator as $leafValue) {
            $keys = [];

            foreach (range(0, $arrayRecursiveIterator->getDepth()) as $depth) {
                $keys[] = $arrayRecursiveIterator->getSubIterator($depth)->key();
            }

            if ($slashOnDotKey) {
                $keys = str_replace('.', '\.', $keys);
            }

            $flatten[implode('.', $keys)] = $leafValue;
        }

        return $flatten;
    }

    /**
     * @param array $flatten
     * @return array
     */
    public static function dotUnflatten(array $flatten): array
    {
        $unflatten = [];

        foreach ($flatten as $dotKey => $value) {
            Arrays::dotSet($unflatten, $dotKey, $value);
        }

        return $unflatten;
    }
}