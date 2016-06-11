<?php

namespace yariksav\actives\dialog;

class ValidationException extends \yii\base\Exception{

    public $validation;
    public function __construct($validation, $message = null) {
        $this->validation = $validation;
        parent::__construct($message);
    }
}
