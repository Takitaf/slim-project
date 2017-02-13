<?php
/**
 * Created by PhpStorm.
 * User: Tafit
 * Date: 22.01.2017
 * Time: 11:35
 */

namespace Core\Mappers;

abstract class MapStrategy {

    abstract public function map(Array $data);
}