<?php

namespace yariksav\actives\view\exports;

use yii;
use yii\base;
use yii\base\Object;
use yariksav\actives\base\CollectionMgr;
use yariksav\actives\base\Component;


class ExportMgr extends CollectionMgr
{

    public static $builtInColumns = [
        'excel' => 'yariksav\actives\view\exports\Excel',
        'csv' => 'yariksav\actives\view\exports\Csv'
    ];

    protected function createObject($params) {
        if (empty($params['name']) || is_int($params['name'])) {
            throw new \Exception('Please get the name for control');
        }

        if (empty($params['type']) && empty($params['class'])) {
            $params['type'] = $params['name'];
        }

        if (isset($params['type'])) {
            $type = $params['type'];
            if (isset(static::$builtInColumns[$type])) {
                $type = static::$builtInColumns[$type];
            }
            if (is_array($type)) {
                $params = array_merge($type, $params);
            } else {
                $params['class'] = $type;
            }
        }
        return Yii::createObject($params, [
            $this->owner
        ]);
    }

//    protected function prepareButtons(){
//        $this->_buttons = isset($this->buttons) ? $this->evaluateExpression($this->buttons, ['grid'=>$this]) : $this->buttons();
//    }

    public function build() {
        return $this->render();
    }



}