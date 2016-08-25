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

class ListViewControl extends Control {

    public $config = [];
    public $requireModel = true;

    public function build() {
        $control = parent::build();
        $instance = ActiveObject::createObject($this->config);
        $instance->run();
        $control['data'] = $instance->getResponse();
        return $control;
    }
}