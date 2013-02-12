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
use eZ\Publish\API\Repository\Values\Content\Query\Criterion;
use eZ\Publish\API\Repository\Values\Content\Query\Criterion\LogicalNot;
use eZ\Publish\API\Repository\Values\Content\Query\Criterion\LogicalOr;
use eZ\Publish\API\Repository\Values\Content\Query\Criterion\LogicalAnd;

class CriterionFactory
{

    /**
     * Creates AND logic criterion.
     *
     * @param Criterion[] $criteria
     */
    public static function buildAnd(array $criteria)
    {
        return new LogicalAnd($criteria);
    }

    /**
     * Creates OR logic criterion.
     *
     * @param Criterion[] $criteria
     */
    public static function buildOr(array $criteria)
    {
        return new LogicalOr($criteria);
    }

    /**
     * Creates NOT logic criterion.
     *
     * @param Criterion[] $criteria One criterion, as a an array
     * @throws \InvalidArgumentException if more than one criterion is given in the array parameter
     */
    public static function buildNot(Criterion $criterion)
    {
        return new LogicalNot($criterion);
    }

    /**
     * Creates AND or OR logic criterion depending on $factor.
     *
     * @param string $facor "AND" , "OR" , "NAND", "NOR"
     * @param Criterion[] $criteria
     */
    public static function buildLogicalTerm($factor, array $criteria)
    {
        $factor = strtoupper($factor);

        $criterion = (in_array($factor, array('OR', 'NOR'))) ? self::buildOr($criteria) : self::buildAnd($criteria);

        if (in_array($factor, array('NAND', 'NOR'))) {
            $criterion = self::buildNot($criterion);
        }

        return $criterion;
    }

}
