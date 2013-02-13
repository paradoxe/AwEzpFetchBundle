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

use Aw\Ezp\FetchBundle\Fetch\Utils\ArrayUtils;
use Aw\Ezp\FetchBundle\Fetch\Exception\InvalidArgumentException;
use eZ\Publish\API\Repository\Values\Content\Query\Criterion;
use eZ\Publish\API\Repository\Values\Content\Query\Criterion\Operator as Op;
use eZ\Publish\API\Repository\Values\Content\Query\Criterion as Cr;

class CriterionUtils
{

    protected static $logicalFactors = array('AND', 'NAND', 'OR', 'NOR');
    protected static $compOperators = array('EQ', 'NE', 'GT', 'GTE', 'LT', 'LTE', 'LIKE', 'UNLIKE');
    protected static $rangeOperators = array('BETWEEN', 'OUTSIDE');
    protected static $enumOperators = array('IN', 'NIN');
    protected static $negativeOpertorsMap = array('EQ' => 'NE', 'IN' => 'NIN', 'LIKE' => 'UNLIKE', 'BETWEEN' => 'OUTSIDE');
    protected static $apiOperatorsMap = array('EQ' => Op::EQ, 'GT' => Op::GT, 'GTE' => Op::GTE, 'LT' => Op::LT, 'LTE' => Op::LTE, 'IN' => Op::IN,
            'BETWEEN' => Op::BETWEEN, 'LIKE' => OP::LIKE);

    protected static $matchOperators;

    public function __construct()
    {
        self::$matchOperators = array_merge(self::$compOperators, self::$enumOperators, self::$rangeOperators);
    }

    public static function getIdentifierInfos($identifier)
    {
        $infos = explode('.', $identifier, 2);
        return array($infos[0], isset($infos[1]) ? $infos[1] : null);
    }

    public static function getCriterionClassName($identifier)
    {
        $className = implode('', array_map('ucwords', explode('_', $identifier)));
        return 'eZ\\Publish\\API\\Repository\\Values\\Content\\Query\\Criterion\\' . $className;
    }

    public static function normalizeOperand($operand, $identifier, $target, $apiOperator)
    {
        switch ($identifier) {
        case 'visibility':
            $operand = self::getApiVisibility($operand);
        default:
            break;
        }

        return $operand;
    }

    protected static function getApiVisibility($visibility)
    {
        if (is_bool($visibility)) {
            $visibility = $visibility ? Cr\Visibility::VISIBLE : Cr\Visibility::HIDDEN;
        }

        return $visibility;
    }

    public static function getApiOperator($operator)
    {
        if (self::isNegativeOperator($operator)) {
            $operator = array_search($operator, self::$negativeOpertorsMap);
        }

        return self::$apiOperatorsMap[$operator];
    }

    public static function getInternalOperator($apiOperator, $isNegative = false)
    {
        $operator = array_serach($apiOperator, self::$apiOperatorsMap);

        if ($isNegative) {
            $operator = self::$negativeOpertorsMap[$operator];
        }

       return $operator;
    }

    public static function assertIsCriterionIdentifier($identifier, array $path = array())
    {
        if (!self::isValidCriterionIdentifier($identifier)) {
            throw new InvalidArgumentException('Invalid Criterion Identifier', $identifier,'A valid criterion identifier', $path);
        }
    }

    public static function assertIsLogicalFactor($factor, array $path = array())
    {
        if (!self::isLogicalFactor($factor)) {
            throw new InvalidArgumentException('Invalid Logical Factor', $factor, self::$logicalFactors, $path);
        }
    }

    public static function assertIsMatchOperator($operator, array $path = array())
    {
        if (!self::isValidMatchOperator($operator)) {
            throw new InvalidArgumentException('Invalid Match operator', $operator, self::$matchOperators, $path);
        }
    }

    public static function assertIsValidMatchOperandType($operator, $operand, array $path = array())
    {
        // ENUM
        if (self::isEnumOperator($operator)) {

            if (ArrayUtils::isIndexedArray($operand, 1) !== true) {
                throw new InvalidArgumentException('Invalid Enumeration match operand Type', $operand,
                        'Sequence (indexed array) with at least one scalar element', $path);
            }

            // RANGE
        } elseif (self::isRangeOperator($operator)) {

            if (ArrayUtils::isIndexedArray($operand, 2, 2) !== true) {
                throw new InvalidArgumentException('Invalid Range match operand Type', $operand,
                        'List (indexed array) containing 2 scalar elements [start, end]', $path);
            }

            list($start, $end) = $operand;

            if (!is_scalar($start) || !is_scalar($end)) {

                $invalidOperand = is_scalar($start) ? $end : $start;

                throw new InvalidArgumentException('Invalid Range border Operand type', $invalidOperand, 'Scalar', $path);
            }

        } else {

            // COMPARE
            if (!is_scalar($operand)) {
                throw new InvalidArgumentException('Invalid Match Operand type', $operand, 'Scalar', $path);
            }
        }
    }

    public static function isValidCriterionIdentifier($identifier)
    {
        return class_exists(self::getCriterionClassName($identifier));
    }

    public static function isValidMatchOperator($operator)
    {

        if (!isset(self::$matchOperators)) {
            self::$matchOperators = array_merge(self::$compOperators, self::$enumOperators, self::$rangeOperators);
        }

        return in_array($operator, self::$matchOperators);
    }

    public static function isNegativeOperator($operator)
    {
        return in_array($operator, self::$negativeOpertorsMap);
    }

    public static function isEnumOperator($operator)
    {
        return in_array($operator, self::$enumOperators);
    }

    public static function isRangeOperator($operator)
    {
        return in_array($operator, self::$rangeOperators);
    }

    public static function isComparaisonOperator($operator)
    {
        return in_array($operator, self::$compOperators);
    }

    public static function isLogicalFactor($factor)
    {
        return in_array($factor, self::$logicalFactors);
    }

}
