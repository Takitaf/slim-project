<?php

namespace Core;

use Valitron\Validator;

abstract class DomainObject {

    public function asArray () {
        $array = [];
        foreach ($this as $key => $value) {
            $array[$key] = $value;
        }

        return $array;
    }

    public function isValid () {
        $validator = new Validator($this->asArray());
        $rules = $this->getValidatorRules();

        if (empty($rules)) {
            return true;
        }

        foreach ($rules as $rule) {
            $validator->rule($rule["name"], $rule["targets"]);
        }

        return $validator->validate();
    }

    abstract protected function getValidatorRules ();
}