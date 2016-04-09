<?php

namespace yariksav\actives\components;

class SyConfirmException extends \yii\base\Exception{

    public $buttons;
    public $id;
    public function __construct($message, $id, $buttons = null) {
        $this->id = $id;
        $this->buttons = $buttons;
        parent::__construct($message);
    }
}
