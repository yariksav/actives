<?php
namespace yariksav\actives\view\plugins;

use yii;
use yii\base\Component;
use yariksav\actives\base\ProtectedObject;

class Plugin extends ProtectedObject
{
    public $name;
    public $type;
    public $owner;
    public $apply;
    public $remember = false;

    protected $_value;
    protected $_provider;

    function __construct($owner, $config = []) {
        $this->beforeInit();
        parent::__construct($config);
        $this->owner = $owner;
    }

    public function beforeInit() {}

    public function build() {
        return [
            'type'=>$this->type
        ];
    }

    public function setProvider($provider) {
        $this->_provider = $provider;
        $this->renderApply();
    }

    public function getValue() {
        // todo if need strict initial data, use  && $this->_value === null
        if ($this->remember && $this->owner->method === 'init') {
            $this->_value = $this->owner->getState($this->name, $this->_value);
        }
        return $this->_value;
    }

    public function setValue($value) {
        $this->_value = $value;
        if ($this->remember && $this->owner->method === 'load') {
            $this->owner->setState($this->name, $value);
        }
    }

    public function renderApply() {
        if (is_callable($this->apply) && $this->_provider && $this->getValue()) {
            call_user_func_array($this->apply, [$this->_provider, $this->_value]);
        }
    }

}
