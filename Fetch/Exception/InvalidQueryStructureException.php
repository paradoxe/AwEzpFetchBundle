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
class InvalidQueryStructureException extends InvalidArgumentException
{

    public function __construct($message, $given, $expected = null, array $path = null, $givenType = null)
    {
        $this->setGivenType($givenType);

        parent::__construct($message, $given, $expected, $path);
    }
}
