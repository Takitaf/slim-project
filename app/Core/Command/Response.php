<?php

namespace Core\Command;

abstract class Response {
    protected $result;

    public function __construct($result = null) {
        $this->result = $result;
    }

    public function getResult () {
        return $this->result;
    }

    public function hasErrors () {
        return false;
    }

    public function hasFile () {
        return false;
    }
}