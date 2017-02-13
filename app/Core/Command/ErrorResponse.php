<?php
/**
 * Created by PhpStorm.
 * User: Tafit
 * Date: 14.01.2017
 * Time: 22:22
 */

namespace Core\Command;

class ErrorResponse extends Response {

    private $errorCode;

    public function __construct($errorCode = 400, $result = null) {
        $this->errorCode = $errorCode;
        parent::__construct($result);
    }

    public function getErrorCode () {
        return $this->errorCode;
    }

    public function hasErrors () {
        return true;
    }
}