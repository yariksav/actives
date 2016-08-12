<?php
namespace yariksav\actives\view\exports;

use yariksav\actives\base\ProtectedObject;
use yii\helpers\ArrayHelper;

class Export extends ProtectedObject
{
    public $icon;
    public $name;
    public $text;
    public $type;
    public $file;

    public function build() {
        $item = [
            'text' => $this->text,
            'icon' => $this->icon
        ];
        return $item;
    }

    public function export() {

    }
}
