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
    protected $component;

    public function load() {
        if (isset($this->_value)) {
            $this->config['data'] = $this->getValue();
        }
        $this->component = ActiveObject::createObject($this->config);
        $this->component->run();
        return [
            'data'=>$this->component->getResponse(),
            'component'=>$this->component->componentName
        ];
    }
}