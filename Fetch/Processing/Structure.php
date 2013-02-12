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
class Structure implements \Serializable
{

    protected $data;

    public function __construct(array $data)
    {
        $this->setData($data);
    }

    public function getFilter()
    {
        return $this->data['filter'];
    }

    public function setFilter(array $filter)
    {
        $this->data['filter'] = $filer;
    }

    public function getSort()
    {
        return $this->data['sort'];
    }

    public function setSort(array $sort)
    {
        $this->data['sort'] = $sort;
    }

    public function getLimit()
    {
        return $this->data['limit'];
    }

    public function setLimit($limit)
    {
        $this->data['limit'] = $limit;
    }

    public function getOffset()
    {
        return $this->data['offset'];
    }

    public function setOffset($offset)
    {
        $this->data['offset'] = $offset;
    }

    public function serialize()
    {
        return serialize($this->data);
    }

    public function unserialize($str)
    {
        $data = unserialize($str);
        $this->setData($data);
    }

    public function getData()
    {
        return $this->getData();
    }

    protected function setData($data)
    {
        $defaults = array('filter' => null, 'sort' => array(), 'limit' => null, 'offset' => 0);
        $data = array_replace($defaults, $data);
        $this->data = $data;
    }

}

