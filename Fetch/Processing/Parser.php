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

namespace Aw\Ezp\FetchBundle\Fetch\Processing;
use Symfony\Component\Yaml\Yaml;

class Parser
{

    public function parse($input)
    {
        return is_array($input)? $input : Yaml::parse($input);
    }
}
