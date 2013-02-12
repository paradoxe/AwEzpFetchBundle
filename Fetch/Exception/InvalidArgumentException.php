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

namespace Aw\Ezp\FetchBundle\Fetch\Exception;
class InvalidArgumentException extends Exception
{

    public function __construct($message, $given, $expected = null, array $path = null)
    {
        $this->setGiven($given);
        $this->setExpected($expected);
        $this->setPath($path);
        parent::__construct($message);
    }

}
