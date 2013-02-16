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

use Symfony\Component\Yaml\Exception\ParseException;

class CqlParseException extends Exception
{

    public function __construct(ParseException $e, $input)
    {
        $rx = new \ReflectionObject($e);
        $rawProp = $rx->getProperty('rawMessage');
        $rawProp->setAccessible(true);
        $details = $rawProp->getValue($e);
        $msg = 'Invalid CQL input at line '.$e->getParsedLine();
        $msg .= "\n\n DETAILS: " . $details ."\n";
        $this->setGiven($input);

        parent::__construct($msg);
    }

}
