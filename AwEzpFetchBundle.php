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

namespace Aw\Ezp\FetchBundle;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\HttpKernel\Bundle\Bundle;
use Aw\Ezp\FetchBundle\DependencyInjection\Compiler\FetcherCompilerPass;

class AwEzpFetchBundle extends Bundle
{

    public function build(ContainerBuilder $container)
    {
        parent::build($container);
    }
}
