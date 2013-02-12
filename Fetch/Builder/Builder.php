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
abstract class Builder implements BuilderInterface
{

    abstract public function build(array $parameters = array());

    public function serialize()
    {
        $data = get_object_vars($this);
        return serialize($data);
    }

    public function unserialize($str)
    {
        $data = unserialize($str);

        foreach ($data as $property => $value) {
            $this->$property = $value;
        }
    }
}
