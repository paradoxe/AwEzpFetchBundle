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

class CompositeBuilder extends Builder
{
    public $builders;

    public function __construct(array $builders)
    {
        $this->builders = array();

        foreach($builders as $builder)
        {
            $this->add($builder);
        }
    }

    public function add(BuilderInterface $builder)
    {
        $this->builders[] = $builder;
    }

    public function build(array $parameters = array())
    {
        $result = array();
        foreach ($this->builders as $builder) {
            $result[] = $builder->build($parameters);
        }

        return $result;
    }
}
