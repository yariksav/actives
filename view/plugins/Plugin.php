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
    public $apply; //test
    public $collection;

    public $saveState = false;
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
        if (is_callable($this->apply) && $this->value !== null) {
            call_user_func($this->apply, $provider, $this->value);
        }
    }

    public function getValue() {
        // todo if need strict initial data, use  && $this->_value === null
        if ($this->saveState && $this->owner->method === 'init') {
            $this->_value = $this->owner->getState($this->name, $this->_value);
        }
        return $this->_value;
    }

    public function setValue($value) {
        $this->_value = $value;
        if ($this->saveState && $this->owner->method === 'load') {
            $this->owner->setState($this->name, $value);
        }
    }

}
