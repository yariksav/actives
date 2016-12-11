<?php
namespace yariksav\actives\components;

use yii;
use yariksav\actives\base\ActiveObject;
use yariksav\actives\base\PermissionTrait;
use yariksav\actives\base\VisibleTrait;

class Modal extends ActiveObject
{
    use PermissionTrait;
    use VisibleTrait;
    protected $_options;
    protected $_forms;

    function __construct($config = []) {
        $this->_forms = new FormsMgr($this);
        $this->_forms->config = $config;
//        $this->_controls = new ControlMgr($this);
//
//        $this->_actions = new ActionMgr($this);
//        $this->_actions->load([
//            'load' => [
//                'on' => [$this, 'onLoad']
//            ],
//            'control' => [
//                'on' => [$this, 'onControl']
//            ]
//        ]);
        $this->_options = [
            'keyboard' => true,
            'backdrop' => true,
        ];
        //$config = array_merge($this->defaults(), $config);
        parent::__construct($this->defaults());

        //$this->_actions->current = $this->action ? : 'load';
    }

//    public function init() {
//
//    }

    protected function defaults() {

    }

    public function run() {
        $output = [];
        foreach($this->_forms as $name=>$form) {
            $output[$name] = $form->run();
        }
        return [
            'forms'=>$output,
            'options'=>$this->_options
        ];
        //return [];
    }

    public function getForms() {
        return $this->_forms;
    }

    public function setForms($value) {
        $this->_forms->load($value);
    }

}

?>