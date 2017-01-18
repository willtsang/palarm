<?php
/**
 * Created by PhpStorm.
 * User: will
 * Date: 2017/1/18
 * Time: 14:26
 */

namespace Palarm\Record;

class Collection implements \ArrayAccess , \Countable, \Iterator
{
    private $payload;
    private $items = [];

     /**
     * @param mixed $payload
     */
    public function setPayload($payload)
    {
        $this->payload = $payload;
    }

    /**
     * @return mixed
     */
    public function getPayload()
    {
        return $this->payload;
    }

    public function add($item)
    {
        $this->items[] = $item;
    }

    public function toArray()
    {
        return $this->items;
    }

    public function count()
    {
        return count($this->items);
    }

    public function current()
    {
        return current($this->items);
    }


    public function next()
    {
        return next($this->items);
    }


    public function key()
    {
        return key($this->items);
    }

    public function valid()
    {
        return  $this->current() !== false;
    }

    public function rewind()
    {
        reset($this->items);
    }

    public function offsetExists($offset)
    {
        return isset($this->items[$offset]);
    }

    public function offsetGet($offset)
    {
        return $this->items[$offset];
    }

    public function offsetSet($offset, $value)
    {
        $this->items[$offset] = $value;
    }

    public function offsetUnset($offset)
    {
        unset($this->items[$offset]);
    }
}

