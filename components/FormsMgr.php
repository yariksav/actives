<?php

namespace yariksav\actives\components;

use yii;
use yii\base;
use yii\base\Object;
use yariksav\actives\base\Component;
use yariksav\actives\base\Collection;

class FormsMgr extends Collection
{
    public $config = [];

    protected function createObject($params) {
        $params = array_merge($params, $this->config);
        $name = $params['name'];
        if (empty($name) || is_int($name)) {
            throw new \Exception('Please get the name for action');
        }
        // set specific key
        if (isset($this->config['key'][$name])) {
            $params['key'] = $this->config['key'][$name];
        }

        if (empty($params['class'])) {
            $params['class'] = Form::className();
        }
        $obj = Yii::createObject($params, [$this->owner]);
        return $obj;
    }

//    public function links() {
//        $actions = [];
//        foreach ($this->_collection as $key => $action) {
//            if ($action->visible && $action->type) {
//                $actions[$key] = $action->link();
//            }
//        }
//        return $actions ? : null;
//    }
//
//    public function setCurrent($value) {
//        // disable previos events
//        if ($this->_current && $this->_current !== $value && $this->getCurrent()) {
//            $this->getCurrent()->disableEvents();
//        }
//
//        if (isset($this->_collection[$value])) {
//            $this->_current = $value;
//            $this->getCurrent()->enableEvents();
//        }
////        else {
////            throw new base\InvalidParamException('Could not found action '.$value);
////        }
//    }
//
//    public function getCurrent() {
//        if (isset($this->_collection[$this->_current])) {
//            return $this->_collection[$this->_current];
//        }
//        return null;
//    }

}