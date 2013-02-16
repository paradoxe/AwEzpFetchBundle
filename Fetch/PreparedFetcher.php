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

namespace Aw\Ezp\FetchBundle\Fetch;
use Aw\Ezp\FetchBundle\Fetch\Builder\QueryBuilder;
use eZ\Publish\API\Repository\Repository;

class PreparedFetcher
{

    protected $queryBuilder;
    protected $repository;
    protected $parameters;

    public function __construct(QueryBuilder $queryBuilder, Repository $repository)
    {
        $this->queryBuilder = $queryBuilder;
        $this->repository = $repository;
        $this->resetParams();
    }

    /**
     * Binds a parameter to the specified variable name
     *
     * @param string $name Parameter identifier(named placeholder)
     * @param mixed $value Parameter value.
     * @return PreparedFetcher self instance so you can chain multiple binds
     */
    public function bindParam($name, $value)
    {
        $this->parameters[$name] = $value;

        return $this;
    }

    /**
     * Resets all parameters
     *
     * @return PreparedFetcher self instance so you can chain multiple binds
     */
    public function resetParams()
    {
        $this->parameters = array();

        return $this;
    }

    /**
     * Finds content objects for the given query.
     *
     * @param array $fieldFilters - a map of filters for the returned fields.
     *         (see eZ\Publish\API\Repository\SearchService)
     * @param boolean $filterOnUserPermissions if true only the objects which is the user allowed to read are returned.
     *         (see eZ\Publish\API\Repository\SearchService)
     *
     * @return \eZ\Publish\API\Repository\Values\Content\Search\SearchResult
     */
    public function fetch(array $fieldFilters = array(), $filterOnUserPermissions = true)
    {
        $query = $this->queryBuilder->build($this->parameters);

        return $this->repository->getSearchService()->findContent($query, $fieldFilters, $filterOnUserPermissions);
    }
}
