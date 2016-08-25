<?php

namespace yariksav\actives\view\plugins;

use yii;
use yii\base;
use yii\base\Object;
use yariksav\actives\base\Collection;
use yariksav\actives\base\Component;


class PluginMgr extends Collection
{

    public static $builtInPlugins = [
        'loader' =>     'yariksav\actives\view\plugins\Plugin',
        'refresh' =>    'yariksav\actives\view\plugins\Plugin',

        'pagination' => 'yariksav\actives\view\plugins\Pagination',
        'sort' =>       'yariksav\actives\view\plugins\Sort',
        'search' =>     'yariksav\actives\view\plugins\Search',
        'filter' =>     'yariksav\actives\view\plugins\Filter',
        'export' =>     'yariksav\actives\view\plugins\Export',

        'manage' =>     'yariksav\actives\view\plugins\BaseMenu',
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
}