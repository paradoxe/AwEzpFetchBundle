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
class Processor
{

    protected $synthesizer;
    protected $parser;
    protected $compiler;
    protected $cache;

    public function __construct()
    {
        $this->cache = array();
        $this->synthesizer = new Synthesizer();
        $this->compiler = new Compiler();
        $this->parser = new Parser();
    }

    public function process($input)
    {
        $key = $this->getQueryCacheKey($input);

        if (!array_key_exists($key, $this->cache)) {
            $this->cache[$key] = $this->doProcess($input);
        }

        return $this->cache[$key];
    }

    protected function doProcess($queryString)
    {
        $parsed = $this->parse($queryString);
        $struct = $this->synthesize($parsed);
        $query = $this->build($struct);

        return $query;
    }

    protected function parse($input)
    {
        return $this->parser->parse($input);
    }

    protected function synthesize(array $input)
    {
        return $this->synthesizer->synthesize($input);
    }

    protected function build(Structure $queryStruct)
    {
        return $this->compiler->compile($queryStruct);
    }

    protected function getQueryCacheKey($input)
    {
        return sha1(serialize($input));
    }

}
