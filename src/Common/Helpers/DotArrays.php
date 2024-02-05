<?php

namespace Nvmcommunity\Alchemist\RestfulApi\Common\Helpers;

class DotArrays
{
    /**
     * @param array $array
     * @param string $dotKey
     * @param mixed $value
     * @param bool $force
     *
     * @return void
     */
    public static function array_dot_set(array &$array, string $dotKey, $value, bool $force = false): void
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
    public static function array_dot_unset(array &$array, string $dotKey): bool
    {
        if (! self::array_dot_key_exists($array, $dotKey)) {
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
    public static function array_dot_key_exists(array $array, string $dotKey): bool
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
    public static function array_dot_value(array $array, string $dotKey)
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
     * @param bool $withSlashOnDotKey
     * @param bool $withEmptyArray
     *
     * @return array
     */
    public static function array_dot_flatten(
        array  $array,
        bool   $withSlashOnDotKey = true,
        bool   $withEmptyArray = true,
        array  $ignoreFlatKeys = []
    ): array
    {
        return self::array_dot_flatten_recursive($array, $withSlashOnDotKey, $withEmptyArray, $ignoreFlatKeys, '');
    }

    /**
     * @param array $flatten
     * @return array
     */
    public static function array_dot_unflatten(array $flatten): array
    {
        $unflatten = [];

        foreach ($flatten as $dotKey => $value) {
            self::array_dot_set($unflatten, $dotKey, $value);
        }

        return $unflatten;
    }

    #---------------------------------------------------------------------------#
    # Private Functions                                                         #
    #---------------------------------------------------------------------------#

    /**
     * @param array $array
     * @param bool $withSlashOnDotKey
     * @param bool $withEmptyArray
     *
     * @return array
     */
    private static function array_dot_flatten_recursive(
        array  $array,
        bool   $withSlashOnDotKey = true,
        bool   $withEmptyArray = true,
        array  $ignoreFlatKeys = [],
        string $parentDotKey = ''
    ): array
    {
        $flatten = [];

        foreach ($array as $key => $value) {
            if ($withSlashOnDotKey) {
                $key = str_replace('.', '\.', $key);
            }

            if (! is_array($value)) {
                $flatten[$key] = $value;

                continue;
            }

            if (empty($value)) {
                if ($withEmptyArray) {
                    $flatten[$key] = $value;
                }

                continue;
            }

            $path =  ($parentDotKey !== '') ? "{$parentDotKey}.{$key}" : $key;

            $hasMatch = false;

            foreach ($ignoreFlatKeys as $ignorePattern) {
                $ignoreRegexPattern = str_replace('.', '\.', $ignorePattern);
                $ignoreRegexPattern = str_replace('*', '\d+', $ignoreRegexPattern);

                if (preg_match("/{$ignoreRegexPattern}/", $path)) {
                    $nestedArray = $value;
                    $flatten["{$key}"] = $value;
                    $hasMatch = true;

                    break;
                }
            }

            if (! $hasMatch) {
                $nestedArray = DotArrays::array_dot_flatten_recursive($value, $withSlashOnDotKey, $withEmptyArray,  $ignoreFlatKeys, $path);

                foreach ($nestedArray as $nestedKey => $nestedValue) {
                    $flatten["{$key}.{$nestedKey}"] = $nestedValue;
                }
            }
        }

        return $flatten;
    }
}