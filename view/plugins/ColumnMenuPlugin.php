<?php
namespace yariksav\actives\view\plugins;

use yii;

class ColumnMenuPlugin extends BaseMenu
{
    public $tag;
    public $tagOptions = [];
    public $showText = false;

    public function build() {
        return array_merge(parent::build(), [
            'tag' => $this->tag,
            'showText' => $this->showText
        ]);
    }
}
