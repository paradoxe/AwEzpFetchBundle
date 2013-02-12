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
use Aw\Ezp\FetchBundle\Fetch\Utils\SortClauseUtils;

class SortClauseBuilder extends Builder
{
    public $className;
    public $identifier;
    public $givenIdentifier;
    public $target;
    public $sortDirection;
    public $path;

    public function __construct($identifier, $sortDirection, array $path)
    {
        $givenIdentifier = $identifier;

        list($identifier, $target) = SortClauseUtils::getIdentifierInfos($identifier);

        SortClauseUtils::assertIsSortClauseIdentifier($identifier, $path);

        SortClauseUtils::assertIsValidTargetFormat($identifier, $target, $path);

        $path[] = $givenIdentifier;

        $className = SortClauseUtils::getSortClauseClassName($identifier);

        $this->className = $className;
        $this->identifier = $identifier;
        $this->givenIdentifier = $givenIdentifier;
        $this->target = $target;
        $this->sortDirection = $sortDirection;
        $this->path = $path;
    }

    public function build(array $parameters = array())
    {
        $sortDirection = $this->sortDirection;
        $className = $this->className;

        if (is_string($sortDirection) && array_key_exists($sortDirection, $parameters)) {
            $sortDirection = $parameters[$sortDirection];
        }

        $path[] = $this->givenIdentifier;

        SortClauseUtils::assertIsSortDirection($sortDirection, $path);

        $apiSortDirection = SortClauseUtils::getApiSortDirection($sortDirection);

        if ($this->identifier == 'field') {

            list($typeIdentifier, $fieldIdentifier) = explode("/", $this->target, 2);

            return new $className($typeIdentifier, $fieldIdentifier, $apiSortDirection);
        }

        return new $className($apiSortDirection);
    }

}
