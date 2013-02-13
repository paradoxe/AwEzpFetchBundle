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

use Aw\Ezp\FetchBundle\Fetch\Builder\OptionBuilder;
use Aw\Ezp\FetchBundle\Fetch\Builder\QueryBuilder;
use Aw\Ezp\FetchBundle\Fetch\Builder\SortClauseBuilder;
use Aw\Ezp\FetchBundle\Fetch\Builder\LogicalTermBuilder;
use Aw\Ezp\FetchBundle\Fetch\Builder\CompositeBuilder;
use Aw\Ezp\FetchBundle\Fetch\Builder\CriterionBuilder;

class Compiler
{

    public function compile(Structure $queryStruct)
    {
        $filter = $this->prepareFilter($queryStruct->getFilter());
        $sort = $this->prepareSortClauses($queryStruct->getSort());
        $offset = $this->prepareOffset($queryStruct->getOffset());
        $limit = $this->prepareLimit($queryStruct->getLimit());

        return new QueryBuilder($filter, $sort, $offset, $limit);
    }

    protected function prepareFilter($parameters)
    {
        $path = array('filter');
        return $this->buildCondition($parameters, $path);
    }

    protected function prepareSortClauses($parameters)
    {
        $path = array('sort');

        $sortClauseBuilders = array();

        foreach ($parameters as $sortClause) {
            $sortClauseBuilders[] = new SortClauseBuilder($sortClause['identifier'], $sortClause['direction'], $path);
        }

        return new CompositeBuilder($sortClauseBuilders);
    }

    protected function prepareLimit($limit)
    {
        $path = array('limit');

        return new OptionBuilder('limit', 'integer', $limit, $path);
    }

    protected function prepareOffset($offset)
    {
        $path = array('offset');

        return new OptionBuilder('offset', 'integer', $offset, $path);
    }

    protected function buildCondition($parameters, array $path)
    {
        $type = key($parameters);
        $value = $parameters[$type];

        return ($type == 'criterion') ? $this->buildCriterion($value, $path) : $this->buildLogicalTerm($value, $path);
    }

    protected function buildCriterion($parameters, array $path)
    {
        return new CriterionBuilder($parameters['identifier'], $parameters['match']['operator'], $parameters['match']['operand'], $path);
    }

    protected function buildLogicalTerm($parameters, array $path)
    {
        $factor = $parameters['factor'];
        $path[] = $factor;
        $criteria = array();

        foreach ($parameters['criteria'] as $conditionParameters) {
            $criteria[] = $this->buildCondition($conditionParameters, $path);
        }

        $criteriaBuilder = new CompositeBuilder($criteria);

        return new LogicalTermBuilder($factor, $criteriaBuilder, $path);
    }
}
