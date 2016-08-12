<?php

namespace yariksav\actives\view\plugins;

use yii;
use yii\base;
use yii\base\Object;
use yariksav\actives\base\StageCollection;
use yariksav\actives\base\Component;


class PluginMgr extends StageCollection
{

    public static $builtInPlugins = [
        'pagination' => 'yariksav\actives\view\plugins\Pagination',
        'sort' =>       'yariksav\actives\view\plugins\SortPlugin',
        'search' =>     'yariksav\actives\view\plugins\SearchPlugin',
        'refresh' =>    'yariksav\actives\view\plugins\RefreshPlugin',
        'loader' =>     'yariksav\actives\view\plugins\Plugin',
        'refresh' =>    'yariksav\actives\view\plugins\Plugin',
        'manage' =>     'yariksav\actives\view\plugins\Plugin',
        'filter' =>     'yariksav\actives\view\plugins\FilterPlugin',
        'export' =>     'yariksav\actives\view\plugins\Plugin',

        'columnMenu' => 'yariksav\actives\view\plugins\ColumnMenuPlugin',
        'contextMenu' => 'yariksav\actives\view\plugins\ContextMenuPlugin',
    ];

    protected function createObject($params) {
        if (empty($params['name']) || is_int($params['name'])) {
            throw new \Exception('Please get the name for control');
        }

        if (empty($params['type']) && empty($params['class'])) {
            $params['type'] = $params['name'];
        }

        if (isset($params['type'])) {
            $type = $params['type'];
            if (isset(static::$builtInPlugins[$type])) {
                $type = static::$builtInPlugins[$type];
            }
            if (is_array($type)) {
                $params = array_merge($type, $params);
            } else {
                $params['class'] = $type;
            }
        }

        return Yii::createObject($params, [
            $this->owner
        ]);
    }

    public function build() {
        $plugins = [];
        if ($this->_collection) foreach ($this->_collection as $key => $plugin) {
            if ($plugin->visible) {
                $plugins[$key] = $plugin->build();
            }
        }
        return $plugins ? : null;
    }

    public function setProvider($provider) {
        if ($this->_collection) {
            foreach ($this->_collection as $key => $plugin) {
                $plugin->setProvider($provider);
            }
        }
    }

//    public function setInit($values) {
//        if ($values) foreach ($values as $key => $value) {
//            if (isset($this->_collection[$key])) {
//                $plugin = $this->_collection[$key];
//                $plugin->value = $value;
//            }
//        }
//    }
//    public function values($values) {
//        if ($values) foreach ($values as $key => $value) {
//            if (isset($this->_collection[$key])) {
//                $plugin = $this->_collection[$key];
//                $plugin->value = $value;
//            }
//        }
//    }
}