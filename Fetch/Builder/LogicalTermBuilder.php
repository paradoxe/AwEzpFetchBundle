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

class LogicalTermBuilder extends Builder
{
    public $factor;
    public $criteriaBuilder;

    public function __construct($factor, CompositeBuilder $criteriaBuilder, array $path)
    {
        CriterionUtils::assertIsLogicalFactor($factor, $path);

        $this->factor = $factor;
        $this->criteriaBuilder = $criteriaBuilder;
    }

    public function build(array $parameters = array())
    {
        $criteria = $this->criteriaBuilder->build($parameters);

        return CriterionFactory::buildLogicalTerm($this->factor, $criteria);
    }
}
