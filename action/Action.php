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

    private $_activeObject;
    private $title;

    function __construct($activeObject, $config = []) {
        parent::__construct($config);
        $this->_activeObject = $activeObject;
    }

    public function getVisible() {
        if (is_callable($this->_visible)) {
            $this->_visible = call_user_func_array($this->_visible, ['activeObject' => $this->_activeObject]);
        }
        return $this->_visible && $this->hasPermissions();
    }

    public function getTitle() {
        if (!$this->title) {
            return Yii::t('app', ucfirst($this->name));
        }
        return $this->title;
    }

    public function setTitle($value) {
        $this->title = $value;
    }

    public function output() {
        $res = [
            'type' => $this->type,
            'text' => $this->getTitle()
        ];
        if ($this->icon) {
            $res['icon'] = $this->icon;
        }
        if ($this->iconright) {
            $res['iconright'] = $this->iconright;
        }
        return $res;
    }

    public function enableEvents() {
        $this->disableEvents();
        if (!$this->visible) {
            return;
        }
        if ($this->on) {
            $this->_activeObject->on($this->name, $this->on);
        }
        if ($this->after) {
            $this->_activeObject->on('after' . $this->name, $this->after);
        }
        if ($this->before) {
            $this->_activeObject->on('before' . $this->name, $this->before);
        }
    }

    public function disableEvents() {
        if ($this->on) {
            $this->_activeObject->off($this->name, $this->on);
        }
        if ($this->after) {
            $this->_activeObject->off('after' . $this->name, $this->after);
        }
        if ($this->before) {
            $this->_activeObject->off('before' . $this->name, $this->before);
        }
    }
}