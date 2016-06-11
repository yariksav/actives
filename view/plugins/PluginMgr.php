<?php

namespace yariksav\actives\view\plugins;

use yii;
use yii\base;
use yii\base\Object;
use yariksav\actives\base\CollectionMgr;
use yariksav\actives\base\Component;


class PluginMgr extends CollectionMgr
{

    public static $builtInPlugins = [
        'sort' => 'yariksav\actives\view\plugins\SortPlugin',
        'search' => 'yariksav\actives\view\plugins\SearchPlugin',
        'refresh' => 'yariksav\actives\view\plugins\RefreshPlugin',
        'pager' => 'yariksav\actives\view\plugins\PagerPlugin',
        'columnMenu' => 'yariksav\actives\view\plugins\ColumnMenuPlugin',
        'contextMenu' => 'yariksav\actives\view\plugins\ContextMenuPlugin',
    ];

    protected function createObject($params) {
        if (empty($params['name']) || is_int($params['name'])) {
            throw new \Exception('Please get the name for control');
        }

        if (empty($params['type']) && empty($params['class'])) {
            $params['type'] = $params['name'];//Plugin::className();
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
            $this->_activeObject
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

}