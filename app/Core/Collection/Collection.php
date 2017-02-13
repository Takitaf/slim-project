<?php

namespace Core\Collection;

class Collection {
    protected $pointer = 0;
    protected $collection = [];

    public function __construct ($data = null) {
        if (!isset($data)) {
            return;
        }

        if ($this->isArrayAssoc($data)) {
            $this->push($data);
        } else {
            foreach ($data as $item) {
                $this->push($item);
            }
        }
    }

    public function length () {
        return sizeof($this->collection);
    }

    public function push ($item) {
        $item = new \ArrayObject($item);
        array_push($this->collection, $item);
        return true;
    }

    public function next () {
        if ($this->pointer >= $this->length() - 1) {
            return false;
        }
        $this->pointer++;
        return true;
    }

    public function prev () {
        if ($this->pointer == 0) {
            return false;
        }
        $this->pointer--;
        return true;
    }

    public function first () {
        $this->pointer = 0;
    }

    public function last () {
        $this->pointer = $this->length() - 1;
    }

    public function &current () {
        $arr = &$this->collection[$this->pointer];
        return $arr;
    }

    public function currentIndex () {
        return $this->pointer;
    }

    public function getRaw () {
        $result = [];
        foreach ($this->collection as $item) {
            $result[] = $item->getArrayCopy();
        }

        return $result;
    }

    public function each ($callable) {
        if (!is_callable($callable)) {
            return false;
        }

        array_walk($this->collection, $callable);
    }

    protected function isArrayAssoc ($array) {
        return array_keys($array) !== range(0, sizeof($array) - 1);
    }
}