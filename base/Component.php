<?php

namespace yariksav\actives\base;

class Component extends \yii\base\Component
{

    public function evaluateExpression($_expression_, $_data_ = []) {
        if (is_string($_expression_)) {
            extract($_data_);
            return eval('return '.$_expression_.';');

        } else if(is_array($_expression_) || is_bool($_expression_)){
            return $_expression_;

        } else {
            $_data_[]=$this;
            return call_user_func_array($_expression_, $_data_);
        }
    }
}