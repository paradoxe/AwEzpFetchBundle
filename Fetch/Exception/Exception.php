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
class Exception extends \RuntimeException
{
    public $error;
    public $expected;
    public $given;
    public $givenType;
    public $path;

    public function __construct($message)
    {
        $this->setError($message);
        parent::__construct($this->getFormattedMessage());
    }

    public function getFormattedMessage()
    {
        $error = $this->getFormatedError();
        $path = $this->getFormatedPath();
        $expected = $this->getFormatedExpected();
        $given = $this->getFormatedGiven();

        $message = sprintf('%s %s %s %s', $error, $path, $expected, $given);

        if (PHP_SAPI === 'cli') {
            $message = "\n" . $message . "\n\n";
        }

        return $message;
    }

    public function getError()
    {
        return $this->error;
    }

    public function setError($error)
    {
        $this->error = $error;
    }

    public function getExpected()
    {
        return $this->expected;
    }

    public function setExpected($expected)
    {
        $this->expected = $expected;
    }

    public function getGiven()
    {
        return $this->given;
    }

    public function setGiven($given)
    {
        $this->given = $given;
    }

    public function getPath()
    {
        return $this->path;
    }

    public function setPath($path)
    {
        $this->path = $path;
    }

    public function getGivenType()
    {
        return $this->givenType;
    }

    public function setGivenType($givenType)
    {
        $this->givenType = $givenType;
    }

    protected function getFormatedError()
    {
        return sprintf('%s ERROR: %s', PHP_EOL, $this->error);
    }

    protected function getFormatedPath()
    {
        return empty($this->path) ? '' : sprintf('%s PATH: |-> %s', PHP_EOL, implode(' -> ', (array) $this->path));
    }

    protected function getFormatedExpected()
    {
        return empty($this->expected) ? '' : sprintf(' %s EXPECTED: %s', PHP_EOL, implode(' | ', array_map('json_encode', (array) $this->expected)));
    }

    protected function getFormatedGiven()
    {
        $format = empty($this->givenType) ? gettype($this->given) : $this->givenType;

        return sprintf('%s GIVEN : (%s) %s', PHP_EOL, $format, json_encode($this->given));
    }

}
