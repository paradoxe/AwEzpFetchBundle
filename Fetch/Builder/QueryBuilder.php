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
use eZ\Publish\API\Repository\Values\Content\Query;

class QueryBuilder extends Builder
{
    public $preparedSort;
    public $preparedLimt;
    public $preparedOffset;
    public $preparedFilter;

    public function __construct(BuilderInterface $preparedFilter, BuilderInterface $preparedSort, BuilderInterface $preparedOffset, BuilderInterface $preparedLimt)
    {
        $this->preparedFilter = $preparedFilter;
        $this->preparedSort = $preparedSort;
        $this->preparedOffset = $preparedOffset;
        $this->preparedLimt = $preparedLimt;
    }

    public function build(array $parameters = array())
    {
        $filter = $this->preparedFilter->build($parameters);
        $sort = $this->preparedSort->build($parameters);
        $offset = $this->preparedOffset->build($parameters);
        $limit = $this->preparedLimt->build($parameters);

        $query = new Query();

        $query->criterion = $filter;
        $query->sortClauses = $sort;
        $query->limit = $limit;
        $query->offset = $offset;

        return $query;
    }
}
