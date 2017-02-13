<?php
namespace Core\Mappers;

use Core\Database\DatabaseConnector;
use Core\DomainObject;

abstract class Mapper {

    protected $db;

    public function __construct (DatabaseConnector $db) {
        $this->db = $db;
    }
}