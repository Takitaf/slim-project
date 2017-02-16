<?php
namespace App\Logic\Commands;

use Core\Command\ErrorResponse;

abstract class Command {
    public function execute ($data) {
        $errors = $this->validateParameters($data);
        if (!empty($errors)) {
            return new ErrorResponse(400, $errors);
        }

        return $this->makeOperation($data);
    }

    abstract function validateParameters ($data);
    abstract function makeOperation ($data);
}