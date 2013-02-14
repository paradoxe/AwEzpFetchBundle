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

namespace Aw\Ezp\FetchBundle\Fetch\Builder;
use Aw\Ezp\FetchBundle\Fetch\Utils\CriterionFactory;
use Aw\Ezp\FetchBundle\Fetch\Utils\CriterionUtils;
use Aw\Ezp\FetchBundle\Fetch\Exception\InvalidArgumentException;
use \InvalidArgumentException as ApiInvalidArgumentException;

class CriterionBuilder extends Builder
{
    public $className;
    public $givenIdentifier;
    public $identifier;
    public $target;
    public $givenOperator;
    public $apiOperator;
    public $givenOperand;
    public $normalizedOperand;
    public $isExclude;
    public $path;

    public function __construct($identifier, $operator, $operand, array $path)
    {
        $givenIdentifier = $identifier;

        list($identifier, $target) = CriterionUtils::getIdentifierInfos($identifier);

        CriterionUtils::assertIsCriterionIdentifier($identifier, $path);

        $path[] = $givenIdentifier;

        CriterionUtils::assertIsMatchOperator($operator, $path);

        $path[] = $operator;

        $isExclude = CriterionUtils::isNegativeOperator($operator);

        $givenOperator = $operator;

        $apiOperator = CriterionUtils::getApiOperator($operator);

        $givenOperand = $operand;

        $normalizedOperand = CriterionUtils::normalizeOperand($givenOperand, $identifier, $target, $apiOperator);

        $className = CriterionUtils::getCriterionClassName($identifier);

        $this->className = $className;
        $this->identifier = $identifier;
        $this->givenIdentifier = $givenIdentifier;
        $this->target = $target;
        $this->givenOperand = $givenOperand;
        $this->normalizedOperand = $normalizedOperand;
        $this->isExclude = $isExclude;
        $this->givenOperator = $givenOperator;
        $this->apiOperator = $apiOperator;
        $this->path = $path;
    }

    public function build(array $parameters = array())
    {
        $operand = $this->normalizedOperand;
        if (is_string($operand) && array_key_exists($operand, $parameters)) {
            $operand = $parameters[$operand];
        }

        CriterionUtils::assertIsValidMatchOperandType($this->givenOperator, $operand, $this->path);

        $className = $this->className;

        try {

            $criterion = $className::createFromQueryBuilder($this->target, $this->apiOperator, $operand);

            return $this->isExclude ? CriterionFactory::buildNot($criterion) : $criterion;

        } catch (ApiInvalidArgumentException $e) {

            $message = $e->getMessage();
            $error = $message;
            $path = $this->path;
            $expected = null;

            if (strpos($message, "Operator") === 0) { //Operator Error

                $specs = $criterion->getSpecifications();
                $expectedOperators = array();

                foreach ($specs as $spec) {

                    $expectedOperators[] = CriterionUtils::getInternalOperator($spec->operator);
                    $expectedOperators[] = CriterionUtils::getInternalOperator($spec->operator, true);
                }

                $error = 'Unsupported Operator';
                $given = $this->givenOperator;
                $expected = $expectedOperators;
                $path = array_slice($this->path, 0, count($this->path) - 1);

            } elseif (strpos($message, "value") !== false) { //Operand (value) Error

                $given = ($operand != $this->normalizedOperand) ? $operand : $this->givenOperand; // for parametred params

            } elseif (strpos($message, 'Unknown') === 0) { //Target Error

                $path = array_slice($this->path, 0, count($this->path) - 1);
                $given = $this->target;

            } else {

                $path = array_slice($this->path, 0, count($this->path) - 2);
                $given = array($this->givenIdentifier => array($this->givenOperator => $this->givenOperand));
            }

            throw new InvalidArgumentException($error, $given, $expected, $path);
        }
    }
}
