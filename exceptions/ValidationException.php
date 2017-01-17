<?php

namespace yariksav\actives\exceptions;

class ValidationException extends \Exception {

    public $validation;

    public function __construct(array $validation, $message = null) {
        $this->validation = $validation;
        parent::__construct($message);
    }
}
