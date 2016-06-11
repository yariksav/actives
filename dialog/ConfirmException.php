<?php

namespace yariksav\actives\dialog;

class ConfirmException extends \yii\base\Exception{

    public $buttons;
    public $id;
    public function __construct($message, $id, $buttons = null) {
        $this->id = $id;
        $this->buttons = $buttons;
        parent::__construct($message);
    }
}
