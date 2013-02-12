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
use Aw\Ezp\FetchBundle\Fetch\Exception\InvalidArgumentException;

class OptionBuilder extends Builder
{
    public $name;
    public $type;
    public $value;
    public $path;

    public function __construct($name, $type, $value, array $path)
    {
        $this->name = $name;
        $this->type = $type;
        $this->value = $value;
        $this->path = $path;
    }

    public function build(array $parameters = array())
    {
        $value = $this->value;

        if (is_string($this->value) && array_key_exists($this->value, $parameters)) {
            $value = $parameters[$this->value];
        }

        if (!is_null($value) && (gettype($value) !== $this->type)) {
            throw new InvalidArgumentException('Invalid ' . $this->name, $value, $type, $this->path);
        }

        return $value;
    }
}
