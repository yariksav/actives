<?php
/**
 * Created by PhpStorm.
 * User: Savaryn Yaroslav
 * Date: 23.05.2016
 * Time: 16:37
 */

namespace yariksav\actives\controls;

use yii;

class DialogControl extends Control {

    public $config = [];
    public $requireModel = true;

    public function build() {
        $control = parent::build();
        $activeObject = Yii::createObject($this->config, [$request]);
        $control['data'] = $activeObject->run();
        return $control;
    }
}