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
use Aw\Ezp\FetchBundle\Fetch\Exception\InvalidArgumentException;
use eZ\Publish\API\Repository\Values\Content\Query;

class SortClauseUtils
{
    protected static $sortDirections = array('ASC', 'DESC', true, false);

    public static function assertIsValidTargetFormat($identifier, $target, array $path = array())
    {
        $path[] = $identifier;
        switch ($identifier) {
        case 'field':
            if (!(is_string($target) && (strpos($target, "/") !== false))) {
                throw new InvalidArgumentException('Invalid field target format', $target, 'ContentTypeIdentifier/FieldIdentifier', $path);
            }
            break;
        default:
            break;
        }
    }

    public static function assertIsSortClauseIdentifier($identifier, $path = array())
    {
        if (!SortClauseUtils::isValidSortClauseIdentifier($identifier)) {
            throw new InvalidArgumentException('Invalid Sort Clause Identifier', $identifier, 'A valid Sort Clause identifier', $path);
        }
    }

    public static function assertIsSortDirection($direction, $path = array())
    {
        if (!SortClauseUtils::isValidSortDirection($direction)) {
            throw new InvalidArgumentException('Invalid Sort Direction', $direction, self::$sortDirections, $path);
        }
    }

    public static function getIdentifierInfos($identifier)
    {
        $infos = explode('.', $identifier, 2);

        return array($infos[0], isset($infos[1]) ? $infos[1] : null);
    }

    public static function getSortClauseClassName($identifier)
    {
        $className = implode('', array_map('ucwords', explode('_', $identifier)));

        return 'eZ\\Publish\\API\\Repository\\Values\\Content\\Query\\SortClause\\' . $className;
    }

    public static function getApiSortDirection($sortDirection)
    {
        if (is_bool($sortDirection)) {

            $sortDirection = $sortDirection ? Query::SORT_ASC : Query::SORT_DESC;

        } elseif (in_array($sortDirection, static::$sortDirections)) {

            $sortDirection = ($sortDirection == 'ASC') ? Query::SORT_ASC : Query::SORT_DESC;
        }

        return $sortDirection;
    }

    public static function isValidSortClauseIdentifier($identifier)
    {
        return class_exists(self::getSortClauseClassName($identifier));
    }

    public static function isValidSortDirection($direction)
    {
        return in_array($direction, static::$sortDirections, true);
    }

}
