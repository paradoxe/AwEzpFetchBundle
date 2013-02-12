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
use Aw\Ezp\FetchBundle\Fetch\Processing\Processor;
use eZ\Publish\API\Repository\Repository;

class Fetcher
{
    protected $repository;
    protected $queryProcessor;

    public function __construct(Repository $repository)
    {
        $this->repository = $repository;
        $this->queryProcessor = new Processor();
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
    public function fetch($queryInput, array $fieldFilters = array(), $filterOnUserPermissions = true)
    {
        $queryBuilder = $this->processQuery($queryInput, false);

        $query = $queryBuilder->build();

        return $this->repository->getSearchService()->findContent($query, $fieldFilters, $filterOnUserPermissions);
    }

    /**
     * Finds content objects for the given query.
     *
     * @param String CQL query or array
     *
     * @return \Aw\Ezp\FetchBundle\Fetch\PreparedFetcher
     */
    public function prepare($queryString)
    {
        $queryBuilder = $this->queryProcessor->process($queryString);

        return new PreparedFetcher($queryBuilder, $this->repository);
    }

    protected function processQuery($queryString)
    {
        return $this->queryProcessor->process($queryString);
    }
}
