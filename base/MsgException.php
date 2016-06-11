<?php

namespace yariksav\actives\base;

class MsgException extends Exception{

    public function __construct($msg, $params = array(), $code = 0, $category = 'app.error'){
        if (is_integer($params)){
            $code = $params;
            $params = array();
        }
        $this->message = \Yii::t($category, $msg, $params);
        parent::__construct($this->message, $code);
    }
}
