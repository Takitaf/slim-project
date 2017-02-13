<?php
namespace Core\Utilities;

class Registry {
    private $values;
    private $databaseAccess;
    static private $instance;

    private function __construct () {}

    static public function getInstance () {
        if (empty(self::$instance)) {
            self::$instance = new self();
        }
        return self::$instance;
    }

    private function get ($key) {
        if (empty($this->values[$key])) {
            return null;
        }
        return $this->values[$key];
    }

    private function set ($key, $value) {
        return $this->values[$key] = $value;
    }

    public function getDatabaseAccess () {
        if (empty($this->databaseAccess)) {
            $this->loadDatabaseAccess();
        }
        return $this->databaseAccess;
    }

    private function loadDatabaseAccess () {
        $url = $this->getProjectRootDirectory() . DIRECTORY_SEPARATOR . "config" . DIRECTORY_SEPARATOR . "database.ini";
        $this->databaseAccess = parse_ini_file($url);
    }

    public function getProjectRootDirectory () {
        return str_replace("/", DIRECTORY_SEPARATOR, substr($_SERVER["DOCUMENT_ROOT"], 0, strrpos($_SERVER["DOCUMENT_ROOT"], "/")));
    }
}