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
        'Loader' =>     'yariksav\actives\view\plugins\Plugin',
        'Refresh' =>    'yariksav\actives\view\plugins\Plugin',

        'Pagination' => 'yariksav\actives\view\plugins\Pagination',
        'InfinityScroll' => 'yariksav\actives\view\plugins\InfinityScroll',
        'SummaryInfo' => 'yariksav\actives\view\plugins\Plugin',

        'Sort' =>       'yariksav\actives\view\plugins\Sort',
        'Search' =>     'yariksav\actives\view\plugins\Search',
        'Filter' =>     'yariksav\actives\view\plugins\Filter',
        'Export' =>     'yariksav\actives\view\plugins\Export',

        'Manage' => [
            'class' => 'yariksav\actives\view\plugins\BaseMenu',
            'cmp' => 'Manage'
        ],
        'PopoverMenu' => [
            'class' => 'yariksav\actives\view\plugins\ItemMenu',
            'cmp' => 'PopoverMenu'
        ],
        'ColumnMenu' => [
            'class' => 'yariksav\actives\view\plugins\ItemMenu',
            'cmp' => 'ColumnMenu'
        ],
        'ContextMenu' => [
            'class'=>'yariksav\actives\view\plugins\ItemMenu',
            'cmp' => 'ContextMenu'
        ],
    ];

    protected function createItem($params, $name = null) {
        if (!$name || is_int($name)) {
            throw new \Exception('Please get the name for control');
        }

        if (empty($params['type']) && empty($params['class'])) {
            $params['type'] = $name;
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
        unset($params['type']);
        return parent::createItem($params, $name);
    }

    public function build() {
        $plugins = [];
        foreach ($this as $key => $plugin) {
            if ($plugin->visible && $plugin->hasPermissions()) {
                $plugins[$key] = $plugin->build();
            }
        }
        return $plugins ? : null;
    }

    public function buildItem($model) {
        $plugins = [];
        foreach ($this as $name=>$plugin) {
            if ($plugin->visible && $plugin instanceof ItemMenu) { //is_callable($plugin->renderItem)
                $plugins[$name] = call_user_func($plugin->buildItem, $model);
            }
        }
        return $plugins ? : null;
    }

}