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
use Symfony\Component\Yaml\Exception\ParseException;
use Aw\Ezp\FetchBundle\Fetch\Exception\CqlParseException;

class Parser
{

    public function parse($input)
    {
        return is_array($input)? $input : $this->doParse($input);
    }

    protected function doParse($input)
    {
        try {
            return (array) Yaml::Parse($input);
        } catch (ParseException $e) {

            throw new CqlParseException($e, $input);
        }
    }
}
