<?php
namespace yariksav\actives\view\buttons;

use yariksav\actives\base\ProtectedObject;
use yii\helpers\ArrayHelper;

class Button extends ProtectedObject
{
    public $buttons;
    public $data;
    public $icon;
    public $name;
    public $text;
    public $type;

    public function build($model) {
        $btn = [
            'text' => $this->text,
            'icon' => $this->icon
        ];

        if ($this->data) {
            $btn['data'] = is_callable($this->data) ? call_user_func_array($this->data, ['data' => $model]) : $this->data;
        }

        return $btn;
    }

    public function isVisible($model) {
        if (is_callable($this->_visible)) {
            return call_user_func_array($this->_visible, [$model]);
        }
        return $this->_visible;/* && $this->hasPermissions(); todo */
    }
}
