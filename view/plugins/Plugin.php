<?php
namespace yariksav\actives\view\plugins;

use yii;
use yii\base\Component;
use yariksav\actives\base\ProtectedObject;

class Plugin extends ProtectedObject
{
    public $name;
    public $type;
    public $_activeObject;

    function __construct($activeObject, $config = []) {
        parent::__construct($config);
        $this->_activeObject = $activeObject;

    }

    public function build() {
        return ['name'=>$this->name];
    }

}
