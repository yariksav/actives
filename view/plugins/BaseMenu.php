<?php
namespace yariksav\actives\view\plugins;

use yii;

class BaseMenu extends Plugin
{
    public $buttons = [];

    public function build() {
        return array_merge(parent::build(), [
            'buttons'=>$this->buttons
        ]);
    }
}
