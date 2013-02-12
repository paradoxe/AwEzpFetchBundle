<?php

/**
 * This file is part of AwEzpFetchBundle
 *
 * @author    Mohamed Karnichi <mka@amiralweb.com>
 * @copyright 2013 Amiral Web
 * @link      http://www.amiralweb.com
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Aw\Ezp\FetchBundle\Fetch\Utils;
class ArrayUtils
{
    const E_TYPE = 0;
    const E_MIN_LENGHT = -1;
    const E_MAX_LENGHT = -2;

    public static function isArray($value, $minLength = null, $maxLength = null)
    {
        $result = true;
        if (!is_array($value)) {
            $result = self::E_TYPE;
        } else {
            $length = count($value);
            if (($minLength !== null) && ($length < $minLength)) {
                $result = self::E_MIN_LENGHT;
            }
            if (($maxLength !== null) && ($length > $maxLength)) {
                $result = self::E_MAX_LENGHT;
            }
        }

        return $result;
    }

    public static function isIndexedArray($value, $minLength = null, $maxLength = null)
    {
        if ($result = static::isArray($value, $minLength, $maxLength)) {
            if (count(array_filter(array_keys($value), function ($v)
            {
                return !is_int($v);
            }))) {
                $result = self::E_TYPE;
            }
        }

        return $result;
    }

    public static function isMap($value, $minLength = null, $maxLength = null)
    {
        if ($result = static::isArray($value, $minLength, $maxLength)) {
            if (count(array_filter(array_keys($value), function ($v)
            {
                return !is_string($v);
            }))) {
                $result = self::E_TYPE;
            }
        }

        return $result;
    }

}
