<?php

namespace yariksav\actives\action;

use yariksav\actives\base\ProtectedObject;
use yii;
use yii\base;
use yii\base\Object;

class Action extends ProtectedObject
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
    private $owner;
    private $text;

    function __construct($owner, $config = []) {
        parent::__construct($config);
        $this->owner = $owner;
    }

    public function getVisible() {
        if (is_callable($this->_visible)) {
            $this->_visible = call_user_func_array($this->_visible, ['owner' => $this->owner]);
        }
        return $this->_visible && $this->hasPermissions();
    }

    public function getText() {
        if (!$this->text) {
            return Yii::t('actives', ucfirst($this->name));
        }
        return $this->text;
    }

    public function setText($value) {
        $this->text = $value;
    }

    public function output() {
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

    public function enableEvents() {
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
    }
}