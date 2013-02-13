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

namespace Aw\Ezp\FetchBundle\Fetch\Processing;
use Aw\Ezp\FetchBundle\Fetch\Utils\CriterionUtils;
use Aw\Ezp\FetchBundle\Fetch\Utils\ArrayUtils;
use Aw\Ezp\FetchBundle\Fetch\Exception\InvalidQueryStructureException;
use Aw\Ezp\FetchBundle\Fetch\Exception\InvalidArgumentException;

class Synthesizer
{

    public function synthesize(array $query)
    {
        $path = array();

        static::assertIsMapStructure($query, 1, null, 'Invalid Query structure', $path);

        if (!array_key_exists('filter', $query)) {
            throw new InvalidQueryStructureException('Invalid Query structure', $query, 'A query should contains at least one filter', $path,
                    'Map with no filter set');
        }

        $data = array();

        foreach ($query as $option => $value) {

            switch ($option) {
            case 'filter':
                $data['filter'] = $this->synthesizeFilterInput($value, $path);
                break;
            case 'sort':
                $data['sort'] = $this->synthesizeSortInput($value, $path);
                break;
            case 'limit':
                $data['limit'] = $this->synthesizeLimitInput($value, $path);
                break;
            case 'offset':
                $data['offset'] = $this->synthesizeOffsetInput($value, $path);
                break;
            default:
                throw new InvalidQueryStructureException('Unkown query directive', $option, array('sort', 'offset', 'limit'), $path);
            }
        }

        return new Structure($data);
    }

    protected function synthesizeFilterInput($value, $path)
    {
        $path[] = 'filter';

        return $this->synthesizeConditionInput($value, $path);
    }

    protected function synthesizeConditionInput($value, $path)
    {
        static::assertIsMapStructure($value, 1, 1, 'Invalid Filter Structure', $path);

        if (CriterionUtils::isLogicalFactor(key($value)) || ArrayUtils::isIndexedArray(end($value))) {

            return $this->synthesizeLogicalTermInput($value, $path);
        }

        return $this->synthesizeCriterionInput($value, $path);
    }

    protected function synthesizeCriterionInput($value, $path)
    {
        static::assertIsMapStructure($value, 1, 1, 'Invalid criterion structure', $path);

        $identifier = key($value);
        $path[] = $identifier;

        $match = $value[$identifier];
        $criterion = array();
        $criterion['criterion']['identifier'] = $identifier;
        $criterion['criterion']['match'] = $this->synthesizeCriterionMatch($match, $path);

        return $criterion;
    }

    protected function synthesizeCriterionMatch($value, $path)
    {
        static::assertIsMapStructure($value, 1, 1, 'Invalid Criterion match structure', $path);

        $operator = key($value);
        $path[] = $operator;

        $operand = $value[$operator];

        $match = array();
        $match['operator'] = $operator;
        $match['operand'] = $operand;

        return $match;
    }

    protected function synthesizeLogicalTermInput($value, $path)
    {
        static::assertIsMapStructure($value, 1, 1, 'Invalid Logical Term Structure', $path);

        $factor = key($value);

        $path[] = $factor;

        $criteria = $value[$factor];

        $logicalTerm = array();
        $logicalTerm['logical_term']['factor'] = $factor;
        $logicalTerm['logical_term']['criteria'] = $this->synthesizeCriteriaInput($criteria, $path);

        return $logicalTerm;
    }

    protected function synthesizeCriteriaInput($value, $path)
    {
        static::assertIsListStructure($value, 1, null, 'Invalid Criteria structure', $path);

        $criteria = array();

        foreach ($value as $condition) {
            $criteria[] = $this->synthesizeConditionInput($condition, $path);
        }

        return $criteria;
    }

    protected function synthesizeSortInput($value, $path)
    {
        $path[] = 'sort';

        static::assertIsMapStructure($value, 1, null, 'Invalid Sort structure', $path);

        $sortClauses = array();
        foreach ($value as $sortClauseIdentifier => $sortDirection) {

            $path[] = $sortClauseIdentifier;

            self::assertIsScalar($sortDirection, 'Invalid Sort Direction', $path);

            $sortClauses[] = array('identifier' => $sortClauseIdentifier, 'direction' => $sortDirection);
        }

        return $sortClauses;
    }

    protected function synthesizeLimitInput($value, $apth)
    {
        $path[] = 'limit';

        static::assertIsScalar($value, 'Invalid Limit', $path);

        return $value;
    }

    protected function synthesizeOffsetInput($value, $path)
    {
        $path[] = 'offset';

        static::assertIsScalar($value, 'Invalid Offset', $path);

        return $value;
    }

    protected static function assertIsMapStructure($value, $minLength = null, $maxLength = null, $errorMessage, array $path)
    {
        if (true !== $result = ArrayUtils::isMap($value, $minLength, $maxLength)) {

            $expected = static::buildExpectedStructureString('Map (associative array)', $minLength, $maxLength);
            $e = new InvalidQueryStructureException($errorMessage, $value, $expected, $path);

            if (($result == ArrayUtils::E_TYPE)) {

                $e->setGivenType('Map of Length ' . count($value));
            }

            throw $e;
        }
    }

    protected static function assertIsListStructure($value, $minLength = null, $maxLength = null, $errorMessage, array $path)
    {
        if (true !== $result = ArrayUtils::isIndexedArray($value, $minLength, $maxLength)) {

            $expected = static::buildExpectedStructureString('Sequence (indexed array)', $minLength, $maxLength);

            $e = new InvalidQueryStructureException($errorMessage, $value, $expected, $path);

            if (($result == ArrayUtils::E_TYPE)) {
                $e->setGivenType('List of Length ' . count($value));
            }

            throw $e;
        }
    }

    protected static function assertIsScalar($value, $errorMessage, $path)
    {
        if (!is_scalar($value)) {

            throw new InvalidArgumentException($errorMessage, $value, 'Scalar', $path);
        }
    }

    protected static function buildExpectedStructureString($type, $minLength = null, $maxLength = null)
    {
        $expected = "$type, ";
        if (($minLength !== null) || ($maxLength !== null)) {
            if (($minLength !== null) && ($maxLength !== null)) {

                $expected .= ($minLength == $maxLength) ? sprintf('containing %d element%s', $minLength, ($minLength > 1) ? 's' : '')
                        : sprintf('containing at least %d element%s and at most %d element%s', $minLength, ($minLength > 1) ? 's' : '', $maxLength,
                                ($maxLength > 1) ? 's' : '');
            } else {

                $expected .= ($minLength !== null) ? sprintf('containing at least %d element%s', $minLength, ($minLength > 1) ? 's' : '')
                        : sprintf('containing at most %d element%s', $minLength, ($maxLength > 1) ? 's' : '');
            }
        }

        return $expected;
    }

}
