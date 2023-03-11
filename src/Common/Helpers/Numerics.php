<?php

namespace Nvmcommunity\Alchemist\RestfulApi\Common\Helpers;

class Numerics
{
    /**
     * @param $value
     * @return bool
     */
    public static function isIntegerValue($value): bool
    {
        if (is_string($value)) {
            return ctype_digit($value);
        }

        if (is_int($value)) {
            return true;
        }

        return false;
    }
}