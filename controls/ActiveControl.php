<?php
/**
 * Created by PhpStorm.
 * User: Savaryn Yaroslav
 * Date: 23.05.2016
 * Time: 11:28
 */

namespace yariksav\actives\controls;

use yii;
use yariksav\actives\base\ActiveObject;

class ActiveControl extends Control {

    public $requireModel = true;

    public function load() {
        $instance = ActiveObject::createObject($this->config);
        $instance->run();
        return [
            'data'=>$instance->getResponse()
        ];
    }
}