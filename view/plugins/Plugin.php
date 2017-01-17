<?php
namespace yariksav\actives\view\plugins;

use yariksav\actives\base\OwnedTrait;
use yii;
use yii\base\Component;
use yariksav\actives\base\ProtectedObject;

class Plugin extends ProtectedObject implements PluginInterface
{
    use OwnedTrait;

    public $cmp;
    //public $name;
    public $apply;
    public $remember = false;
    public $renderItem;

    protected $_value;
    protected $_provider;

    function __construct($owner, $config = []) {
        $this->_owner = $owner;
        parent::__construct($config);
    }

    public function build() {
        return [
            'cmp'=>$this->cmp// ?: $this->name,//(new \ReflectionClass($this))->getShortName(),
            //'name'=>$this->name
        ];
    }

//    public function getCmp() {
//        if (!$this->_cmp) {
//            return (new \ReflectionClass($this))->getShortName();
//        }
//        return $this->_cmp;
//    }
//
//    public function setCmp
    public function getValue() {
        // todo if need strict initial data, use  && $this->_value === null
//        if ($this->remember && $this->owner->method === 'init') {
//            $this->_value = $this->owner->getState($this->name, $this->_value);
//        }
        return $this->_value;
    }

    public function setValue($value) {
        $this->_value = $value;
//        if ($this->remember && $this->owner->method === 'load') {
//            $this->owner->setState($this->name, $value);
//        }
    }

}
