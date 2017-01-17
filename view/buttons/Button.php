<?php
namespace yariksav\actives\view\buttons;

use yariksav\actives\base\ProtectedObject;
use yii\helpers\ArrayHelper;

class Button extends ProtectedObject
{
    public $data;
    public $item = false;
    public $icon;
    public $text;
    public $options;

    public function build($model) {
        $btn = [
            'text' => $this->text,
            'icon' => $this->icon,
        ];

        if ($this->options) {
            $btn['options'] = $this->options;
        }

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
