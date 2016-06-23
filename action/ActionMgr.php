<?php

namespace yariksav\actives\action;

use yii;
use yii\base;
use yii\base\Object;
use yariksav\actives\base\CollectionMgr;
use yariksav\actives\base\Component;


class ActionMgr extends CollectionMgr
{

    protected function createObject($params) {
        $name = $params['name'];
        if (empty($name) || is_int($name)) {
            throw new \Exception('Please get the name for action');
        }

        if (empty($params['class'])) {
            $params['class'] = Action::className();
        }
        $obj = Yii::createObject($params, [$this->owner]);
        // Disable previous action events if exists
        if (isset($this->_collection[$name]) && $this->_collection[$name] instanceof DialogAction) {
            $this->_collection[$name]->disableEvents();
        }
        return $obj;
    }

    public function load(array $newActions) {
        // clear previous
       /* $withParents = array_search('*', $newActions) >= 0;
        if (!$withParents && $this->_collection) {
            foreach ($this->_collection as $action) {
                $action->disableEvents();
            }
        }*/

        parent::load($newActions);
        return $this->_collection;
    }

    public function loadIfNotExists(array $newActions){
        if (!$newActions || !is_array($newActions))
            return;
        $actionsForLoad = [];
        foreach ($newActions as $name => $action) {
            if (empty($this->_collection[$name])) {
                $actionsForLoad[$name] = $action;
            }
        }
        if ($actionsForLoad) {
            $actionsForLoad[0] = '*';
            $this->load($actionsForLoad);
        }
    }

    public function links() {
        $actions = [];
        foreach ($this->_collection as $key => $action) {
            if ($action->visible && $action->type) {
                $actions[$key] = $action->output();
            }
        }
        return $actions ? : null;
    }

    public function setCurrent($value) {
        parent::setCurrent($value);
        $current = $this->getCurrent();
        if ($current)
            $current->enableEvents();
    }
}