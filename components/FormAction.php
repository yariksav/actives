<?php

namespace yariksav\actives\components;

use yariksav\actives\base\ProtectedObject;
use yariksav\actives\base\RunnableInterface;
use yii;
use yii\base;
use yii\base\Object;

class FormAction extends ProtectedObject implements RunnableInterface
{
    public $name;
    public $on;
    public $after;
    public $before;
    public $type;
    public $data;
    public $icon;
    public $iconright;
    public $options;

    protected $_owner;
    protected $_text;

    public $validate = false;

    function __construct($owner, $config = []) {
        parent::__construct($config);
        $this->_owner = $owner;
    }

    public function getVisible() {
        if (is_callable($this->_visible)) {
            $this->_visible = call_user_func_array($this->_visible, ['owner' => $this->owner]);
        }
        return $this->_visible && $this->hasPermissions();
    }

    public function getText() {
        if (!$this->_text) {
            return Yii::t('actives', ucfirst($this->name));
        }
        return $this->_text;
    }

    public function setText($value) {
        $this->_text = $value;
    }

    public function link() {
        $res = [
            'type' => $this->type,
            'text' => $this->getText()
        ];
        if ($this->icon) {
            $res['icon'] = $this->icon;
        }
        if ($this->iconright) {
            $res['iconright'] = $this->iconright;
        }
        if ($this->options) {
            $res['options'] = $this->options;
        }
        return $res;
    }

    public function run() {
        if (is_callable($this->before)) {
            call_user_func($this->before);
        }

        if (is_callable($this->on)) {
            call_user_func($this->on);
        }

        if (is_callable($this->after)) {
            call_user_func($this->after);
        }
    }

    /*public function enableEvents() {
        $this->disableEvents();
        if (!$this->visible) {
            return;
        }
        if ($this->on) {
            $this->owner->on($this->name, $this->on);
        }
        if ($this->after) {
            $this->owner->on('after' . $this->name, $this->after);
        }
        if ($this->before) {
            $this->owner->on('before' . $this->name, $this->before);
        }
    }

    public function disableEvents() {
        if ($this->on) {
            $this->owner->off($this->name, $this->on);
        }
        if ($this->after) {
            $this->owner->off('after' . $this->name, $this->after);
        }
        if ($this->before) {
            $this->owner->off('before' . $this->name, $this->before);
        }
    }*/
}