<?php

namespace yariksav\actives\base;

class Component extends \yii\base\Component
{
    function __construct($config = []) {
        $config = array_merge($this->defaults(), $config);
        parent::__construct($config);
    }

    public function defaults() {
        return [];
    }

    public static function getClass()
    {
        return get_called_class();
    }

    //    public function className() {
    //        return get_called_class();
    //    }

//    public function evaluateExpression($_expression_, $_data_ = []) {
//        if (is_string($_expression_)) {
//            extract($_data_);
//            return eval('return '.$_expression_.';');
//
//        } else if(is_array($_expression_) || is_bool($_expression_)){
//            return $_expression_;
//
//        } else {
//            $_data_[]=$this;
//            return call_user_func_array($_expression_, $_data_);
//        }
//    }
}