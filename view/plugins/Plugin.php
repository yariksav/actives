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

    function __construct($owner, $config = []) {
        parent::__construct($config);
        $this->owner = $owner;

    }

    public function build() {
        return [
            'name'=>$this->name
        ];
    }

}
