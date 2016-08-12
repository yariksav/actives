<?php
namespace yariksav\actives\view\plugins;

use yii;

class MenuPlugin extends Plugin
{
    public $buttons = [];

    public function build() {
        return array_merge(parent::build(), [
            'buttons'=>$this->buttons
        ]);
    }
}
