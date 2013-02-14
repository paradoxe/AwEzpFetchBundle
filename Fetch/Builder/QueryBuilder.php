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
    public $filterBuilder;
    public $sortBuilder;
    public $offsetBuilder;
    public $limtBuilder;

    public function __construct(BuilderInterface $filterBuilder, BuilderInterface $sortBuilder, BuilderInterface $offsetBuilder, BuilderInterface $limtBuilder)
    {
        $this->filterBuilder = $filterBuilder;
        $this->sortBuilder = $sortBuilder;
        $this->offsetBuilder = $offsetBuilder;
        $this->limtBuilder = $limtBuilder;
    }

    public function build(array $parameters = array())
    {
        $query = new Query();
        $query->criterion = $this->filterBuilder->build($parameters);
        $query->sortClauses = $this->sortBuilder->build($parameters);
        $query->offset = $this->offsetBuilder->build($parameters);
        $query->limit = $this->limtBuilder->build($parameters);

        return $query;
    }
}
